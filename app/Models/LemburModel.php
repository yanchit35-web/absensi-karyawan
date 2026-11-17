<?php

namespace App\Models;

use CodeIgniter\Model;

class LemburModel extends Model
{
    protected $table            = 'lembur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'karyawan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'tugas',
        'status',
        'catatan_admin',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLembur($karyawan_id = null, $status = null)
    {
        $builder = $this->select('lembur.*, karyawan.nama, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = lembur.karyawan_id');

        if ($karyawan_id) {
            $builder->where('lembur.karyawan_id', $karyawan_id);
        }

        if ($status) {
            $builder->where('lembur.status', $status);
        }

        return $builder->orderBy('lembur.created_at', 'DESC')->findAll();
    }
}

