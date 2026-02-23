<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\FsPagesModel;

class FsPagesSeeder extends Seeder
{
    public function run()
    {
        $m = model(FsPagesModel::class);

        $rows = [
            [
                'title'             => 'Technology Solutions',
                'slug'              => 'technology-solutions',
                'content'           => '<p>Do you have an IT or technology need or request?</p><p>Let us help!</p>',
                'status'            => 'active',
                'internal_only'     => 1,
                'visible_to'        => 'client',
                'layout_full_width' => 0,
                'hide_topbar'       => 0,
            ],
            [
                'title'             => 'Human Resources Platform',
                'slug'              => 'hr-platform',
                'content'           => "Post Job and review candidate here",
                'status'            => 'active',
                'internal_only'     => 0,
                'visible_to'        => 'all',
                'layout_full_width' => 0,
                'hide_topbar'       => 0,
            ],
        ];

        foreach ($rows as $r) {
            // idempotent: skip if exists
            if (! $m->where('slug', $r['slug'])->first()) {
                $m->insert($r);
            }
        }
    }
}