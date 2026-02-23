<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchStructureSeeder extends Seeder
{
    public function run()
    {
        $this->call('RegionSeeder');
        $this->call('OfficeSeeder');
        $this->call('DivisionSeeder');
    }
}