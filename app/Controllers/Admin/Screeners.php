<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StockListModel;

class Screeners extends BaseController
{
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
}
