<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login(): string
    {
        $redirect = $this->request->getGet('redirect') ?? session()->get('redirect_after_login') ?? '';
        $data = ['title' => 'Login - StockTrade Tips', 'redirect' => $redirect];
        return view('templates/header', $data)
            . view('auth/login', $data)
            . view('templates/footer');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'isLoggedIn' => true,
                'user'       => [
                    'id'       => $user['id'],
                    'name'     => $user['name'],
                    'email'    => $user['email'],
                    'is_admin' => $user['is_admin'] ?? 0,
                ],
            ]);
            $redirect = $this->request->getPost('redirect') ?: $session->get('redirect_after_login') ?: '/dashboard';
            $session->remove('redirect_after_login');
            return redirect()->to($redirect)->with('success', 'Welcome back, ' . $user['name'] . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
    }

    public function register(): string
    {
        $data = ['title' => 'Register - StockTrade Tips'];
        return view('templates/header', $data)
            . view('auth/register', $data)
            . view('templates/footer');
    }

    public function attemptRegister()
    {
        $model = new UserModel();
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        if (!$model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/login')->with('success', 'Registration successful! Please login.');
    }

    public function logout()
    {
        $session = session();
        $session->setFlashdata('success', 'Logged out successfully.');
        $session->destroy();
        return redirect()->to('/');
    }
}
