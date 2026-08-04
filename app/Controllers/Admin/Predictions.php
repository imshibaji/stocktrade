<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PredictionQueryModel;

class Predictions extends BaseController
{
    public function predictions(): string
    {
        $queryModel = new PredictionQueryModel();
        $queries = $queryModel->getAllForAdmin();

        $data = [
            'title' => 'Predictions - Admin - StockTrade Tips',
            'queries' => $queries,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'predictions', 'content' => view('admin/predictions', $data)])
            . view('templates/footer');
    }

    public function togglePublic(int $id)
    {
        $queryModel = new PredictionQueryModel();
        $query = $queryModel->getById($id);

        if (!$query) {
            return redirect()->back()->with('error', 'Prediction query not found.');
        }

        $queryModel->togglePublic($id);

        return redirect()->back()->with('success', 'Prediction query visibility updated.');
    }

    public function deletePrediction(int $id)
    {
        $queryModel = new PredictionQueryModel();
        $query = $queryModel->getById($id);

        if (!$query) {
            return redirect()->back()->with('error', 'Prediction query not found.');
        }

        $queryModel->delete($id);

        return redirect()->back()->with('success', 'Prediction query deleted.');
    }
}
