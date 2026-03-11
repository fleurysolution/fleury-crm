<?php

namespace App\Controllers;

class Subscription extends BaseController
{
    public function locked()
    {
        $data = [
            'title' => 'Subscription Required · BPMS247'
        ];
        return view('errors/html/subscription_locked', $data);
    }
}
