<?php

namespace App\Controllers;

use App\Libraries\YahooFinanceService;
use App\Models\StockModel;
use App\Models\InvestmentModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $userId = current_user_id();
        $stockModel = new StockModel();
        $investmentModel = new InvestmentModel();
        $user = current_user();

        $taxRates = [
            'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
            'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
            'fee_rates' => $user,
        ];

        $activeInvestments = $investmentModel->getActiveInvestments($userId);
        $portfolio = $investmentModel->getPortfolioSummary($userId, $taxRates);

        $investmentDetails = [];
        foreach ($activeInvestments as $inv) {
            $pl = $investmentModel->calculateProfitLoss($inv, $taxRates);
            $pl['stock_id'] = $inv['stock_id'];
            $pl['symbol'] = $inv['symbol'];
            $pl['name'] = $inv['name'];
            $pl['shares'] = $inv['shares'];
            $pl['buy_price'] = $inv['buy_price'];
            $pl['current_price'] = $inv['current_price'];
            $pl['total_invested'] = $inv['total_invested'];
            $pl['buy_date'] = $inv['buy_date'];
            $investmentDetails[] = $pl;
        }

        $allStocks = $stockModel->orderBy('symbol', 'ASC')->findAll();
        $allStocks = (new YahooFinanceService())->enrichStocks($allStocks);

        $data = [
            'title'              => 'Dashboard - StockTrade Tips',
            'activeInvestments'  => $activeInvestments,
            'investmentDetails'  => $investmentDetails,
            'portfolio'          => $portfolio,
            'allStocks'          => $allStocks,
            'taxRates'           => $taxRates,
        ];

        return view('templates/header', $data)
            . view('dashboard', $data)
            . view('templates/footer');
    }
}
