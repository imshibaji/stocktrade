<?php

namespace App\Controllers;

use App\Models\WatchlistModel;
use App\Models\WatchlistBucketModel;
use App\Models\PredictionModel;

class Watchlist extends BaseController
{
    public function index(): string
    {
        $userId = current_user_id();
        $watchlistModel = new WatchlistModel();
        $bucketModel = new WatchlistBucketModel();
        $predictionModel = new PredictionModel();

        $buckets = $bucketModel->getUserBuckets($userId);
        $uncategorized = $watchlistModel->getByBucket($userId, null);

        $stocksByBucket = [];
        foreach ($buckets as $b) {
            $stocksByBucket[$b['id']] = [
                'bucket' => $b,
                'stocks' => $watchlistModel->getByBucket($userId, $b['id']),
            ];
        }

        $allStocks = array_merge($uncategorized, ...array_column($stocksByBucket, 'stocks'));
        $stockIds = array_map(fn($s) => (int) $s['stock_id'], $allStocks);

        $predictions = [];
        if (!empty($stockIds)) {
            $allPreds = $predictionModel->getPredictionsForStocks($stockIds, 30);
            foreach ($allPreds as $p) {
                $sid = (int) $p['stock_id'];
                if (!isset($predictions[$sid])) {
                    $predictions[$sid] = ['low' => INF, 'high' => -INF];
                }
                $price = (float) $p['predicted_price'];
                if ($price < $predictions[$sid]['low']) $predictions[$sid]['low'] = $price;
                if ($price > $predictions[$sid]['high']) $predictions[$sid]['high'] = $price;
            }
            foreach ($predictions as &$r) {
                if ($r['low'] === INF) $r['low'] = 0;
                if ($r['high'] === -INF) $r['high'] = 0;
            }
            unset($r);
        }

        $data = [
            'title'            => 'My Watchlist - StockTrade Tips',
            'stocks'           => $allStocks,
            'uncategorized'    => $uncategorized,
            'stocksByBucket'   => $stocksByBucket,
            'buckets'          => $buckets,
            'predictions'      => $predictions,
        ];

        return view('templates/header', $data)
            . view('watchlist/index', $data)
            . view('templates/footer');
    }

    public function add($stockId)
    {
        $userId = current_user_id();
        $watchlistModel = new WatchlistModel();
        $sid = (int) $stockId;

        if ($watchlistModel->isWatched($userId, $sid)) {
            return redirect()->back()->with('info', 'Stock already in your watchlist.');
        }

        $bucketId = $this->request->getPost('bucket_id');
        $watchlistModel->insert([
            'user_id'   => $userId,
            'stock_id'  => $sid,
            'bucket_id' => $bucketId ? (int) $bucketId : null,
        ]);

        return redirect()->back()->with('success', 'Stock added to watchlist.');
    }

    public function remove($stockId)
    {
        $userId = current_user_id();
        $db = \Config\Database::connect();
        $db->table('watchlist')
            ->where('user_id', $userId)
            ->where('stock_id', (int) $stockId)
            ->delete();
        return redirect()->back()->with('success', 'Stock removed from watchlist.');
    }

    public function toggle($stockId)
    {
        $userId = current_user_id();
        $watchlistModel = new WatchlistModel();
        $sid = (int) $stockId;

        if ($watchlistModel->isWatched($userId, $sid)) {
            $watchlistModel->where('user_id', $userId)
                ->where('stock_id', $sid)
                ->delete();
            return $this->response->setJSON(['status' => 'removed', 'watched' => false]);
        }

        $watchlistModel->insert([
            'user_id'  => $userId,
            'stock_id' => $sid,
        ]);
        return $this->response->setJSON(['status' => 'added', 'watched' => true]);
    }

    public function createBucket()
    {
        $userId = current_user_id();
        $name = trim($this->request->getPost('name'));
        if ($name === '') {
            return redirect()->back()->with('error', 'Bucket name is required.');
        }
        $model = new WatchlistBucketModel();
        $model->insert(['user_id' => $userId, 'name' => $name]);
        return redirect()->to('/watchlist')->with('success', 'Bucket "' . esc($name) . '" created.');
    }

    public function deleteBucket($id)
    {
        $userId = current_user_id();
        $model = new WatchlistBucketModel();
        $bucket = $model->where('id', (int) $id)->where('user_id', $userId)->first();
        if (!$bucket) {
            return redirect()->back()->with('error', 'Bucket not found.');
        }
        $db = \Config\Database::connect();
        $db->table('watchlist')->where('bucket_id', (int) $id)->update(['bucket_id' => null]);
        $model->delete($id);
        return redirect()->to('/watchlist')->with('success', 'Bucket "' . esc($bucket['name']) . '" deleted.');
    }

    public function moveToBucket()
    {
        $userId = current_user_id();
        $watchlistId = (int) $this->request->getPost('watchlist_id');
        $bucketId = $this->request->getPost('bucket_id');
        $bucketId = $bucketId ? (int) $bucketId : null;

        $model = new WatchlistModel();
        $entry = $model->where('id', $watchlistId)->where('user_id', $userId)->first();
        if (!$entry) {
            return $this->response->setJSON(['error' => 'Not found']);
        }
        $model->setBucket($watchlistId, $bucketId);
        return $this->response->setJSON(['success' => true]);
    }
}
