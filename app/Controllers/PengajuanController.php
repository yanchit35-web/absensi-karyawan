<?php

namespace App\Controllers;

use App\Models\PengajuanModel;
use App\Models\KaryawanModel;
use App\Models\AbsensiModel;

class PengajuanController extends BaseController
{
    protected $pengajuanModel;
    protected $karyawanModel;
    protected $absensiModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanModel();
        $this->karyawanModel  = new KaryawanModel();
        $this->absensiModel   = new AbsensiModel();
    }

    public function index()
    {
        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');
        $status         = $this->request->getGet('status');

        if ($role === 'karyawan') {
            // Karyawan HANYA bisa melihat pengajuannya sendiri
            $pengajuan = $this->pengajuanModel->getPengajuan($userKaryawanId, $status);
        } else {
            // Admin bisa melihat semua pengajuan
            $pengajuan = $this->pengajuanModel->getPengajuan(null, $status);
        }

        $data = [
            'title'      => ($role === 'karyawan') ? 'Pengajuan Cuti & Izin Saya' : 'Persetujuan Cuti & Izin',
            'pengajuan'  => $pengajuan,
            'status'     => $status,
            'isKaryawan' => ($role === 'karyawan'),
        ];

        return view('pengajuan/index', $data);
    }

    public function create()
    {
        return view('pengajuan/create', [
            'title'      => 'Form Pengajuan Cuti / Izin / Sakit',
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
            'jenis'       => 'required|in_list[cuti,izin,sakit]',
            'tgl_mulai'   => 'required|valid_date[Y-m-d]',
            'tgl_selesai' => 'required|valid_date[Y-m-d]',
            'keterangan'  => 'required|min_length[5]',
            'file_surat'  => 'permit_empty|uploaded[file_surat]|max_size[file_surat,3072]|ext_in[file_surat,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return view('pengajuan/create', [
                'title'      => 'Form Pengajuan Cuti / Izin / Sakit',
                'validation' => $this->validator,
            ]);
        }

        $fileSurat = $this->request->getFile('file_surat');
        $fileName  = null;
        if ($fileSurat && $fileSurat->isValid() && !$fileSurat->hasMoved()) {
            $fileName = $fileSurat->getRandomName();
            $uploadDir = FCPATH . 'uploads/surat/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileSurat->move($uploadDir, $fileName);
        }

        $this->pengajuanModel->insert([
            'karyawan_id' => $karyawan_id,
            'jenis'       => $this->request->getPost('jenis'),
            'tgl_mulai'   => $this->request->getPost('tgl_mulai'),
            'tgl_selesai' => $this->request->getPost('tgl_selesai'),
            'keterangan'  => $this->request->getPost('keterangan'),
            'file_surat'  => $fileName,
            'status'      => 'pending',
        ]);

        return redirect()->to('/pengajuan')->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan admin.');
    }

    public function approve($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            return redirect()->to('/pengajuan')->with('error', 'Pengajuan tidak ditemukan!');
        }

        $catatanAdmin = $this->request->getPost('catatan_admin') ?? 'Disetujui oleh admin';
        $db = \Config\Database::connect();

        // 1. Eksekusi Stored Procedure Atomik: sp_proses_approval_pengajuan
        try {
            $db->query("CALL sp_proses_approval_pengajuan(?, ?, @status, @message)", [
                (int)$id, 
                $catatanAdmin
            ]);
            $res = $db->query("SELECT @status AS status, @message AS message")->getRow();

            if ($res && !empty($res->status)) {
                if ($res->status === 'success') {
                    return redirect()->to('/pengajuan')->with('success', $res->message);
                } elseif ($res->status === 'warning') {
                    return redirect()->to('/pengajuan')->with('warning', $res->message);
                } elseif ($res->status === 'error') {
                    return redirect()->to('/pengajuan')->with('error', $res->message);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'sp_proses_approval_pengajuan fallback: ' . $e->getMessage());
        }

        // 2. Fallback PHP jika database environment tidak mendukung Stored Procedure
        $this->pengajuanModel->update($id, [
            'status'        => 'approved',
            'catatan_admin' => $catatanAdmin,
        ]);

        // Otomatis masukkan record ke absensi untuk rentang tanggal pengajuan
        $startDate = strtotime($pengajuan['tgl_mulai']);
        $endDate   = strtotime($pengajuan['tgl_selesai']);

        $statusAbsen = ($pengajuan['jenis'] === 'sakit') ? 'sakit' : 'izin';

        for ($current = $startDate; $current <= $endDate; $current = strtotime('+1 day', $current)) {
            $tglStr = date('Y-m-d', $current);
            $dayOfWeek = date('N', $current);
            if ($dayOfWeek >= 6) continue; // Lewati weekend

            $existing = $this->absensiModel
                ->where('karyawan_id', $pengajuan['karyawan_id'])
                ->where('tanggal', $tglStr)
                ->first();

            if ($existing) {
                $this->absensiModel->update($existing['id'], [
                    'status'     => $statusAbsen,
                    'keterangan' => ucfirst($pengajuan['jenis']) . ': ' . $pengajuan['keterangan'],
                ]);
            } else {
                $this->absensiModel->insert([
                    'karyawan_id' => $pengajuan['karyawan_id'],
                    'tanggal'     => $tglStr,
                    'status'      => $statusAbsen,
                    'keterangan'  => ucfirst($pengajuan['jenis']) . ': ' . $pengajuan['keterangan'],
                ]);
            }
        }

        return redirect()->to('/pengajuan')->with('success', 'Pengajuan disetujui & otomatis dicatat pada rekap kehadiran!');
    }

    public function reject($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            return redirect()->to('/pengajuan')->with('error', 'Pengajuan tidak ditemukan!');
        }

        $this->pengajuanModel->update($id, [
            'status'        => 'rejected',
            'catatan_admin' => $this->request->getPost('catatan_admin') ?? 'Ditolak oleh admin',
        ]);

        return redirect()->to('/pengajuan')->with('warning', 'Pengajuan berhasil ditolak.');
    }
}

