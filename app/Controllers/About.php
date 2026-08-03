<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'            => 'About Us - StockTrade Tips',
            'metaDescription'  => 'StockTrade Tips is a free stock analysis and prediction platform offering real-time data, AI-driven 30-day forecasts, portfolio tracking, and automated STCG/LTCG tax calculations.',
            'metaKeywords'     => 'stock analysis, stock prediction, AI trading, portfolio tracker, STCG tax, LTCG tax, stock market India, free stock tools',
            'ogTitle'          => 'About StockTrade Tips — AI-Powered Stock Analysis & Prediction',
            'ogDescription'    => 'Empowering investors with data-driven insights, AI-powered predictions, and comprehensive portfolio management tools.',
            'ogImage'          => '',
            'canonical'        => site_url('/about'),
            'robots'           => 'index, follow',
            'author'           => 'Shibaji Debnath',
        ];
        return view('templates/header', $data)
            . view('about', $data)
            . view('templates/footer');
    }
}
