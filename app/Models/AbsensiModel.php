<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'karyawan_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'status', 'keterangan', 'foto', 'latitude', 'longitude',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'tanggal'     => 'required|valid_date[Y-m-d]',
        'status'      => 'required|in_list[hadir,izin,sakit,alpha,terlambat]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Get absensi with karyawan join
     */
    public function getWithKaryawan($id = null)
    {
        $builder = $this->select('absensi.*, karyawan.nama, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id');

        if ($id !== null) {
            return $builder->where('absensi.id', $id)->first();
        }

        return $builder->orderBy('absensi.tanggal', 'DESC')->findAll();
    }

    /**
     * Get absensi today count by status
     */
    public function getTodayStats()
    {
        $today = date('Y-m-d');
        $stats = [
            'hadir'     => 0,
            'terlambat' => 0,
            'izin'      => 0,
            'sakit'     => 0,
            'alpha'     => 0,
        ];

        $results = $this->select('status, COUNT(*) as total')
            ->where('tanggal', $today)
            ->groupBy('status')
            ->findAll();

        foreach ($results as $row) {
            $stats[$row['status']] = (int) $row['total'];
        }

        return $stats;
    }

    /**
     * Get absensi by bulan/tahun with optional karyawan filter
     */
    public function getLaporan($bulan, $tahun, $karyawan_id = null, $departemen = null)
    {
        $query = $this->select('absensi.*, karyawan.nama, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('MONTH(absensi.tanggal)', $bulan)
            ->where('YEAR(absensi.tanggal)', $tahun);

        if ($karyawan_id) {
            $query->where('absensi.karyawan_id', $karyawan_id);
        }

        if ($departemen) {
            $query->where('karyawan.departemen', $departemen);
        }

        return $query->orderBy('absensi.tanggal', 'ASC')
            ->orderBy('karyawan.nama', 'ASC')
            ->findAll();
    }

    /**
     * Mengambil rekapitulasi absensi harian dari SQL View vw_rekap_absensi_harian
     */
    public function getRekapView($tanggal = null, $karyawan_id = null, $status = null)
    {
        $db = \Config\Database::connect();
        try {
            $builder = $db->table('vw_rekap_absensi_harian');
            if ($tanggal) {
                $builder->where('tanggal', $tanggal);
            }
            if ($karyawan_id) {
                $builder->where('karyawan_id', $karyawan_id);
            }
            if ($status) {
                $builder->where('status', $status);
            }
            return $builder->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->get()->getResultArray();
        } catch (\Throwable $e) {
            // Fallback ke tabel dasar jika View belum tersedia
            return $this->getWithKaryawan();
        }
    }

    /**
     * Hitung jarak geospasial menggunakan Stored Function SQL fn_hitung_jarak_haversine
     */
    public function getHaversineDistanceSQL($lat1, $lon1, $lat2, $lon2)
    {
        $db = \Config\Database::connect();
        try {
            $query = $db->query("SELECT fn_hitung_jarak_haversine(?, ?, ?, ?) AS distance", [
                (float)$lat1, (float)$lon1, (float)$lat2, (float)$lon2
            ]);
            $row = $query->getRow();
            if ($row && $row->distance !== null) {
                return (int)$row->distance;
            }
        } catch (\Throwable $e) {
            log_message('notice', 'fn_hitung_jarak_haversine exception: ' . $e->getMessage());
        }

        // Fallback formula matematis
        $earthRadius = 6371000;
        $lat1Rad = deg2rad((float)$lat1);
        $lat2Rad = deg2rad((float)$lat2);
        $deltaLat = deg2rad((float)$lat2 - (float)$lat1);
        $deltaLon = deg2rad((float)$lon2 - (float)$lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt(min(1.0, max(0.0, $a))), sqrt(max(0.0, 1 - $a)));

        return (int)round($earthRadius * $c);
    }

    /**
     * Check if karyawan already absen today
     */
    public function cekAbsenHariIni($karyawan_id)
    {
        return $this->where('karyawan_id', $karyawan_id)
            ->where('tanggal', date('Y-m-d'))
            ->first();
    }
}
