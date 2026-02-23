<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FsPagesModel;

class ImportPcmPages extends BaseCommand
{
    protected $group       = 'FS';
    protected $name        = 'fs:import-pcm-pages';
    protected $description = 'Import legacy pcm_pages into fs_pages (convert URL slugs into proper slugs).';

    public function run(array $params)
    {
        $db = db_connect();

        if (! $db->tableExists('pcm_pages')) {
            CLI::error('pcm_pages table not found.');
            return;
        }

        $pages = $db->table('pcm_pages')
            ->where('deleted', 0)
            ->get()
            ->getResultObject();

        $m = model(FsPagesModel::class);
        $inserted = 0;
        $skipped  = 0;

        foreach ($pages as $p) {
            $slug = $this->normalizeSlug((string)($p->slug ?? ''));

            if ($slug === '') {
                $skipped++;
                continue;
            }

            // Map visibility flags
            $visibleTo = 'all';
            if (!empty($p->visible_to_team_members_only)) {
                $visibleTo = 'staff';
            } elseif (!empty($p->visible_to_clients_only)) {
                $visibleTo = 'client';
            }

            $data = [
                'title'             => (string)($p->title ?? 'Untitled'),
                'slug'              => $slug,
                'content'           => (string)($p->content ?? ''),
                'status'            => (string)($p->status ?? 'active'),
                'internal_only'     => !empty($p->internal_use_only) ? 1 : 0,
                'visible_to'        => $visibleTo,
                'layout_full_width' => !empty($p->full_width) ? 1 : 0,
                'hide_topbar'       => !empty($p->hide_topbar) ? 1 : 0,
            ];

            // Ensure slug unique
            $finalSlug = $this->ensureUniqueSlug($m, $data['slug']);
            $data['slug'] = $finalSlug;

            if ($m->where('slug', $data['slug'])->first()) {
                $skipped++;
                continue;
            }

            $m->insert($data);
            $inserted++;
        }

        CLI::write("Imported: {$inserted}", 'green');
        CLI::write("Skipped:  {$skipped}", 'yellow');
    }

    private function normalizeSlug(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        // If it's a URL, extract host + path
        if (preg_match('~^https?://~i', $raw)) {
            $parts = parse_url($raw);
            $host  = $parts['host'] ?? '';
            $path  = $parts['path'] ?? '';
            $raw   = trim($host . $path, '/');
        }

        // Replace spaces/invalid chars
        $raw = strtolower($raw);
        $raw = preg_replace('~[^a-z0-9/_-]+~', '-', $raw);
        $raw = str_replace(['/', '_'], '-', $raw);
        $raw = preg_replace('~-+~', '-', $raw);
        $raw = trim($raw, '-');

        // If still empty, abort
        return $raw;
    }

    private function ensureUniqueSlug(FsPagesModel $m, string $slug): string
    {
        $base = $slug;
        $i = 2;

        while ($m->where('slug', $slug)->first()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}