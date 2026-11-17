<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_perusahaan',
        'jam_masuk',
        'jam_keluar',
        'toleransi_menit',
        'shift_pagi_masuk',
        'shift_pagi_keluar',
        'shift_siang_masuk',
        'shift_siang_keluar',
        'shift_malam_masuk',
        'shift_malam_keluar',
        'kantor_lat',
        'kantor_lng',
        'kantor_radius',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSettings()
    {
        $setting = $this->first();
        if (!$setting) {
            $default = [
                'nama_perusahaan'    => 'PT. Modern Digital Nusantara',
                'jam_masuk'          => '08:00:00',
                'jam_keluar'         => '17:00:00',
                'toleransi_menit'    => 15,
                'shift_pagi_masuk'   => '08:00:00',
                'shift_pagi_keluar'  => '16:00:00',
                'shift_siang_masuk'  => '14:00:00',
                'shift_siang_keluar' => '22:00:00',
                'shift_malam_masuk'  => '22:00:00',
                'shift_malam_keluar' => '06:00:00',
                'kantor_lat'         => '-6.200000',
                'kantor_lng'         => '106.816666',
                'kantor_radius'      => 500,
            ];
            $id = $this->insert($default);
            return $this->find($id);
        }

        // Pastikan default jam shift selalu tersedia
        $setting['shift_pagi_masuk']   = $setting['shift_pagi_masuk'] ?: '08:00:00';
        $setting['shift_pagi_keluar']  = $setting['shift_pagi_keluar'] ?: '16:00:00';
        $setting['shift_siang_masuk']  = $setting['shift_siang_masuk'] ?: '14:00:00';
        $setting['shift_siang_keluar'] = $setting['shift_siang_keluar'] ?: '22:00:00';
        $setting['shift_malam_masuk']  = $setting['shift_malam_masuk'] ?: '22:00:00';
        $setting['shift_malam_keluar'] = $setting['shift_malam_keluar'] ?: '06:00:00';

        return $setting;
    }
}

