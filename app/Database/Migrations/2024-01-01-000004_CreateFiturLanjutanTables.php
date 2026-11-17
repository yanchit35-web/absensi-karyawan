<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFiturLanjutanTables extends Migration
{
    public function up()
    {
        // 1. Tambah kolom ke tabel absensi: foto, latitude, longitude
        $fieldsAbsensi = [
            'foto' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'keterangan',
            ],
            'latitude' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'foto',
            ],
            'longitude' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'latitude',
            ],
        ];
        $this->forge->addColumn('absensi', $fieldsAbsensi);

        // 2. Buat tabel pengaturan
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_perusahaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'PT. Modern Digital Nusantara',
            ],
            'jam_masuk' => [
                'type' => 'TIME',
                'default' => '08:00:00',
            ],
            'jam_keluar' => [
                'type' => 'TIME',
                'default' => '17:00:00',
            ],
            'toleransi_menit' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 15,
            ],
            'kantor_lat' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '-6.200000',
            ],
            'kantor_lng' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '106.816666',
            ],
            'kantor_radius' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 500, // radius dalam meter
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('pengaturan');

        // 3. Buat tabel pengajuan (Cuti / Izin / Sakit)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'karyawan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['cuti', 'izin', 'sakit'],
                'default'    => 'izin',
            ],
            'tgl_mulai' => [
                'type' => 'DATE',
            ],
            'tgl_selesai' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'file_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'catatan_admin' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan');

        // 4. Buat tabel lembur
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'karyawan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jam_mulai' => [
                'type' => 'TIME',
            ],
            'jam_selesai' => [
                'type' => 'TIME',
            ],
            'tugas' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'catatan_admin' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lembur');
    }

    public function down()
    {
        $this->forge->dropTable('lembur');
        $this->forge->dropTable('pengajuan');
        $this->forge->dropTable('pengaturan');
        $this->forge->dropColumn('absensi', ['foto', 'latitude', 'longitude']);
    }
}

