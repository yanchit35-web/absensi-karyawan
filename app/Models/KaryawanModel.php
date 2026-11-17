<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nik', 'nama', 'jabatan', 'departemen', 'email', 'telepon', 'tgl_masuk', 'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nik'        => 'required|max_length[20]',
        'nama'       => 'required|max_length[150]',
        'jabatan'    => 'required|max_length[100]',
        'departemen' => 'required|max_length[100]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Get unique departemen list
     */
    public function getDepartemen()
    {
        return $this->distinct()->select('departemen')->findAll();
    }

    /**
     * Get karyawan by status aktif
     */
    public function getAktif()
    {
        return $this->where('status', 'aktif')->findAll();
    }
}
