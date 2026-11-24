<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdvancedDatabaseComponents extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. SQL View: vw_rekap_absensi_harian
        $db->query("DROP VIEW IF EXISTS `vw_rekap_absensi_harian`");
        $sqlView = "
        CREATE VIEW `vw_rekap_absensi_harian` AS
        SELECT 
            a.id AS id,
            a.id AS absensi_id,
            k.id AS karyawan_id,
            k.nik AS nik,
            k.nama AS nama,
            k.jabatan AS jabatan,
            k.departemen AS departemen,
            COALESCE(k.shift, 'pagi') AS shift,
            a.tanggal AS tanggal,
            a.jam_masuk AS jam_masuk,
            a.jam_keluar AS jam_keluar,
            a.status AS status,
            a.keterangan AS keterangan,
            a.foto AS foto,
            a.latitude AS latitude,
            a.longitude AS longitude,
            CASE 
                WHEN k.shift = 'siang' THEN COALESCE(p.shift_siang_masuk, '14:00:00')
                WHEN k.shift = 'malam' THEN COALESCE(p.shift_malam_masuk, '22:00:00')
                ELSE COALESCE(p.shift_pagi_masuk, p.jam_masuk, '08:00:00')
            END AS shift_jam_masuk,
            CASE 
                WHEN k.shift = 'siang' THEN COALESCE(p.shift_siang_keluar, '22:00:00')
                WHEN k.shift = 'malam' THEN COALESCE(p.shift_malam_keluar, '06:00:00')
                ELSE COALESCE(p.shift_pagi_keluar, p.jam_keluar, '16:00:00')
            END AS shift_jam_keluar,
            CASE 
                WHEN a.jam_masuk IS NOT NULL AND a.jam_masuk > (
                    CASE 
                        WHEN k.shift = 'siang' THEN COALESCE(p.shift_siang_masuk, '14:00:00')
                        WHEN k.shift = 'malam' THEN COALESCE(p.shift_malam_masuk, '22:00:00')
                        ELSE COALESCE(p.shift_pagi_masuk, p.jam_masuk, '08:00:00')
                    END
                ) THEN GREATEST(0, FLOOR(TIME_TO_SEC(TIMEDIFF(
                    a.jam_masuk,
                    CASE 
                        WHEN k.shift = 'siang' THEN COALESCE(p.shift_siang_masuk, '14:00:00')
                        WHEN k.shift = 'malam' THEN COALESCE(p.shift_malam_masuk, '22:00:00')
                        ELSE COALESCE(p.shift_pagi_masuk, p.jam_masuk, '08:00:00')
                    END
                )) / 60))
                ELSE 0
            END AS menit_keterlambatan,
            CASE 
                WHEN a.status = 'terlambat' THEN 1
                WHEN a.jam_masuk IS NOT NULL AND a.jam_masuk > ADDTIME(
                    CASE 
                        WHEN k.shift = 'siang' THEN COALESCE(p.shift_siang_masuk, '14:00:00')
                        WHEN k.shift = 'malam' THEN COALESCE(p.shift_malam_masuk, '22:00:00')
                        ELSE COALESCE(p.shift_pagi_masuk, p.jam_masuk, '08:00:00')
                    END, 
                    SEC_TO_TIME(COALESCE(p.toleransi_menit, 15) * 60)
                ) THEN 1
                ELSE 0
            END AS is_terlambat,
            CASE 
                WHEN a.jam_masuk IS NOT NULL AND a.jam_keluar IS NOT NULL THEN
                    CASE 
                        WHEN a.jam_keluar < a.jam_masuk THEN 
                            FLOOR((TIME_TO_SEC(a.jam_keluar) + 86400 - TIME_TO_SEC(a.jam_masuk)) / 60)
                        ELSE 
                            FLOOR(TIME_TO_SEC(TIMEDIFF(a.jam_keluar, a.jam_masuk)) / 60)
                    END
                ELSE NULL
            END AS durasi_kerja_menit,
            a.created_at AS created_at,
            a.updated_at AS updated_at
        FROM absensi a
        JOIN karyawan k ON k.id = a.karyawan_id
        LEFT JOIN pengaturan p ON p.id = 1
        ";
        $db->query($sqlView);

        // 2. Stored Function: fn_hitung_jarak_haversine
        $db->query("DROP FUNCTION IF EXISTS `fn_hitung_jarak_haversine`");
        $sqlFn = "
        CREATE FUNCTION `fn_hitung_jarak_haversine`(
            lat1 DOUBLE,
            lon1 DOUBLE,
            lat2 DOUBLE,
            lon2 DOUBLE
        )
        RETURNS INT
        DETERMINISTIC
        NO SQL
        BEGIN
            DECLARE earth_radius DOUBLE DEFAULT 6371000;
            DECLARE rad_lat1 DOUBLE;
            DECLARE rad_lat2 DOUBLE;
            DECLARE delta_lat DOUBLE;
            DECLARE delta_lon DOUBLE;
            DECLARE a DOUBLE;
            DECLARE c DOUBLE;

            IF lat1 IS NULL OR lon1 IS NULL OR lat2 IS NULL OR lon2 IS NULL THEN
                RETURN NULL;
            END IF;

            SET rad_lat1 = RADIANS(lat1);
            SET rad_lat2 = RADIANS(lat2);
            SET delta_lat = RADIANS(lat2 - lat1);
            SET delta_lon = RADIANS(lon2 - lon1);

            SET a = SIN(delta_lat / 2.0) * SIN(delta_lat / 2.0) +
                    COS(rad_lat1) * COS(rad_lat2) *
                    SIN(delta_lon / 2.0) * SIN(delta_lon / 2.0);

            IF a > 1.0 THEN
                SET a = 1.0;
            END IF;

            SET c = 2.0 * ATAN2(SQRT(a), SQRT(1.0 - a));

            RETURN ROUND(earth_radius * c);
        END
        ";
        $db->query($sqlFn);

        // 3. Stored Procedure: sp_proses_approval_pengajuan
        $db->query("DROP PROCEDURE IF EXISTS `sp_proses_approval_pengajuan`");
        $sqlProc = "
        CREATE PROCEDURE `sp_proses_approval_pengajuan`(
            IN p_pengajuan_id INT,
            IN p_catatan_admin TEXT,
            OUT p_status VARCHAR(20),
            OUT p_message VARCHAR(255)
        )
        proc_block: BEGIN
            DECLARE v_karyawan_id INT;
            DECLARE v_jenis ENUM('cuti','izin','sakit');
            DECLARE v_tgl_mulai DATE;
            DECLARE v_tgl_selesai DATE;
            DECLARE v_keterangan TEXT;
            DECLARE v_current_status ENUM('pending','approved','rejected');
            DECLARE v_cur_date DATE;
            DECLARE v_day_of_week INT;
            DECLARE v_status_absen VARCHAR(20);
            DECLARE v_absensi_id INT;
            DECLARE v_ket_absen TEXT;

            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                SET p_status = 'error';
                SET p_message = 'Terjadi kesalahan sistem database saat memproses approval.';
            END;

            SELECT karyawan_id, jenis, tgl_mulai, tgl_selesai, keterangan, status
            INTO v_karyawan_id, v_jenis, v_tgl_mulai, v_tgl_selesai, v_keterangan, v_current_status
            FROM pengajuan
            WHERE id = p_pengajuan_id;

            IF v_karyawan_id IS NULL THEN
                SET p_status = 'error';
                SET p_message = 'Data pengajuan tidak ditemukan!';
                LEAVE proc_block;
            END IF;

            IF v_current_status = 'approved' THEN
                SET p_status = 'warning';
                SET p_message = 'Pengajuan sudah pernah disetujui sebelumnya.';
                LEAVE proc_block;
            END IF;

            START TRANSACTION;

            UPDATE pengajuan
            SET status = 'approved',
                catatan_admin = COALESCE(NULLIF(p_catatan_admin, ''), 'Disetujui oleh admin'),
                updated_at = NOW()
            WHERE id = p_pengajuan_id;

            SET v_status_absen = IF(v_jenis = 'sakit', 'sakit', 'izin');
            SET v_ket_absen = CONCAT(
                CASE v_jenis
                    WHEN 'cuti' THEN 'Cuti'
                    WHEN 'sakit' THEN 'Sakit'
                    ELSE 'Izin'
                END,
                ': ',
                v_keterangan
            );

            SET v_cur_date = v_tgl_mulai;
            WHILE v_cur_date <= v_tgl_selesai DO
                SET v_day_of_week = DAYOFWEEK(v_cur_date);
                IF v_day_of_week NOT IN (1, 7) THEN
                    SET v_absensi_id = NULL;
                    SELECT id INTO v_absensi_id
                    FROM absensi
                    WHERE karyawan_id = v_karyawan_id AND tanggal = v_cur_date
                    LIMIT 1;

                    IF v_absensi_id IS NOT NULL THEN
                        UPDATE absensi
                        SET status = v_status_absen,
                            keterangan = v_ket_absen,
                            updated_at = NOW()
                        WHERE id = v_absensi_id;
                    ELSE
                        INSERT INTO absensi (karyawan_id, tanggal, status, keterangan, created_at, updated_at)
                        VALUES (v_karyawan_id, v_cur_date, v_status_absen, v_ket_absen, NOW(), NOW());
                    END IF;
                END IF;

                SET v_cur_date = DATE_ADD(v_cur_date, INTERVAL 1 DAY);
            END WHILE;

            COMMIT;

            SET p_status = 'success';
            SET p_message = 'Pengajuan disetujui & otomatis dicatat pada rekap kehadiran!';
        END
        ";
        $db->query($sqlProc);

        // 4. Trigger: trg_auto_create_user_karyawan
        $db->query("DROP TRIGGER IF EXISTS `trg_auto_create_user_karyawan`");
        $sqlTrg = "
        CREATE TRIGGER `trg_auto_create_user_karyawan`
        AFTER INSERT ON `karyawan`
        FOR EACH ROW
        BEGIN
            DECLARE v_username VARCHAR(100);
            DECLARE v_count INT;
            DECLARE v_default_pass VARCHAR(255);
            
            SET v_default_pass = '$2y$12$OTYmtnkqXJWdZfb9cLA/3uKMo806YQJHCfqlAztuCTXxfbguT31eG';
            SET v_username = CONCAT('emp', LPAD(NEW.id, 3, '0'));

            SELECT COUNT(*) INTO v_count FROM users WHERE username = v_username;
            IF v_count > 0 THEN
                SET v_username = CONCAT('emp', LPAD(NEW.id, 3, '0'), '_', FLOOR(10 + (RAND() * 89)));
            END IF;

            SELECT COUNT(*) INTO v_count FROM users WHERE karyawan_id = NEW.id;
            IF v_count = 0 THEN
                INSERT INTO users (username, password, nama, role, karyawan_id, created_at, updated_at)
                VALUES (v_username, v_default_pass, NEW.nama, 'karyawan', NEW.id, NOW(), NOW());
            END IF;
        END
        ";
        $db->query($sqlTrg);
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("DROP TRIGGER IF EXISTS `trg_auto_create_user_karyawan`");
        $db->query("DROP PROCEDURE IF EXISTS `sp_proses_approval_pengajuan`");
        $db->query("DROP FUNCTION IF EXISTS `fn_hitung_jarak_haversine`");
        $db->query("DROP VIEW IF EXISTS `vw_rekap_absensi_harian`");
    }
}
