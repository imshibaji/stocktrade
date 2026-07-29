<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestmentModel extends Model
{
    protected $table = 'investments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'stock_id', 'shares', 'buy_price', 'total_invested',
        'buy_date', 'sell_price', 'sell_date', 'status'
    ];
    protected $useTimestamps = true;

    public function getUserInvestments(int $userId)
    {
        return $this->select('investments.*, stocks.symbol, stocks.name, stocks.current_price')
            ->join('stocks', 'stocks.id = investments.stock_id')
            ->where('investments.user_id', $userId)
            ->orderBy('investments.created_at', 'DESC')
            ->findAll();
    }

    public function getActiveInvestments(int $userId)
    {
        return $this->select('investments.*, stocks.symbol, stocks.name, stocks.current_price')
            ->join('stocks', 'stocks.id = investments.stock_id')
            ->where('investments.user_id', $userId)
            ->where('investments.status', 'active')
            ->findAll();
    }

    public function calculateProfitLoss(array $investment, array $taxRates = []): array
    {
        $stcgRate = (float) ($taxRates['stcg_rate'] ?? 0.15);
        $ltcgRate = (float) ($taxRates['ltcg_rate'] ?? 0.10);
        $feeRates = $taxRates['fee_rates'] ?? [];

        $currentPrice = (float) $investment['current_price'];
        $buyPrice = (float) $investment['buy_price'];
        $shares = (float) $investment['shares'];
        $totalInvested = (float) $investment['total_invested'];

        $currentValue = $shares * $currentPrice;
        $buyFees = calc_transaction_fees($totalInvested, $feeRates);
        $sellFees = calc_transaction_fees($currentValue, $feeRates);
        $totalFees = $buyFees['total'] + $sellFees['total'];

        $grossProfit = $currentValue - $totalInvested - $totalFees;
        $grossProfitPercent = $totalInvested > 0 ? ($grossProfit / $totalInvested) * 100 : 0;

        $stcgTax = 0;
        $ltcgTax = 0;
        $buyDate = strtotime($investment['buy_date']);
        $heldDays = (time() - $buyDate) / 86400;

        if ($grossProfit > 0) {
            if ($heldDays < 365) {
                $stcgTax = $grossProfit * $stcgRate;
            } else {
                $exemption = 100000;
                $taxableLtcg = max(0, $grossProfit - $exemption);
                $ltcgTax = $taxableLtcg * $ltcgRate;
            }
        }

        $totalTax = $stcgTax + $ltcgTax;
        $netProfit = $grossProfit - $totalTax;
        $stcgPct = round($stcgRate * 100, 1);
        $ltcgPct = round($ltcgRate * 100, 1);

        return [
            'total_invested'     => round($totalInvested, 2),
            'current_value'      => round($currentValue, 2),
            'buy_fees'           => $buyFees,
            'sell_fees'          => $sellFees,
            'total_fees'         => round($totalFees, 2),
            'gross_profit'       => round($grossProfit, 2),
            'gross_profit_pct'   => round($grossProfitPercent, 2),
            'stcg_tax'           => round($stcgTax, 2),
            'ltcg_tax'           => round($ltcgTax, 2),
            'total_tax'          => round($totalTax, 2),
            'net_profit'         => round($netProfit, 2),
            'held_days'          => (int) $heldDays,
            'type'               => $heldDays < 365 ? "STCG ({$stcgPct}%)" : "LTCG ({$ltcgPct}% above 1L)",
        ];
    }

    public function getPortfolioSummary(int $userId, array $taxRates = []): array
    {
        $investments = $this->getActiveInvestments($userId);
        $results = [];
        $totalInvested = 0;
        $totalCurrentValue = 0;
        $totalGrossProfit = 0;
        $totalFees = 0;
        $totalTax = 0;
        $totalNetProfit = 0;

        foreach ($investments as $inv) {
            $pl = $this->calculateProfitLoss($inv, $taxRates);
            $pl['stock'] = $inv;
            $results[] = $pl;

            $totalInvested += $pl['total_invested'];
            $totalCurrentValue += $pl['current_value'];
            $totalGrossProfit += $pl['gross_profit'];
            $totalFees += $pl['total_fees'];
            $totalTax += $pl['total_tax'];
            $totalNetProfit += $pl['net_profit'];
        }

        return [
            'investments'        => $results,
            'total_invested'     => round($totalInvested, 2),
            'total_current_value'=> round($totalCurrentValue, 2),
            'total_gross_profit' => round($totalGrossProfit, 2),
            'total_fees'         => round($totalFees, 2),
            'total_tax'          => round($totalTax, 2),
            'total_net_profit'   => round($totalNetProfit, 2),
        ];
    }
}
