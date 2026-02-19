<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index()
    {
        return view('landing_page', [
            'title' => 'BPMS247 - Enterprise Grade CRM',
        ]);
    }
}
