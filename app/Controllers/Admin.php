<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StockModel;
use App\Models\StockListModel;
use App\Models\PageModel;
use App\Models\SettingModel;

class Admin extends BaseController
{
    public function index(): string
    {
        $stockModel = new StockModel();
        $userModel = new UserModel();
        $pageModel = new PageModel();

        $stats = [
            'users'    => $userModel->countAllResults(),
            'stocks'   => $stockModel->countAllResults(),
            'pages'    => $pageModel->countAllResults(),
            'screeners' => (new StockListModel())->countAllResults(),
        ];

        $data = [
            'title' => 'Admin Dashboard - StockTrade Tips',
            'stats' => $stats,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'dashboard', 'content' => view('admin/index', $data)])
            . view('templates/footer');
    }

    public function users(): string
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'title' => 'Users - Admin - StockTrade Tips',
            'users' => $users,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'users', 'content' => view('admin/users', $data)])
            . view('templates/footer');
    }

    public function makeAdmin($id)
    {
        $userModel = new UserModel();
        $userModel->update((int) $id, ['is_admin' => 1]);
        return redirect()->back()->with('success', 'User is now an admin.');
    }

    public function removeAdmin($id)
    {
        $userModel = new UserModel();
        $userModel->update((int) $id, ['is_admin' => 0]);
        return redirect()->back()->with('success', 'Admin privileges removed.');
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        $userModel->delete((int) $id);
        return redirect()->back()->with('success', 'User deleted.');
    }

    public function stocks(): string
    {
        $stockModel = new StockModel();
        $stocks = $stockModel->orderBy('symbol', 'ASC')->findAll();

        $data = [
            'title' => 'Stocks - Admin - StockTrade Tips',
            'stocks' => $stocks,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'stocks', 'content' => view('admin/stocks', $data)])
            . view('templates/footer');
    }

    public function deleteStock($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found.');
        }
        $db = \Config\Database::connect();
        $db->table('watchlist')->where('stock_id', (int) $id)->delete();
        $db->table('stock_prices')->where('stock_id', (int) $id)->delete();
        $db->table('predictions')->where('stock_id', (int) $id)->delete();
        $db->table('investments')->where('stock_id', (int) $id)->delete();
        $stockModel->delete((int) $id);
        return redirect()->back()->with('success', $stock['symbol'] . ' deleted.');
    }

    public function screeners(): string
    {
        $listModel = new StockListModel();
        $screeners = $listModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Screeners - Admin - StockTrade Tips',
            'screeners' => $screeners,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'screeners', 'content' => view('admin/screeners', $data)])
            . view('templates/footer');
    }

    public function deleteScreeners()
    {
        $listModel = new StockListModel();
        $listModel->emptyTable();
        return redirect()->back()->with('success', 'All screeners deleted.');
    }

    public function pages(): string
    {
        $pageModel = new PageModel();
        $pages = $pageModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title' => 'Pages - Admin - StockTrade Tips',
            'pages' => $pages,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'pages', 'content' => view('admin/pages', $data)])
            . view('templates/footer');
    }

    public function editPage($id = null): string
    {
        $pageModel = new PageModel();
        $page = $id ? $pageModel->find((int) $id) : null;

        $data = [
            'title' => ($page ? 'Edit ' . $page['title'] : 'Add New Page') . ' - Admin - StockTrade Tips',
            'page'  => $page,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'pages', 'content' => view('admin/edit_page', $data)])
            . view('templates/footer');
    }

    public function savePage()
    {
        $pageModel = new PageModel();
        $id = $this->request->getPost('id');

        $data = [
            'title'            => $this->request->getPost('title'),
            'slug'             => $this->request->getPost('slug'),
            'content'          => $this->request->getPost('content'),
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id) {
            $pageModel->update((int) $id, $data);
            return redirect()->to('/admin/pages')->with('success', 'Page updated.');
        } else {
            $pageModel->insert($data);
            return redirect()->to('/admin/pages')->with('success', 'Page created.');
        }
    }

    public function deletePage($id)
    {
        $pageModel = new PageModel();
        $pageModel->delete((int) $id);
        return redirect()->back()->with('success', 'Page deleted.');
    }

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
