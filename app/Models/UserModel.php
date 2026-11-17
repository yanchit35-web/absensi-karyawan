<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'password', 'nama', 'role', 'karyawan_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]',
        'password' => 'permit_empty|min_length[6]',
        'nama'     => 'required|max_length[150]',
        'role'     => 'permit_empty|in_list[admin,karyawan]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }
        return $data;
    }

    /**
     * Cari nomor urut username 'empXXX' berikutnya secara otomatis
     */
    public function getNextEmpUsername(): string
    {
        $users = $this->select('username')->like('username', 'emp', 'after')->findAll();
        $maxNum = 0;
        foreach ($users as $u) {
            if (preg_match('/^emp(\d+)$/i', $u['username'], $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $nextNum = $maxNum + 1;
        return 'emp' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
    }
}
