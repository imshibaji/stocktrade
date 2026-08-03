<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PageModel;
use App\Models\StockListModel;
use App\Models\StockModel;
use App\Models\UserModel;

class Dashboard extends BaseController
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
}
