<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummySeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('absensi')->truncate();
        $this->db->table('users')->where('role', 'karyawan')->delete();
        $this->db->table('karyawan')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $karyawanData = [
            [
                'nik'         => 'EMP001',
                'nama'        => 'Ahmad Fauzi',
                'jabatan'     => 'Software Engineer',
                'departemen'  => 'IT',
                'email'       => 'fauzi@example.com',
                'telepon'     => '081234567801',
                'tgl_masuk'   => '2023-01-15',
                'status'      => 'aktif',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nik'         => 'EMP002',
                'nama'        => 'Siti Rahmawati',
                'jabatan'     => 'HR Specialist',
                'departemen'  => 'HRD',
                'email'       => 'siti@example.com',
                'telepon'     => '081234567802',
                'tgl_masuk'   => '2023-03-01',
                'status'      => 'aktif',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nik'         => 'EMP003',
                'nama'        => 'Budi Santoso',
                'jabatan'     => 'Finance Officer',
                'departemen'  => 'Keuangan',
                'email'       => 'budi@example.com',
                'telepon'     => '081234567803',
                'tgl_masuk'   => '2022-07-10',
                'status'      => 'aktif',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nik'         => 'EMP004',
                'nama'        => 'Dewi Lestari',
                'jabatan'     => 'Marketing Lead',
                'departemen'  => 'Marketing',
                'email'       => 'dewi@example.com',
                'telepon'     => '081234567804',
                'tgl_masuk'   => '2023-05-20',
                'status'      => 'aktif',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nik'         => 'EMP005',
                'nama'        => 'Rian Pratama',
                'jabatan'     => 'UI/UX Designer',
                'departemen'  => 'IT',
                'email'       => 'rian@example.com',
                'telepon'     => '081234567805',
                'tgl_masuk'   => '2024-02-01',
                'status'      => 'aktif',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('karyawan')->insertBatch($karyawanData);

        // Fetch inserted IDs
        $karyawans = $this->db->table('karyawan')->get()->getResultArray();

        // Create user accounts for employees
        $userData = [];
        foreach ($karyawans as $k) {
            $userData[] = [
                'username'    => strtolower($k['nik']),
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'nama'        => $k['nama'],
                'role'        => 'karyawan',
                'karyawan_id' => $k['id'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('users')->insertBatch($userData);

        // Generate Attendance for October & November 2026
        $absensiData = [];
        $statuses = ['hadir', 'hadir', 'hadir', 'terlambat', 'hadir'];

        // Seed 10 days of attendance
        for ($d = 20; $d <= 31; $d++) {
            $dateStr = sprintf('2026-10-%02d', $d);
            $dayOfWeek = date('N', strtotime($dateStr));
            if ($dayOfWeek >= 6) continue;

            foreach ($karyawans as $idx => $k) {
                $status = $statuses[($idx + $d) % count($statuses)];
                if ($d == 28 && $idx == 1) $status = 'izin';
                if ($d == 29 && $idx == 2) $status = 'sakit';

                $jamMasuk = ($status === 'terlambat') ? '08:35:00' : '07:55:00';
                $jamKeluar = ($status === 'izin' || $status === 'sakit') ? null : '17:05:00';
                if ($status === 'izin' || $status === 'sakit') {
                    $jamMasuk = null;
                }

                $absensiData[] = [
                    'karyawan_id' => $k['id'],
                    'tanggal'     => $dateStr,
                    'jam_masuk'   => $jamMasuk,
                    'jam_keluar'  => $jamKeluar,
                    'status'      => $status,
                    'keterangan'  => ($status === 'izin' ? 'Urusan keluarga' : ($status === 'sakit' ? 'Surat dokter' : 'Tepat waktu')),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Add today Nov 1
        foreach ($karyawans as $idx => $k) {
            $absensiData[] = [
                'karyawan_id' => $k['id'],
                'tanggal'     => date('Y-m-d'),
                'jam_masuk'   => ($idx % 2 == 0) ? '07:50:00' : '08:30:00',
                'jam_keluar'  => null,
                'status'      => ($idx % 2 == 0) ? 'hadir' : 'terlambat',
                'keterangan'  => 'Hadir shift pagi',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($absensiData)) {
            $this->db->table('absensi')->insertBatch($absensiData);
        }

        // Seed Sample Pengajuan (Cuti / Izin / Sakit)
        $this->db->table('pengajuan')->truncate();
        $pengajuanSample = [
            [
                'karyawan_id'   => $karyawans[0]['id'],
                'jenis'         => 'cuti',
                'tgl_mulai'     => '2026-11-10',
                'tgl_selesai'   => '2026-11-12',
                'keterangan'    => 'Cuti tahunan untuk acara keluarga di luar kota',
                'file_surat'    => null,
                'status'        => 'pending',
                'catatan_admin' => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'karyawan_id'   => $karyawans[1]['id'],
                'jenis'         => 'izin',
                'tgl_mulai'     => '2026-11-05',
                'tgl_selesai'   => '2026-11-05',
                'keterangan'    => 'Izin urusan perpanjangan paspor di kantor imigrasi',
                'file_surat'    => null,
                'status'        => 'approved',
                'catatan_admin' => 'Disetujui. Harap selesaikan pekerjaan sebelum izin.',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('pengajuan')->insertBatch($pengajuanSample);

        // Seed Sample Lembur
        $this->db->table('lembur')->truncate();
        $lemburSample = [
            [
                'karyawan_id'   => $karyawans[0]['id'],
                'tanggal'       => '2026-11-02',
                'jam_mulai'     => '17:30:00',
                'jam_selesai'   => '21:00:00',
                'tugas'         => 'Maintenance server dan deploy update fitur absensi',
                'status'        => 'pending',
                'catatan_admin' => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('lembur')->insertBatch($lemburSample);
    }
}
