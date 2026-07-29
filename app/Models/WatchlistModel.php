<?php

namespace App\Models;

use CodeIgniter\Model;

class WatchlistModel extends Model
{
    protected $table = 'watchlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'stock_id', 'bucket_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    public function getUserWatchlist(int $userId)
    {
        return $this->select('watchlist.*, stocks.symbol, stocks.name, stocks.sector, stocks.current_price, stocks.previous_close, stocks.pe_ratio')
            ->join('stocks', 'stocks.id = watchlist.stock_id')
            ->where('watchlist.user_id', $userId)
            ->findAll();
    }

    public function getByBucket(int $userId, ?int $bucketId)
    {
        $q = $this->select('watchlist.*, stocks.symbol, stocks.name, stocks.sector, stocks.current_price, stocks.previous_close, stocks.pe_ratio, stocks.market_cap, stocks.dividend_yield, stocks.beta, stocks.week_52_high, stocks.week_52_low')
            ->join('stocks', 'stocks.id = watchlist.stock_id')
            ->where('watchlist.user_id', $userId);
        if ($bucketId === null) {
            $q->where('watchlist.bucket_id IS NULL');
        } else {
            $q->where('watchlist.bucket_id', $bucketId);
        }
        return $q->findAll();
    }

    public function isWatched(int $userId, int $stockId): bool
    {
        return $this->where('user_id', $userId)
            ->where('stock_id', $stockId)
            ->countAllResults() > 0;
    }

    public function getWatchlistCount(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    public function setBucket(int $id, ?int $bucketId): void
    {
        $this->update($id, ['bucket_id' => $bucketId]);
    }
}
