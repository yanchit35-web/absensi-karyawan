<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanModel extends Model
{
    protected $table            = 'pengajuan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'karyawan_id',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'file_surat',
        'status',
        'catatan_admin',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPengajuan($karyawan_id = null, $status = null)
    {
        $builder = $this->select('pengajuan.*, karyawan.nama, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = pengajuan.karyawan_id');

        if ($karyawan_id) {
            $builder->where('pengajuan.karyawan_id', $karyawan_id);
        }

        if ($status) {
            $builder->where('pengajuan.status', $status);
        }

        return $builder->orderBy('pengajuan.created_at', 'DESC')->findAll();
    }
}

