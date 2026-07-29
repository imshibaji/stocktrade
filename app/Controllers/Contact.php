<?php

namespace App\Controllers;

class Contact extends BaseController
{
    public function index(): string
    {
        $data = ['title' => 'Contact Us - StockTrade Tips'];
        return view('templates/header', $data)
            . view('contact', $data)
            . view('templates/footer');
    }

    public function send()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required|min_length[2]',
            'email'   => 'required|valid_email',
            'message' => 'required|min_length[10]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->table('contact_messages')->insert([
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'message' => $this->request->getPost('message'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/contact')->with('success', 'Thank you! Your message has been received. We will get back to you soon.');
    }
}
