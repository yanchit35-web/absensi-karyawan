<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - <?= $namaBulan ?> <?= $tahun ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 15px 0 10px;
            border-bottom: 2px solid #1a1a2e;
            margin-bottom: 15px;
        }
        .header h2 {
            font-size: 16px;
            color: #1a1a2e;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 10px;
        }
        .info-item { }
        .info-item strong { color: #1a1a2e; }

        /* Summary */
        .summary {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .summary-item {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px;
            text-align: center;
        }
        .summary-item .num { font-size: 16px; font-weight: bold; }
        .summary-item .label { font-size: 9px; color: #666; margin-top: 2px; }
        .s-hadir { border-color: #198754; color: #198754; }
        .s-terlambat { border-color: #ffc107; color: #e6a800; }
        .s-izin { border-color: #0dcaf0; color: #0dcaf0; }
        .s-sakit { border-color: #6c757d; color: #6c757d; }
        .s-alpha { border-color: #dc3545; color: #dc3545; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        thead tr {
            background-color: #1a1a2e;
            color: white;
        }
        thead th {
            padding: 7px 5px;
            text-align: left;
            font-weight: 600;
        }
        tbody tr:nth-child(even) { background-color: #f8f9fa; }
        tbody td {
            padding: 5px 5px;
            border-bottom: 1px solid #e9ecef;
        }
        .status-hadir     { color: #198754; font-weight: bold; }
        .status-terlambat { color: #e6a800; font-weight: bold; }
        .status-izin      { color: #0dcaf0; font-weight: bold; }
        .status-sakit     { color: #6c757d; font-weight: bold; }
        .status-alpha     { color: #dc3545; font-weight: bold; }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?= !empty($isKaryawan) ? 'Laporan Kehadiran Karyawan (Pribadi)' : 'Laporan Rekap Absensi Karyawan' ?></h2>
        <p>Periode: <?= $namaBulan ?> <?= $tahun ?><?= $departemen ? " | Departemen: {$departemen}" : "" ?></p>
        <?php if (!empty($isKaryawan) && !empty($karyawanInfo)): ?>
            <p style="margin-top: 5px; font-weight: bold; color: #1a1a2e;">
                Nama: <?= esc($karyawanInfo['nama']) ?> (NIK: <?= esc($karyawanInfo['nik']) ?>) &bull; <?= esc($karyawanInfo['jabatan']) ?> - <?= esc($karyawanInfo['departemen']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="info-row">
        <div class="info-item">
            <strong>Total Data:</strong> <?= count($absensi) ?> record
        </div>
        <div class="info-item">
            <strong>Dicetak:</strong> <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>

    <?php
    $summary = ['hadir'=>0,'terlambat'=>0,'izin'=>0,'sakit'=>0,'alpha'=>0];
    foreach ($absensi as $row) { $summary[$row['status']] = ($summary[$row['status']] ?? 0) + 1; }
    ?>

    <div class="summary">
        <div class="summary-item s-hadir">
            <div class="num"><?= $summary['hadir'] ?></div>
            <div class="label">Hadir</div>
        </div>
        <div class="summary-item s-terlambat">
            <div class="num"><?= $summary['terlambat'] ?></div>
            <div class="label">Terlambat</div>
        </div>
        <div class="summary-item s-izin">
            <div class="num"><?= $summary['izin'] ?></div>
            <div class="label">Izin</div>
        </div>
        <div class="summary-item s-sakit">
            <div class="num"><?= $summary['sakit'] ?></div>
            <div class="label">Sakit</div>
        </div>
        <div class="summary-item s-alpha">
            <div class="num"><?= $summary['alpha'] ?></div>
            <div class="label">Alpha</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="70">Tanggal</th>
                <th width="60">NIK</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th width="50">Jam Masuk</th>
                <th width="50">Jam Keluar</th>
                <th width="55">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($absensi)): ?>
            <tr>
                <td colspan="10" style="text-align:center;padding:20px;color:#999;">
                    Tidak ada data absensi untuk periode ini
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($absensi as $i => $row): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                <td><?= htmlspecialchars($row['nik']) ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                <td><?= htmlspecialchars($row['departemen']) ?></td>
                <td><?= $row['jam_masuk'] ? substr($row['jam_masuk'], 0, 5) : '-' ?></td>
                <td><?= $row['jam_keluar'] ? substr($row['jam_keluar'], 0, 5) : '-' ?></td>
                <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dibuat secara otomatis oleh Sistem Absensi Karyawan pada <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html>
