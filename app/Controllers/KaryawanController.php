<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\UserModel;

class KaryawanController extends BaseController
{
    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Halaman manajemen karyawan khusus Admin!');
        }

        $search     = $this->request->getGet('search');
        $departemen = $this->request->getGet('departemen');

        $db = \Config\Database::connect();
        $builder = $db->table('karyawan')
            ->select('karyawan.*, users.username, users.id as user_id')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left');

        if ($search) {
            $builder = $builder->groupStart()
                ->like('karyawan.nama', $search)
                ->orLike('karyawan.nik', $search)
                ->orLike('karyawan.jabatan', $search)
                ->orLike('users.username', $search)
                ->groupEnd();
        }

        if ($departemen) {
            $builder = $builder->where('karyawan.departemen', $departemen);
        }

        $karyawan    = $builder->orderBy('karyawan.nama', 'ASC')->get()->getResultArray();
        $departemens = $this->karyawanModel->getDepartemen();

        $data = [
            'title'      => 'Data Karyawan',
            'karyawan'   => $karyawan,
            'departemens'=> $departemens,
            'search'     => $search,
            'departemen' => $departemen,
        ];

        return view('karyawan/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $userModel = new UserModel();
        $nextUsername = $userModel->getNextEmpUsername();
        $nextNik      = $this->karyawanModel->getNextNik();

        $data = [
            'title'        => 'Tambah Karyawan',
            'nextUsername' => $nextUsername,
            'nextNik'      => $nextNik,
            'validation'   => null,
        ];
        return view('karyawan/create', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $rules = [
            'nik'        => 'required|max_length[20]|is_unique[karyawan.nik]',
            'nama'       => 'required|max_length[150]',
            'jabatan'    => 'required|max_length[100]',
            'departemen' => 'required|max_length[100]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
            'telepon'    => 'permit_empty|max_length[20]',
            'tgl_masuk'  => 'permit_empty|valid_date[Y-m-d]',
            'username'   => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'password'   => 'required|min_length[6]',
        ];

        $messages = [
            'username' => [
                'required'  => 'Username akun login wajib diisi.',
                'is_unique' => 'Username ini sudah digunakan oleh akun lain. Silakan pilih username lain.',
            ],
            'password' => [
                'required'   => 'Password akun login wajib diisi.',
                'min_length' => 'Password minimal harus 6 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $userModel = new UserModel();
            return view('karyawan/create', [
                'title'        => 'Tambah Karyawan',
                'nextUsername' => $userModel->getNextEmpUsername(),
                'nextNik'      => $this->karyawanModel->getNextNik(),
                'validation'   => $this->validator,
            ]);
        }

        $nama = $this->request->getPost('nama');

        // 1. Simpan data karyawan
        $karyawanId = $this->karyawanModel->insert([
            'nik'        => $this->request->getPost('nik'),
            'nama'       => $nama,
            'jabatan'    => $this->request->getPost('jabatan'),
            'departemen' => $this->request->getPost('departemen'),
            'email'      => $this->request->getPost('email'),
            'telepon'    => $this->request->getPost('telepon'),
            'tgl_masuk'  => $this->request->getPost('tgl_masuk') ?: null,
            'status'     => $this->request->getPost('status') ?? 'aktif',
        ]);

        // 2. Otomatis buatkan akun user untuk karyawan baru
        $userModel = new UserModel();
        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password') ?: 'password123';

        $userModel->insert([
            'username'    => $username,
            'password'    => $password,
            'nama'        => $nama,
            'role'        => 'karyawan',
            'karyawan_id' => $karyawanId,
        ]);

        return redirect()->to('/karyawan')->with('success', "Karyawan '{$nama}' berhasil ditambahkan! Akun login otomatis dibuat dengan username '{$username}'.");
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Karyawan tidak ditemukan!');
        }

        $userModel = new UserModel();
        $user = $userModel->where('karyawan_id', $id)->first();
        $suggestedUsername = $user ? $user['username'] : $userModel->getNextEmpUsername();

        $data = [
            'title'             => 'Edit Karyawan',
            'karyawan'          => $karyawan,
            'user'              => $user,
            'suggestedUsername' => $suggestedUsername,
            'validation'        => null,
        ];

        return view('karyawan/edit', $data);
    }

    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Karyawan tidak ditemukan!');
        }

        $userModel = new UserModel();
        $user = $userModel->where('karyawan_id', $id)->first();

        // Validasi keunikan username (kecuali milik user ini sendiri)
        $usernameRule = $user
            ? "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$user['id']}]"
            : "required|min_length[3]|max_length[100]|is_unique[users.username]";

        $rules = [
            'nik'        => "required|max_length[20]|is_unique[karyawan.nik,id,{$id}]",
            'nama'       => 'required|max_length[150]',
            'jabatan'    => 'required|max_length[100]',
            'departemen' => 'required|max_length[100]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
            'telepon'    => 'permit_empty|max_length[20]',
            'tgl_masuk'  => 'permit_empty|valid_date[Y-m-d]',
            'username'   => $usernameRule,
            'password'   => 'permit_empty|min_length[6]',
        ];

        $messages = [
            'username' => [
                'required'  => 'Username akun login wajib diisi.',
                'is_unique' => 'Username ini sudah digunakan oleh akun lain. Silakan pilih username lain.',
            ],
            'password' => [
                'min_length' => 'Password minimal harus 6 karakter jika ingin diubah.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return view('karyawan/edit', [
                'title'             => 'Edit Karyawan',
                'karyawan'          => $karyawan,
                'user'              => $user,
                'suggestedUsername' => $user ? $user['username'] : $userModel->getNextEmpUsername(),
                'validation'        => $this->validator,
            ]);
        }

        $namaKaryawan = $this->request->getPost('nama');

        // 1. Update data karyawan
        $this->karyawanModel->update($id, [
            'nik'        => $this->request->getPost('nik'),
            'nama'       => $namaKaryawan,
            'jabatan'    => $this->request->getPost('jabatan'),
            'departemen' => $this->request->getPost('departemen'),
            'email'      => $this->request->getPost('email'),
            'telepon'    => $this->request->getPost('telepon'),
            'tgl_masuk'  => $this->request->getPost('tgl_masuk') ?: null,
            'status'     => $this->request->getPost('status') ?? 'aktif',
        ]);

        // 2. Update atau Buat akun login di tabel users (untuk karyawan lama maupun baru)
        $newUsername = trim($this->request->getPost('username'));
        $newPassword = $this->request->getPost('password');

        if ($user) {
            $userData = [
                'username' => $newUsername,
                'nama'     => $namaKaryawan,
            ];
            if (!empty($newPassword)) {
                $userData['password'] = $newPassword; // Otomatis di-hash oleh UserModel
            }
            $userModel->update($user['id'], $userData);
        } else {
            // Jika karyawan lama belum memiliki akun di tabel users
            $userModel->insert([
                'username'    => $newUsername,
                'password'    => !empty($newPassword) ? $newPassword : 'password123',
                'nama'        => $namaKaryawan,
                'role'        => 'karyawan',
                'karyawan_id' => $id,
            ]);
        }

        $msg = "Data karyawan '{$namaKaryawan}' berhasil diperbarui! Username login: '{$newUsername}'.";
        if (!empty($newPassword)) {
            $msg .= " Password login berhasil diubah.";
        }

        return redirect()->to('/karyawan')->with('success', $msg);
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Karyawan tidak ditemukan!');
        }

        // Hapus akun user terkait jika ada
        $userModel = new UserModel();
        $userModel->where('karyawan_id', $id)->delete();

        $this->karyawanModel->delete($id);

        return redirect()->to('/karyawan')->with('success', 'Karyawan dan akun login terkait berhasil dihapus!');
    }
}
