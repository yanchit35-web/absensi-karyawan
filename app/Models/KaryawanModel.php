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
        'nik', 'nama', 'jabatan', 'departemen', 'shift', 'email', 'telepon', 'tgl_masuk', 'status',
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

    /**
     * Cari NIK 'EMPXXX' berikutnya secara otomatis
     */
    public function getNextNik(): string
    {
        $rows = $this->select('nik')->like('nik', 'EMP', 'after')->findAll();
        $maxNum = 0;
        foreach ($rows as $r) {
            if (preg_match('/^emp(\d+)$/i', $r['nik'], $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $nextNum = $maxNum + 1;
        return 'EMP' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
    }
}
