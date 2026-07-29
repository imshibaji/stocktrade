<?php

namespace App\Controllers;

class ApiDocs extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'API Playground - StockTrade Tips',
        ];

        return view('templates/header', $data)
            . view('api/index', $data)
            . view('templates/footer');
    }
}
