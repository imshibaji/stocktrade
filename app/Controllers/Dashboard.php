<?php

namespace App\Controllers;

use App\Libraries\YahooFinanceService;
use App\Models\StockModel;
use App\Models\InvestmentModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('currency');
        try {
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

            $baseCurrency = get_user_base_currency();

            $investmentDetails = [];
            foreach ($activeInvestments as $inv) {
                $pl = $investmentModel->calculateProfitLoss($inv, $taxRates);
                $stockCurrency = $inv['currency'] ?? 'INR';
                $pl['stock_id'] = $inv['stock_id'];
                $pl['symbol'] = $inv['symbol'];
                $pl['name'] = $inv['name'];
                $pl['shares'] = $inv['shares'];
                $pl['buy_price'] = $inv['buy_price'];
                $pl['buy_price_base'] = convert_to_base_currency((float) $inv['buy_price'], $stockCurrency);
                $pl['current_price'] = $inv['current_price'];
                $pl['current_price_base'] = convert_to_base_currency((float) $inv['current_price'], $stockCurrency);
                $pl['total_invested'] = $inv['total_invested'];
                $pl['total_invested_base'] = convert_to_base_currency((float) $inv['total_invested'], $stockCurrency);
                $pl['current_value'] = $pl['current_price'] * $inv['shares'];
                $pl['current_value_base'] = convert_to_base_currency($pl['current_value'], $stockCurrency);
                $pl['profit_loss'] = $pl['profit_loss'] ?? 0;
                $pl['profit_loss_base'] = convert_to_base_currency($pl['profit_loss'] ?? 0, $stockCurrency);
                $pl['buy_date'] = $inv['buy_date'];
                $pl['currency'] = $stockCurrency;
                $pl['base_currency'] = $baseCurrency;
                $investmentDetails[] = $pl;
            }

            $portfolio['base_currency'] = $baseCurrency;
            $portfolio['total_invested_base'] = convert_to_base_currency($portfolio['total_invested'] ?? 0, 'INR');
            $portfolio['total_current_value_base'] = convert_to_base_currency($portfolio['total_current_value'] ?? 0, 'INR');
            $portfolio['total_profit_loss_base'] = convert_to_base_currency($portfolio['total_profit_loss'] ?? 0, 'INR');
            $portfolio['total_fees_base'] = convert_to_base_currency($portfolio['total_fees'] ?? 0, 'INR');
            $portfolio['total_tax_base'] = convert_to_base_currency($portfolio['total_tax'] ?? 0, 'INR');
            $portfolio['net_profit_loss_base'] = convert_to_base_currency($portfolio['net_profit_loss'] ?? 0, 'INR');

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
        } catch (\Exception $e) {
            return view('templates/header')
                . view('error', ['error' => $e->getMessage()])
                . view('templates/footer');
        }
    }
}
