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

        $builder = $this->karyawanModel;

        if ($search) {
            $builder = $builder->groupStart()
                ->like('nama', $search)
                ->orLike('nik', $search)
                ->orLike('jabatan', $search)
                ->groupEnd();
        }

        if ($departemen) {
            $builder = $builder->where('departemen', $departemen);
        }

        $karyawan   = $builder->orderBy('nama', 'ASC')->findAll();
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
        $data = [
            'title'      => 'Tambah Karyawan',
            'validation' => null,
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
        ];

        if (!$this->validate($rules)) {
            return view('karyawan/create', [
                'title'      => 'Tambah Karyawan',
                'validation' => $this->validator,
            ]);
        }

        $this->karyawanModel->insert([
            'nik'        => $this->request->getPost('nik'),
            'nama'       => $this->request->getPost('nama'),
            'jabatan'    => $this->request->getPost('jabatan'),
            'departemen' => $this->request->getPost('departemen'),
            'email'      => $this->request->getPost('email'),
            'telepon'    => $this->request->getPost('telepon'),
            'tgl_masuk'  => $this->request->getPost('tgl_masuk') ?: null,
            'status'     => $this->request->getPost('status') ?? 'aktif',
        ]);

        return redirect()->to('/karyawan')->with('success', 'Karyawan berhasil ditambahkan!');
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

        $data = [
            'title'      => 'Edit Karyawan',
            'karyawan'   => $karyawan,
            'validation' => null,
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

        $rules = [
            'nik'        => "required|max_length[20]|is_unique[karyawan.nik,id,{$id}]",
            'nama'       => 'required|max_length[150]',
            'jabatan'    => 'required|max_length[100]',
            'departemen' => 'required|max_length[100]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
            'telepon'    => 'permit_empty|max_length[20]',
            'tgl_masuk'  => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return view('karyawan/edit', [
                'title'      => 'Edit Karyawan',
                'karyawan'   => $karyawan,
                'validation' => $this->validator,
            ]);
        }

        $this->karyawanModel->update($id, [
            'nik'        => $this->request->getPost('nik'),
            'nama'       => $this->request->getPost('nama'),
            'jabatan'    => $this->request->getPost('jabatan'),
            'departemen' => $this->request->getPost('departemen'),
            'email'      => $this->request->getPost('email'),
            'telepon'    => $this->request->getPost('telepon'),
            'tgl_masuk'  => $this->request->getPost('tgl_masuk') ?: null,
            'status'     => $this->request->getPost('status') ?? 'aktif',
        ]);

        return redirect()->to('/karyawan')->with('success', 'Karyawan berhasil diupdate!');
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

        // Also delete the associated user account if any
        $userModel = new UserModel();
        $userModel->where('karyawan_id', $id)->delete();

        $this->karyawanModel->delete($id);

        return redirect()->to('/karyawan')->with('success', 'Karyawan berhasil dihapus!');
    }
}
