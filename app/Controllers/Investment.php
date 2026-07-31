<?php

namespace App\Controllers;

use App\Models\InvestmentModel;
use App\Models\StockModel;

class Investment extends BaseController
{
    public function index(): string
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();
        $stockModel = new StockModel();

        $user = current_user();
        $taxRates = [
            'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
            'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
            'fee_rates' => [
                'brokerage_pct' => $user['brokerage_pct'] ?? 0,
                'stt_pct'       => $user['stt_pct'] ?? 0,
                'exchange_pct'  => $user['exchange_pct'] ?? 0,
                'gst_pct'       => $user['gst_pct'] ?? 0,
                'stamp_duty_pct'=> $user['stamp_duty_pct'] ?? 0,
                'sebi_fees'     => $user['sebi_fees'] ?? 0,
            ],
            'base_currency' => $user['base_currency'] ?? 'INR',
        ];

        $investments = $investmentModel->getUserInvestments($userId);
        $portfolio = $investmentModel->getPortfolioSummary($userId, $taxRates);
        $stocks = $stockModel->findAll();

        $stockCurrencyMap = [];
        foreach ($stocks as $s) {
            $stockCurrencyMap[$s['id']] = stock_currency($s['exchange'] ?? null);
        }
        foreach ($investments as &$inv) {
            $inv['currency'] = $stockCurrencyMap[$inv['stock_id']] ?? 'INR';
        }
        unset($inv);

        $investmentPl = [];
        foreach ($investments as $inv) {
            $invId = (int) $inv['id'];
            $investmentPl[$invId] = $investmentModel->calculateProfitLoss($inv, $taxRates);
        }

        $data = [
            'title'          => 'My Investments - StockTrade Tips',
            'investments'    => $investments,
            'investmentPl'   => $investmentPl,
            'portfolio'      => $portfolio,
            'stocks'         => $stocks,
            'taxInfo'        => tax_bracket_info($user),
            'taxRates'       => $taxRates,
            'base_currency'  => $user['base_currency'] ?? 'INR',
        ];

