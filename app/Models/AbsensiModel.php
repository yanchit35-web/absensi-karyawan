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
     * Check if karyawan already absen today
     */
    public function cekAbsenHariIni($karyawan_id)
    {
        return $this->where('karyawan_id', $karyawan_id)
            ->where('tanggal', date('Y-m-d'))
            ->first();
    }
}
