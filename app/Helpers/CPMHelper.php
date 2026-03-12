<?php

namespace App\Helpers;

use App\Models\TaskModel;

/**
 * CPMHelper handles the Critical Path Method calculations.
 */
class CPMHelper
{
    /**
     * Recalculates the entire schedule for a project.
     * Performs forward and backward passes to determine ES, EF, LS, LF, and Float.
     */
    public static function recalculate(int $projectId): bool
    {
        $taskModel = new TaskModel();
        $projectModel = new \App\Models\ProjectModel();
        $project = $projectModel->find($projectId);
        
        $baseDate = $project['start_date'] ?? date('Y-m-d');
        $tasks = $taskModel->where('project_id', $projectId)->findAll();
        
        if (empty($tasks)) return true;

        $db = \Config\Database::connect();
        $deps = $db->table('task_dependencies')
            ->select('task_dependencies.*')
            ->join('tasks', 'tasks.id = task_dependencies.task_id')
            ->where('tasks.project_id', $projectId)
            ->get()->getResultArray();
        
        // Fetch procurement items to use as constraints
        $procurement = $db->table('project_procurement_items')
            ->where('project_id', $projectId)
            ->get()->getResultArray();
        
        $procurementConstraints = [];
        foreach ($procurement as $p) {
            if ($p['task_id'] && $p['expected_on_site']) {
                $days = (int)ceil((strtotime($p['expected_on_site']) - strtotime($baseDate)) / 86400);
                if (!isset($procurementConstraints[$p['task_id']]) || $days > $procurementConstraints[$p['task_id']]) {
                    $procurementConstraints[$p['task_id']] = $days;
                }
            }
        }

        $predecessors = [];
        $successors = [];
        foreach ($deps as $d) {
            $successors[$d['depends_on_task']][] = [
                'id' => $d['task_id'],
                'type' => $d['type'],
                'lag' => $d['lag_days'] ?? 0
            ];
            $predecessors[$d['task_id']][] = [
                'id' => $d['depends_on_task'],
                'type' => $d['type'],
                'lag' => $d['lag_days'] ?? 0
            ];
        }

        $network = [];
        foreach ($tasks as $t) {
            $network[$t['id']] = [
                'id' => $t['id'],
                'duration' => (int)($t['original_duration'] ?: 1),
                'es' => 0, 'ef' => 0, 'ls' => 0, 'lf' => 0,
                'is_milestone' => (bool)$t['is_milestone']
            ];
            if ($network[$t['id']]['is_milestone']) $network[$t['id']]['duration'] = 0;
        }

        // Forward Pass
        $resolved = [];
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($network as $id => &$node) {
                if (isset($resolved[$id])) continue;
                
                $maxEF = 0;
                $ready = true;
                foreach ($predecessors[$id] ?? [] as $p) {
                    if (!isset($resolved[$p['id']])) {
                        $ready = false;
                        break;
                    }
                    $pEF = $network[$p['id']]['ef'];
                    $finishToStart = $pEF + (int)$p['lag'];
                    if ($finishToStart > $maxEF) $maxEF = $finishToStart;
                }

                if ($ready) {
                    // Check Procurement Constraint
                    if (isset($procurementConstraints[$id])) {
                        if ($procurementConstraints[$id] > $maxEF) {
                            $maxEF = $procurementConstraints[$id];
                        }
                    }

                    $node['es'] = $maxEF;
                    $node['ef'] = $maxEF + $node['duration'];
                    $resolved[$id] = true;
                    $changed = true;
                }
            }
        }

        // Backward Pass
        $maxProjectEF = 0;
        foreach ($network as $n) if ($n['ef'] > $maxProjectEF) $maxProjectEF = $n['ef'];

        $resolvedBack = [];
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($network as $id => &$node) {
                if (isset($resolvedBack[$id])) continue;

                $minLS = $maxProjectEF;
                $ready = true;
                foreach ($successors[$id] ?? [] as $s) {
                    if (!isset($resolvedBack[$s['id']])) {
                        $ready = false;
                        break;
                    }
                    $sLS = $network[$s['id']]['ls'];
                    $startToFinish = $sLS - (int)$s['lag'];
                    if ($startToFinish < $minLS) $minLS = $startToFinish;
                }

                if ($ready) {
                    $node['lf'] = $minLS;
                    $node['ls'] = $minLS - $node['duration'];
                    $resolvedBack[$id] = true;
                    $changed = true;
                }
            }
        }

        // Save
        foreach ($network as $id => $node) {
            $float = $node['ls'] - $node['es'];
            $taskModel->update($id, [
                'early_start'  => date('Y-m-d', strtotime("+$node[es] days", strtotime($baseDate))),
                'early_finish' => date('Y-m-d', strtotime("+$node[ef] days", strtotime($baseDate))),
                'late_start'   => date('Y-m-d', strtotime("+$node[ls] days", strtotime($baseDate))),
                'late_finish'  => date('Y-m-d', strtotime("+$node[lf] days", strtotime($baseDate))),
                'total_float'  => $float,
                'is_critical'  => ($float <= 0) ? 1 : 0,
                // Update system dates to match ES/EF for now
                'start_date'   => date('Y-m-d', strtotime("+$node[es] days", strtotime($baseDate))),
                'due_date'     => date('Y-m-d', strtotime("+$node[ef] days", strtotime($baseDate))),
            ]);
        }

        return true;
    }
}
