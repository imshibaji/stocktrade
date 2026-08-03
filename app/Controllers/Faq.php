<?php

namespace App\Controllers;

class Faq extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'FAQ - StockTrade Tips'];

        return view('templates/header', $data)
            . view('faq', $data)
            . view('templates/footer');
    }
}
