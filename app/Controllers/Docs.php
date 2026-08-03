<?php

namespace App\Controllers;

class Docs extends BaseController
{
    public function user(): string
    {
        $data = ['title' => 'User Guide - StockTrade Tips'];

        return view('templates/header', $data)
            . view('docs/user', $data)
            . view('templates/footer');
    }

    public function developer(): string
    {
        $data = ['title' => 'Developer Documentation - StockTrade Tips'];

        return view('templates/header', $data)
            . view('docs/developer', $data)
            . view('templates/footer');
    }
}
