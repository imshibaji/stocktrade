<?php

namespace App\Models;

use CodeIgniter\Model;

class StockListModel extends Model
{
    protected $table = 'stock_lists';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'name', 'criteria', 'technical_criteria', 'stock_ids', 'stock_symbols', 'stock_count'];
    protected $useTimestamps = true;
    protected $returnType = 'array';

    public function emptyTable(): bool
    {
        return $this->db->table($this->table)->emptyTable();
    }
}
