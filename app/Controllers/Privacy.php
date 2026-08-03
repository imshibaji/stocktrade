<?php

namespace App\Controllers;

class Privacy extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'Privacy Policy - StockTrade Tips'];

        return view('templates/header', $data)
            . view('privacy', $data)
            . view('templates/footer');
    }
}
