<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
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
}
