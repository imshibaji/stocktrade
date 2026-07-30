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
            . view('admin/index', $data)
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
            . view('admin/users', $data)
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
            . view('admin/stocks', $data)
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
            . view('admin/screeners', $data)
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
            . view('admin/pages', $data)
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
            . view('admin/edit_page', $data)
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
            . view('admin/settings', $data)
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
                    $settingModel->where('key', $key)->update(['value' => $values[$i] ?? '']);
                }
            }
        }

        return redirect()->back()->with('success', 'Settings updated.');
    }
}
