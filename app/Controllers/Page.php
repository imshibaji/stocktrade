<?php

namespace App\Controllers;

use App\Models\PageModel;

class Page extends BaseController
{
    public function show($slug)
    {
        $model = new PageModel();
        $page = $model->where('slug', $slug)->where('is_active', 1)->first();

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $page['meta_title'] ?? $page['title'],
            'page'  => $page,
        ];

        return view('templates/header', $data)
            . view('pages/show', $data)
            . view('templates/footer');
    }
}
