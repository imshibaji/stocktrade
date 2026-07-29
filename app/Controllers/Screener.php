<?php

namespace App\Controllers;

use App\Models\StockModel;

class Screener extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Stock Screener - StockTrade Tips',
        ];
        return view('templates/header', $data)
            . view('screener/index', $data)
            . view('templates/footer');
    }

    public function run()
    {
        $stockModel = new StockModel();
        $stocks = $stockModel->findAll();

        $filters = $this->request->getGet('filters');
        $filters = $filters ? json_decode($filters, true) : [];

        $results = [];
        foreach ($stocks as $s) {
            if ($this->matchesFilters($s, $filters)) {
                $results[] = $s;
            }
        }

        return $this->response->setJSON([
            'total'   => count($results),
            'stocks'  => array_values($results),
        ]);
    }

    private function matchesFilters(array $stock, array $filters): bool
    {
        if (empty($filters)) return true;

        foreach ($filters as $f) {
            $field = $f['field'] ?? '';
            $op    = $f['op'] ?? '';
            $value = $f['value'] ?? '';
            $stockVal = $this->getFieldValue($stock, $field);

            if ($stockVal === null) return false;

            $stockVal = (float) $stockVal;
            $value = (float) $value;

            switch ($op) {
                case '>':  if (!($stockVal > $value)) return false; break;
                case '>=': if (!($stockVal >= $value)) return false; break;
                case '<':  if (!($stockVal < $value)) return false; break;
                case '<=': if (!($stockVal <= $value)) return false; break;
                case '==': if (!($stockVal == $value)) return false; break;
                default: return false;
            }
        }
        return true;
    }

    private function getFieldValue(array $stock, string $field): ?float
    {
        $map = [
            'price'          => $stock['current_price'] ?? null,
            'current_price'  => $stock['current_price'] ?? null,
            'previous_close' => $stock['previous_close'] ?? null,
            'market_cap'     => $stock['market_cap'] ?? null,
            'pe_ratio'       => $stock['pe_ratio'] ?? null,
            'dividend_yield' => $stock['dividend_yield'] ?? null,
            'beta'           => $stock['beta'] ?? null,
            'avg_volume'     => $stock['avg_volume'] ?? null,
            'week_52_high'   => $stock['week_52_high'] ?? null,
            'week_52_low'    => $stock['week_52_low'] ?? null,
        ];
        $v = $map[$field] ?? null;
        return $v !== null ? (float) $v : null;
    }
}
