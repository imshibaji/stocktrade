<?php

namespace App\Controllers;

class Pricing extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'Pricing - StockTrade Tips'];

        return view('templates/header', $data)
            . view('pricing', $data)
            . view('templates/footer');
    }
}
