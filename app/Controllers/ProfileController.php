<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KaryawanModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Sesi tidak valid!');
        }

        $karyawan = null;
        if (!empty($user['karyawan_id'])) {
            $karyawanModel = new KaryawanModel();
            $karyawan = $karyawanModel->find($user['karyawan_id']);
        }

        $data = [
            'title'      => 'Profil & Ganti Password',
            'user'       => $user,
            'karyawan'   => $karyawan,
            'validation' => null,
        ];

        return view('profile/index', $data);
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Pengguna tidak ditemukan!');
        }

        $rules = [
            'password_lama'       => 'required',
            'password_baru'       => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ];

        $messages = [
            'password_lama' => [
                'required' => 'Password saat ini (lama) wajib diisi.',
            ],
            'password_baru' => [
                'required'   => 'Password baru wajib diisi.',
                'min_length' => 'Password baru minimal harus 6 karakter.',
            ],
            'konfirmasi_password' => [
                'required' => 'Konfirmasi password baru wajib diisi.',
                'matches'  => 'Konfirmasi password tidak cocok dengan password baru.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $karyawan = null;
            if (!empty($user['karyawan_id'])) {
                $karyawanModel = new KaryawanModel();
                $karyawan = $karyawanModel->find($user['karyawan_id']);
            }

            return view('profile/index', [
                'title'      => 'Profil & Ganti Password',
                'user'       => $user,
                'karyawan'   => $karyawan,
                'validation' => $this->validator,
            ]);
        }

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');

        // Verifikasi kesesuaian password lama
        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password saat ini (lama) yang Anda masukkan salah!');
        }

        // Simpan password baru (username TIDAK disentuh sama sekali)
        $userModel->update($userId, [
            'password' => $passwordBaru,
        ]);

        return redirect()->to('/profil')->with('success', 'Password Anda berhasil diperbarui! Silakan gunakan password baru ini pada sesi login berikutnya.');
    }
}
