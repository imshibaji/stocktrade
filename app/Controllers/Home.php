<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $stockModel = new \App\Models\StockModel();
        $data = [
            'title'        => 'Home - StockTrade Tips',
            'topStocks'    => $stockModel->orderBy('market_cap', 'DESC')->limit(6)->findAll(),
            'activeStocks' => $stockModel->orderBy('current_price', 'DESC')->limit(4)->findAll(),
        ];
        return view('templates/header', $data)
            . view('home', $data)
            . view('templates/footer');
    }
}
