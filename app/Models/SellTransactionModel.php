<?php

namespace App\Models;

use CodeIgniter\Model;

class SellTransactionModel extends Model
{
    protected $table      = 'sell_transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'stock_id', 'investment_id', 'symbol',
        'shares', 'buy_price', 'sell_price',
        'cost_basis', 'sale_value', 'profit_loss',
        'total_fees', 'total_tax', 'net_profit_loss',
        'buy_date', 'sell_date',
    ];

    protected $useTimestamps = false;

    public function getUserTransactions(int $userId): array
    {
        return $this->select('sell_transactions.*, stocks.exchange, stocks.exchange_display')
            ->join('stocks', 'stocks.id = sell_transactions.stock_id')
            ->where('sell_transactions.user_id', $userId)
            ->orderBy('sell_transactions.sell_date', 'DESC')
            ->orderBy('sell_transactions.created_at', 'DESC')
            ->findAll();
    }
}
