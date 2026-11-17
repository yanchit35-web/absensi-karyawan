<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\AbsensiModel;
use App\Models\PengajuanModel;
use App\Models\LemburModel;
use App\Models\PengaturanModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $karyawanModel  = new KaryawanModel();
        $absensiModel   = new AbsensiModel();
        $pengajuanModel = new PengajuanModel();
        $lemburModel    = new LemburModel();
        $pengaturanModel= new PengaturanModel();

        $role           = session()->get('role');
        $userKaryawanId = session()->get('karyawan_id');
        $today          = date('Y-m-d');
        $currentMonth   = date('m');
        $currentYear    = date('Y');

        $setting = $pengaturanModel->getSettings();

        if ($role === 'karyawan') {
            // Dashboard khusus Karyawan (Data milik pribadi saja)
            $karyawanProfile = $karyawanModel->find($userKaryawanId);

            // Cek absen hari ini
            $absenHariIni = $absensiModel->cekAbsenHariIni($userKaryawanId);

            // Statistik kehadiran bulan ini untuk karyawan ini
            $totalHadirBulanIni = $absensiModel->where('karyawan_id', $userKaryawanId)
                ->where('MONTH(tanggal)', $currentMonth)
                ->where('YEAR(tanggal)', $currentYear)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->countAllResults();

            $totalTerlambatBulanIni = $absensiModel->where('karyawan_id', $userKaryawanId)
                ->where('MONTH(tanggal)', $currentMonth)
                ->where('YEAR(tanggal)', $currentYear)
                ->where('status', 'terlambat')
                ->countAllResults();

            $totalIzinSakitBulanIni = $absensiModel->where('karyawan_id', $userKaryawanId)
                ->where('MONTH(tanggal)', $currentMonth)
                ->where('YEAR(tanggal)', $currentYear)
                ->whereIn('status', ['izin', 'sakit'])
                ->countAllResults();

            // Pengajuan status
            $pengajuanPending = $pengajuanModel->where('karyawan_id', $userKaryawanId)
                ->where('status', 'pending')
                ->countAllResults();

            // 10 Riwayat Absen Terakhir Karyawan Ini Saja
            $riwayatPribadi = $absensiModel->where('karyawan_id', $userKaryawanId)
                ->orderBy('tanggal', 'DESC')
                ->limit(10)
                ->findAll();

            $data = [
                'title'                  => 'Dashboard Karyawan',
                'isKaryawan'             => true,
                'profile'                => $karyawanProfile,
                'absenHariIni'           => $absenHariIni,
                'totalHadirBulanIni'     => $totalHadirBulanIni,
                'totalTerlambatBulanIni' => $totalTerlambatBulanIni,
                'totalIzinSakitBulanIni' => $totalIzinSakitBulanIni,
                'pengajuanPending'       => $pengajuanPending,
                'riwayatPribadi'         => $riwayatPribadi,
                'setting'                => $setting,
            ];

            return view('dashboard/karyawan', $data);
        }

        // Dashboard Admin (Company-wide overview)
        $totalKaryawan = $karyawanModel->where('status', 'aktif')->countAllResults();
        $todayStats    = $absensiModel->getTodayStats();
        $hadirHariIni  = $todayStats['hadir'] + $todayStats['terlambat'];
        $terlambatHariIni = $todayStats['terlambat'];

        $sudahAbsen = $absensiModel->where('tanggal', $today)->countAllResults();
        $tidakHadir = max(0, $totalKaryawan - $sudahAbsen);

        $pendingPengajuan = $pengajuanModel->where('status', 'pending')->countAllResults();
        $pendingLembur    = $lemburModel->where('status', 'pending')->countAllResults();

        $rekapTerbaru = $absensiModel->select('absensi.*, karyawan.nama, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.tanggal', $today)
            ->orderBy('absensi.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title'            => 'Dashboard Admin',
            'isKaryawan'       => false,
            'totalKaryawan'    => $totalKaryawan,
            'hadirHariIni'     => $hadirHariIni,
            'tidakHadir'       => $tidakHadir,
            'terlambatHariIni' => $terlambatHariIni,
            'todayStats'       => $todayStats,
            'rekapTerbaru'     => $rekapTerbaru,
            'pendingPengajuan' => $pendingPengajuan,
            'pendingLembur'    => $pendingLembur,
            'setting'          => $setting,
        ];

        return view('dashboard/index', $data);
    }
}
