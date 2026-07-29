<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    public function index(): string
    {
        $user = current_user();

        $data = [
            'title' => 'Settings - StockTrade Tips',
            'user'  => $user,
        ];

        return view('templates/header', $data)
            . view('settings/index', $data)
            . view('templates/footer');
    }

    public function updateProfile()
    {
        $userId = current_user_id();
        $model = new UserModel();

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($userId, ['name' => $name, 'email' => $email]);

        $session = session();
        $user = $session->get('user');
        $user['name'] = $name;
        $user['email'] = $email;
        $session->set('user', $user);

        return redirect()->to('/settings')->with('success', 'Profile updated successfully.');
    }

    public function updateTax()
    {
        $userId = current_user_id();
        $model = new UserModel();

        $fields = ['stcg_rate', 'ltcg_rate', 'brokerage_pct', 'stt_pct', 'exchange_pct', 'gst_pct', 'stamp_duty_pct', 'sebi_fees'];
        $data = [];
        $session = session();
        $user = $session->get('user');

        foreach ($fields as $f) {
            $val = (float) $this->request->getPost($f);
            $data[$f] = min(100, max(0, $val));
            $user[$f] = $data[$f];
        }

        $model->update($userId, $data);
        $session->set('user', $user);

        return redirect()->to('/settings')->with('success', 'Tax & fee settings updated successfully.');
    }

    public function updatePassword()
    {
        $userId = current_user_id();
        $model = new UserModel();

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $user = $model->find($userId);

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters.');
        }

        $model->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/settings')->with('success', 'Password changed successfully.');
    }
}
