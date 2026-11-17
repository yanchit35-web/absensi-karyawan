<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $userModel = new UserModel();
        $username  = $this->request->getPost('username');
        $password  = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'logged_in'   => true,
                'user_id'     => $user['id'],
                'username'    => $user['username'],
                'nama'        => $user['nama'],
                'role'        => $user['role'],
                'karyawan_id' => $user['karyawan_id'],
            ]);
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Username atau password salah!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
