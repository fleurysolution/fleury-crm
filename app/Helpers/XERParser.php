<?php

namespace App\Helpers;

/**
 * XERParser parses Primavera P6 .xer files.
 */
class XERParser
{
    /**
     * Parses an XER file content into a structured array of tables.
     */
    public static function parse(string $content): array
    {
        $lines = explode("\n", $content);
        $tables = [];
        $currentTable = null;
        $fields = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode("\t", $line);
            $prefix = $parts[0];

            if ($prefix === '%T') {
                $currentTable = $parts[1];
                $tables[$currentTable] = [];
            } elseif ($prefix === '%F') {
                $fields = array_slice($parts, 1);
            } elseif ($prefix === '%R') {
                if (!$currentTable) continue;
                $record = [];
                foreach (array_slice($parts, 1) as $index => $value) {
                    if (isset($fields[$index])) {
                        $record[$fields[$index]] = $value;
                    }
                }
                $tables[$currentTable][] = $record;
            }
        }

        return $tables;
    }

    /**
     * Maps XER tables to our internal system structure for a specific project.
     */
    public static function mapToSystem(int $projectId, array $tables): array
    {
        $results = [
            'tasks' => [],
            'dependencies' => [],
            'wbs' => []
        ];

        // 1. Map WBS (PROJWBS)
        $wbsMap = [];
        foreach ($tables['PROJWBS'] ?? [] as $row) {
            $wbsMap[$row['wbs_id']] = $row;
            $results['wbs'][] = [
                'p6_id' => $row['wbs_id'],
                'project_id' => $projectId,
                'title' => $row['wbs_name'],
                'parent_p6_id' => $row['parent_wbs_id'] ?: null,
                'sort_order' => (int)$row['seq_num']
            ];
        }

        // 2. Map Tasks (TASK)
        $taskMap = [];
        foreach ($tables['TASK'] ?? [] as $row) {
            $results['tasks'][] = [
                'p6_id' => $row['task_id'],
                'project_id' => $projectId,
                'activity_id' => $row['task_code'],
                'title' => $row['task_name'],
                'status' => $row['status_code'] === 'TK_Complete' ? 'done' : ($row['status_code'] === 'TK_Active' ? 'in_progress' : 'to_do'),
                'start_date' => $row['target_start_date'] ?: ($row['expect_end_date'] ?: null),
                'due_date' => $row['target_end_date'] ?: null,
                'original_duration' => (int)$row['target_durs_cnt'],
                'is_milestone' => ($row['task_type'] === 'TT_FinMile' || $row['task_type'] === 'TT_StartMile') ? 1 : 0,
                'wbs_p6_id' => $row['wbs_id']
            ];
        }

        // 3. Map Dependencies (TASKPRED)
        foreach ($tables['TASKPRED'] ?? [] as $row) {
            $results['dependencies'][] = [
                'task_p6_id' => $row['task_id'],
                'pred_task_p6_id' => $row['pred_task_id'],
                'type' => str_replace('PR_', '', $row['pred_type']), // FS, SS, FF, SF
                'lag' => (float)$row['lag_cnt']
            ];
        }

        return $results;
    }
}
