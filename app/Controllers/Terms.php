<?php

namespace App\Controllers;

class Terms extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'Terms and Conditions - StockTrade Tips'];

        return view('templates/header', $data)
            . view('terms', $data)
            . view('templates/footer');
    }
}
