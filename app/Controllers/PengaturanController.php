<?php

namespace App\Controllers;

use App\Models\PengaturanModel;

class PengaturanController extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $setting = $this->pengaturanModel->getSettings();

        return view('pengaturan/index', [
            'title'   => 'Pengaturan Jam Kerja & Kantor',
            'setting' => $setting,
        ]);
    }

    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $setting = $this->pengaturanModel->getSettings();

        $this->pengaturanModel->update($setting['id'], [
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'jam_masuk'       => $this->request->getPost('jam_masuk'),
            'jam_keluar'      => $this->request->getPost('jam_keluar'),
            'toleransi_menit' => $this->request->getPost('toleransi_menit'),
            'kantor_lat'      => $this->request->getPost('kantor_lat'),
            'kantor_lng'      => $this->request->getPost('kantor_lng'),
            'kantor_radius'   => $this->request->getPost('kantor_radius'),
        ]);

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

