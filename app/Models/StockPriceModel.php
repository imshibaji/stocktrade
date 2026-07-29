<?php

namespace App\Models;

use CodeIgniter\Model;

class StockPriceModel extends Model
{
    protected $table = 'stock_prices';
    protected $primaryKey = 'id';
    protected $allowedFields = ['stock_id', 'price_date', 'open', 'high', 'low', 'close', 'volume'];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}