        return view('templates/header', $data)
            . view('investments/index', $data)
            . view('templates/footer');
    }

    public function create()
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();
        $stockModel = new StockModel();

        $stockId = (int) $this->request->getPost('stock_id');
        $inputType = $this->request->getPost('input_type') ?: 'amount';
        $amount = (float) $this->request->getPost('amount');
        $quantity = (int) $this->request->getPost('quantity');
        $buyDate = $this->request->getPost('buy_date') ?: date('Y-m-d');

        $stock = $stockModel->find($stockId);
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found.');
        }

        $buyPrice = (float) $stock['current_price'];

        if ($inputType === 'quantity') {
            $shares = $quantity;
            $totalInvested = $shares * $buyPrice;
        } else {
            $shares = (int) ($amount / $buyPrice);
            if ($shares < 1) {
                return redirect()->back()->with('error', 'Amount too low. Minimum 1 share required at ' . format_price($buyPrice) . '.');
            }
            $totalInvested = $shares * $buyPrice;
        }

        $now = date('Y-m-d H:i:s');
        $investmentModel->insert([
            'user_id'        => $userId,
            'stock_id'       => $stockId,
            'shares'         => $shares,
            'buy_price'      => $buyPrice,
            'total_invested' => $totalInvested,
            'buy_date'       => $buyDate,
            'status'         => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        return redirect()->to('/investments')->with('success', sprintf(
            'Bought %d shares of %s at %s each - Total: %s',
            $shares,
            $stock['symbol'],
            format_price($buyPrice),
            format_price($totalInvested)
        ));
    }

    public function sellForm($id): string
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();

        $investment = $investmentModel->select('investments.*, stocks.symbol, stocks.name, stocks.current_price, stocks.exchange, stocks.exchange_display')
            ->join('stocks', 'stocks.id = investments.stock_id')
            ->where('investments.id', (int) $id)
            ->where('investments.user_id', $userId)
            ->first();

        if (!$investment) {
            return redirect()->to('/investments')->with('error', 'Investment not found.');
        }

        $user = current_user();
        $taxRates = [
            'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
            'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
            'fee_rates' => [
                'brokerage_pct' => $user['brokerage_pct'] ?? 0,
                'stt_pct'       => $user['stt_pct'] ?? 0,
                'exchange_pct'  => $user['exchange_pct'] ?? 0,
                'gst_pct'       => $user['gst_pct'] ?? 0,
                'stamp_duty_pct'=> $user['stamp_duty_pct'] ?? 0,
                'sebi_fees'     => $user['sebi_fees'] ?? 0,
            ],
        ];

        $pl = $investmentModel->calculateProfitLoss($investment, $taxRates);

        $investment['currency'] = stock_currency($investment['exchange'] ?? null);

        $data = [
            'title'      => 'Sell Investment - StockTrade Tips',
            'investment' => $investment,
            'pl'         => $pl,
        ];

        return view('templates/header', $data)
            . view('investments/sell', $data)
            . view('templates/footer');
    }

    public function sell($id)
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();

        $investment = $investmentModel->select('investments.*, stocks.symbol')
            ->join('stocks', 'stocks.id = investments.stock_id')
            ->where('investments.id', (int) $id)
            ->where('investments.user_id', $userId)
            ->first();

        if (!$investment) {
            return redirect()->to('/investments')->with('error', 'Investment not found.');
        }

        $sellPrice = (float) $this->request->getPost('sell_price');
        $sellQty = (int) $this->request->getPost('quantity');
        $heldShares = (int) $investment['shares'];

        if ($sellQty < 1) {
            return redirect()->back()->with('error', 'Invalid quantity.');
        }

        if ($sellQty > $heldShares) {
            return redirect()->back()->with('error', 'Cannot sell more shares than you hold.');
        }

        if ($sellQty >= $heldShares) {
            $investmentModel->update($id, [
                'sell_price' => $sellPrice,
                'sell_date'  => date('Y-m-d'),
                'status'     => 'sold',
            ]);
        } else {
            $ratio = ($heldShares - $sellQty) / $heldShares;
            $investmentModel->update($id, [
                'shares'         => $heldShares - $sellQty,
                'total_invested' => (float) $investment['total_invested'] * $ratio,
            ]);
        }

        $costBasis = $sellQty * (float) $investment['buy_price'];
        $saleValue = $sellQty * $sellPrice;
        $grossProfit = $saleValue - $costBasis;
        $profitSign = $grossProfit >= 0 ? '+' : '';

        $user = current_user();
        $feeRates = [
            'brokerage_pct' => $user['brokerage_pct'] ?? 0,
            'stt_pct'       => $user['stt_pct'] ?? 0,
            'exchange_pct'  => $user['exchange_pct'] ?? 0,
            'gst_pct'       => $user['gst_pct'] ?? 0,
            'stamp_duty_pct'=> $user['stamp_duty_pct'] ?? 0,
            'sebi_fees'     => $user['sebi_fees'] ?? 0,
        ];
        $sellFees = calc_transaction_fees($saleValue, $feeRates);
        $totalFees = $sellFees['total'];

        $buyDate = strtotime($investment['buy_date']);
        $heldDays = (time() - $buyDate) / 86400;
        $stcgRate = (float) ($user['stcg_rate'] ?? 15) / 100;
        $ltcgRate = (float) ($user['ltcg_rate'] ?? 10) / 100;

        $taxAmount = 0;
        if ($grossProfit > 0) {
            if ($heldDays < 365) {
                $taxAmount = $grossProfit * $stcgRate;
            } else {
                $exemption = 100000;
                $taxableLtcg = max(0, $grossProfit - $exemption);
                $taxAmount = $taxableLtcg * $ltcgRate;
            }
        }

        $netProfit = $grossProfit - $totalFees - $taxAmount;

        $txModel = new \App\Models\SellTransactionModel();
        $txModel->insert([
            'user_id'         => $userId,
            'stock_id'        => (int) $investment['stock_id'],
            'investment_id'   => (int) $id,
            'symbol'          => $investment['symbol'],
            'shares'          => $sellQty,
            'buy_price'       => (float) $investment['buy_price'],
            'sell_price'      => $sellPrice,
            'cost_basis'      => $costBasis,
            'sale_value'      => $saleValue,
            'profit_loss'     => $grossProfit,
            'total_fees'      => round($totalFees, 2),
            'total_tax'       => round($taxAmount, 2),
            'net_profit_loss' => round($netProfit, 2),
            'buy_date'        => $investment['buy_date'],
            'sell_date'       => date('Y-m-d'),
        ]);

        return redirect()->to('/investments')->with('success', sprintf(
            'Sold %d shares of %s at %s — Gross: %s%s | Net: %s%s',
            $sellQty,
            $investment['symbol'],
            format_price($sellPrice),
            $profitSign,
            format_price($grossProfit),
            $netProfit >= 0 ? '+' : '',
            format_price($netProfit)
        ));
    }

    public function history(): string
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();
        $user = current_user();

        $taxRates = [
            'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
            'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
            'fee_rates' => [
                'brokerage_pct' => $user['brokerage_pct'] ?? 0,
                'stt_pct'       => $user['stt_pct'] ?? 0,
                'exchange_pct'  => $user['exchange_pct'] ?? 0,
                'gst_pct'       => $user['gst_pct'] ?? 0,
                'stamp_duty_pct'=> $user['stamp_duty_pct'] ?? 0,
                'sebi_fees'     => $user['sebi_fees'] ?? 0,
            ],
        ];

        $investments = $investmentModel->getUserInvestments($userId);

        $investmentPl = [];
        foreach ($investments as $inv) {
            $invId = (int) $inv['id'];
            if ($inv['status'] === 'active') {
                $investmentPl[$invId] = $investmentModel->calculateProfitLoss($inv, $taxRates);
            }
        }

        $data = [
            'title'         => 'Investment History - StockTrade Tips',
            'investments'   => $investments,
            'investmentPl'  => $investmentPl,
            'taxRates'      => $taxRates,
        ];

        return view('templates/header', $data)
            . view('investments/history', $data)
            . view('templates/footer');
    }

    public function portfolio(): string
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();
        $stockModel = new StockModel();
        $user = current_user();

        $taxRates = [
            'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
            'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
            'fee_rates' => [
                'brokerage_pct' => $user['brokerage_pct'] ?? 0,
                'stt_pct'       => $user['stt_pct'] ?? 0,
                'exchange_pct'  => $user['exchange_pct'] ?? 0,
                'gst_pct'       => $user['gst_pct'] ?? 0,
                'stamp_duty_pct'=> $user['stamp_duty_pct'] ?? 0,
                'sebi_fees'     => $user['sebi_fees'] ?? 0,
            ],
        ];

        $portfolio = $investmentModel->getPortfolioSummary($userId, $taxRates);

        $stocks = $stockModel->findAll();

        $stockCurrencyMap = [];
        foreach ($stocks as $s) {
            $stockCurrencyMap[$s['id']] = stock_currency($s['exchange'] ?? null);
        }
        $portfolio['base_currency'] = $user['base_currency'] ?? 'INR';

        $data = [
            'title'     => 'Portfolio Summary - StockTrade Tips',
            'portfolio' => $portfolio,
            'taxInfo'   => tax_bracket_info($user),
            'taxRates'  => $taxRates,
        ];

        return view('templates/header', $data)
            . view('investments/portfolio', $data)
            . view('templates/footer');
    }

    public function editForm($id): string
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();
        $investment = $investmentModel
            ->select('investments.*, stocks.symbol, stocks.name')
            ->join('stocks', 'stocks.id = investments.stock_id')
            ->where('investments.id', (int) $id)
            ->where('investments.user_id', $userId)
            ->first();

        if (!$investment) {
            return redirect()->to('/investments')->with('error', 'Investment not found.');
        }

        $data = [
            'title'      => 'Edit Investment - StockTrade Tips',
            'investment' => $investment,
        ];

        return view('templates/header', $data)
            . view('investments/edit', $data)
            . view('templates/footer');
    }

    public function update($id)
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();

        $investment = $investmentModel->where('id', (int) $id)
            ->where('user_id', $userId)
            ->first();

        if (!$investment) {
            return redirect()->to('/investments')->with('error', 'Investment not found.');
        }

        $shares = (int) $this->request->getPost('shares');
        $buyPrice = (float) $this->request->getPost('buy_price');
        $buyDate = $this->request->getPost('buy_date') ?: $investment['buy_date'];

        $investmentModel->update($id, [
            'shares'         => $shares,
            'buy_price'      => $buyPrice,
            'total_invested' => $shares * $buyPrice,
            'buy_date'       => $buyDate,
        ]);

        return redirect()->to('/investments')->with('success', 'Investment updated successfully.');
    }

    public function delete($id)
    {
        $userId = current_user_id();
        $investmentModel = new InvestmentModel();

        $investment = $investmentModel->where('id', (int) $id)
            ->where('user_id', $userId)
            ->first();

        if (!$investment) {
            return redirect()->to('/investments')->with('error', 'Investment not found.');
        }

        $investmentModel->delete($id);

        return redirect()->to('/investments')->with('success', 'Investment deleted successfully.');
    }

    public function transactions(): string
    {
        $userId = current_user_id();
        $txModel = new \App\Models\SellTransactionModel();
        $transactions = $txModel->getUserTransactions($userId);
        $user = current_user();
        $baseCurrency = $user['base_currency'] ?? 'INR';

        $totalGross = 0;
        $totalSellValue = 0;
        $totalCost = 0;
        $totalFees = 0;
        $totalTax = 0;
        $totalNet = 0;
        foreach ($transactions as $tx) {
            $totalGross += (float) $tx['profit_loss'];
            $totalSellValue += (float) $tx['sale_value'];
            $totalCost += (float) $tx['cost_basis'];
            $totalFees += (float) ($tx['total_fees'] ?? 0);
            $totalTax += (float) ($tx['total_tax'] ?? 0);
            $totalNet += (float) ($tx['net_profit_loss'] ?? 0);
        }

        $data = [
            'title'         => 'Transaction History - StockTrade Tips',
            'transactions'  => $transactions,
            'totalGross'    => $totalGross,
            'totalSellValue' => $totalSellValue,
            'totalCost'     => $totalCost,
            'totalFees'     => $totalFees,
            'totalTax'      => $totalTax,
            'totalNet'      => $totalNet,
            'baseCurrency'  => $baseCurrency,
        ];

        return view('templates/header', $data)
            . view('investments/transactions', $data)
            . view('templates/footer');
    }
}
