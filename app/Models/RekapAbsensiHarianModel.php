<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapAbsensiHarianModel extends Model
{
    protected $table            = 'vw_rekap_absensi_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = false;

    /**
     * Mengambil rekapitulasi data absensi dari SQL View vw_rekap_absensi_harian
     * lengkap dengan perhitungan menit keterlambatan, status shift, dan durasi kerja.
     */
    public function getRekap($tanggal = null, $karyawan_id = null, $status = null, $departemen = null, $shift = null)
    {
        $builder = $this->builder();

        if (!empty($tanggal)) {
            $builder->where('tanggal', $tanggal);
        }

        if (!empty($karyawan_id)) {
            $builder->where('karyawan_id', $karyawan_id);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        if (!empty($departemen)) {
            $builder->where('departemen', $departemen);
        }

        if (!empty($shift)) {
            $builder->where('shift', $shift);
        }

        return $builder->orderBy('tanggal', 'DESC')
                       ->orderBy('jam_masuk', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Rekap statistik kehadiran hari ini dari view
     */
    public function getTodaySummary()
    {
        $today = date('Y-m-d');
        return $this->select("
            COUNT(*) as total_absen,
            SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN status = 'terlambat' OR is_terlambat = 1 THEN 1 ELSE 0 END) as total_terlambat,
            SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
            AVG(CASE WHEN is_terlambat = 1 THEN menit_keterlambatan ELSE NULL END) as rata_rata_telat_menit
        ")
        ->where('tanggal', $today)
        ->first();
    }
}
