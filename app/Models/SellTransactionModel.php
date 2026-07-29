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
        return $this->where('user_id', $userId)
            ->orderBy('sell_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
