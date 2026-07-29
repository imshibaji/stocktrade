<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'About Us - StockTrade Tips'];
        return view('templates/header', $data)
            . view('about', $data)
            . view('templates/footer');
    }
}
