<?php

namespace App\Controllers;

use App\Models\LemburModel;
use App\Models\KaryawanModel;

class LemburController extends BaseController
{
    protected $lemburModel;
    protected $karyawanModel;

    public function __construct()
    {
        $this->lemburModel   = new LemburModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');
        $status         = $this->request->getGet('status');

        if ($role === 'karyawan') {
            // Karyawan HANYA melihat data lembur miliknya sendiri
            $lembur = $this->lemburModel->getLembur($userKaryawanId, $status);
        } else {
            // Admin melihat semua pengajuan lembur
            $lembur = $this->lemburModel->getLembur(null, $status);
        }

        $data = [
            'title'      => ($role === 'karyawan') ? 'Pengajuan Lembur Saya' : 'Persetujuan Lembur Karyawan',
            'lembur'     => $lembur,
            'status'     => $status,
            'isKaryawan' => ($role === 'karyawan'),
        ];

        return view('lembur/index', $data);
    }

    public function create()
    {
        return view('lembur/create', [
            'title'      => 'Form Pengajuan Lembur',
            'validation' => null,
        ]);
    }

    public function store()
    {
        $karyawan_id = session()->get('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->to('/dashboard')->with('error', 'Akun tidak terhubung dengan profil karyawan!');
        }

        $rules = [
            'tanggal'     => 'required|valid_date[Y-m-d]',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'tugas'       => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return view('lembur/create', [
                'title'      => 'Form Pengajuan Lembur',
                'validation' => $this->validator,
            ]);
        }

        $this->lemburModel->insert([
            'karyawan_id' => $karyawan_id,
            'tanggal'     => $this->request->getPost('tanggal'),
            'jam_mulai'   => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'tugas'       => $this->request->getPost('tugas'),
            'status'      => 'pending',
        ]);

        return redirect()->to('/lembur')->with('success', 'Pengajuan lembur berhasil dikirim! Menunggu persetujuan admin.');
    }

    public function approve($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $lembur = $this->lemburModel->find($id);
        if (!$lembur) {
            return redirect()->to('/lembur')->with('error', 'Data lembur tidak ditemukan!');
        }

        $this->lemburModel->update($id, [
            'status'        => 'approved',
            'catatan_admin' => $this->request->getPost('catatan_admin') ?? 'Disetujui',
        ]);

        return redirect()->to('/lembur')->with('success', 'Pengajuan lembur telah disetujui!');
    }

    public function reject($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $lembur = $this->lemburModel->find($id);
        if (!$lembur) {
            return redirect()->to('/lembur')->with('error', 'Data lembur tidak ditemukan!');
        }

        $this->lemburModel->update($id, [
            'status'        => 'rejected',
            'catatan_admin' => $this->request->getPost('catatan_admin') ?? 'Ditolak',
        ]);

        return redirect()->to('/lembur')->with('warning', 'Pengajuan lembur ditolak.');
    }
}

