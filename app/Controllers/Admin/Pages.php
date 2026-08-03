<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PageModel;

class Pages extends BaseController
{
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
}
