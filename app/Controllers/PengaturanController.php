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
            'nama_perusahaan'    => $this->request->getPost('nama_perusahaan'),
            'jam_masuk'          => $this->request->getPost('jam_masuk'),
            'jam_keluar'         => $this->request->getPost('jam_keluar'),
            'toleransi_menit'    => $this->request->getPost('toleransi_menit'),
            'shift_pagi_masuk'   => $this->request->getPost('shift_pagi_masuk') ?: '08:00:00',
            'shift_pagi_keluar'  => $this->request->getPost('shift_pagi_keluar') ?: '16:00:00',
            'shift_siang_masuk'  => $this->request->getPost('shift_siang_masuk') ?: '14:00:00',
            'shift_siang_keluar' => $this->request->getPost('shift_siang_keluar') ?: '22:00:00',
            'shift_malam_masuk'  => $this->request->getPost('shift_malam_masuk') ?: '22:00:00',
            'shift_malam_keluar' => $this->request->getPost('shift_malam_keluar') ?: '06:00:00',
            'kantor_lat'         => $this->request->getPost('kantor_lat'),
            'kantor_lng'         => $this->request->getPost('kantor_lng'),
            'kantor_radius'      => $this->request->getPost('kantor_radius'),
        ]);

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

