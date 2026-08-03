<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\StockModel;

class Settings extends BaseController
{
    public function settings(): string
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getGrouped();

        $data = [
            'title'   => 'Website Settings - Admin - StockTrade Tips',
            'settings' => $settings,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'settings', 'content' => view('admin/settings', $data)])
            . view('templates/footer');
    }

    public function updateSettings()
    {
        $settingModel = new SettingModel();
        $keys = $this->request->getPost('keys');
        $values = $this->request->getPost('values');

        if (is_array($keys) && is_array($values)) {
            foreach ($keys as $i => $key) {
                if ($key) {
                    $settingModel->set('value', $values[$i] ?? '')->where('key', $key)->update();
                }
            }
        }

        return redirect()->back()->with('success', 'Settings updated.');
    }

    public function featuredStocks(): string
    {
        $stockModel = new StockModel();
        $stocks = $stockModel->orderBy('market_cap', 'DESC')->findAll();

        $selected = [];
        $raw = (new SettingModel())->getValue('home_featured_stocks');
        if (is_string($raw) && $raw !== '') {
            foreach (explode(',', $raw) as $id) {
                $id = (int) trim($id);
                if ($id > 0) {
                    $selected[] = $id;
                }
            }
        }

        $stocksById = [];
        foreach ($stocks as $stock) {
            $stocksById[(int) $stock['id']] = $stock;
        }

        $data = [
            'title'      => 'Featured Home Stocks - Admin - StockTrade Tips',
            'stocks'     => $stocks,
            'stocksById' => $stocksById,
            'selected'   => $selected,
            'maxCount'   => 6,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'featured', 'content' => view('admin/featured_stocks', $data)])
            . view('templates/footer');
    }

    public function saveFeaturedStocks()
    {
        $ids = $this->request->getPost('ids');
        $positions = $this->request->getPost('positions');

        $ids = is_array($ids) ? $ids : [];
        $positions = is_array($positions) ? $positions : [];

        $entries = [];
        foreach ($ids as $stockId) {
            $stockId = (int) $stockId;
            if ($stockId <= 0) {
                continue;
            }
            $pos = (int) ($positions[$stockId] ?? 0);
            $entries[] = ['id' => $stockId, 'pos' => $pos];
        }

        usort($entries, static fn($a, $b) => $a['pos'] <=> $b['pos']);

        $max = max(1, (int) $this->request->getPost('max_count'));
        $ordered = array_slice(array_column($entries, 'id'), 0, $max);

        (new SettingModel())
            ->set('value', implode(',', $ordered))
            ->where('key', 'home_featured_stocks')
            ->update();

        return redirect()->to('/admin/featured-stocks')
            ->with('success', count($ordered) . ' stock(s) set as featured on the home page.');
    }

    public function clearFeaturedStocks()
    {
        (new SettingModel())
            ->set('value', '')
            ->where('key', 'home_featured_stocks')
            ->update();

        return redirect()->to('/admin/featured-stocks')
            ->with('success', 'Featured stocks cleared. Home page now uses automatic top-by-market-cap.');
    }
}
