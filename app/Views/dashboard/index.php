<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Approval Notifications for Admin -->
<?php if (!empty($pendingPengajuan) || !empty($pendingLembur)): ?>
<div class="row g-3 mb-4">
    <?php if (!empty($pendingPengajuan)): ?>
    <div class="col-md-6">
        <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 m-0 border-0 shadow-sm rounded-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-bell-fill fs-3 text-warning"></i>
                <div>
                    <strong><?= $pendingPengajuan ?> Pengajuan Cuti / Izin Menunggu Persetujuan!</strong>
                    <div class="small text-muted">Karyawan telah mengajukan permohonan yang perlu diverifikasi.</div>
                </div>
            </div>
            <a href="/pengajuan?status=pending" class="btn btn-warning btn-sm fw-bold">Tinjau Sekarang</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($pendingLembur)): ?>
    <div class="col-md-6">
        <div class="alert alert-info d-flex align-items-center justify-content-between p-3 m-0 border-0 shadow-sm rounded-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-clock-history fs-3 text-info"></i>
                <div>
                    <strong><?= $pendingLembur ?> Pengajuan Lembur Menunggu Persetujuan!</strong>
                    <div class="small text-muted">Ada pengajuan jam lembur yang perlu persetujuan admin.</div>
                </div>
            </div>
            <a href="/lembur?status=pending" class="btn btn-info btn-sm fw-bold text-dark">Tinjau Sekarang</a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border-left: 5px solid #0f3460;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:55px;height:55px;border-radius:14px;background:rgba(15,52,96,0.1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.82rem;font-weight:600;">TOTAL KARYAWAN</div>
                    <div class="fw-bold" style="font-size:2rem;line-height:1.1;"><?= $totalKaryawan ?></div>
                    <div class="text-muted" style="font-size:0.75rem;">Karyawan aktif</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border-left: 5px solid #198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:55px;height:55px;border-radius:14px;background:rgba(25,135,84,0.1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.82rem;font-weight:600;">HADIR HARI INI</div>
                    <div class="fw-bold text-success" style="font-size:2rem;line-height:1.1;"><?= $hadirHariIni ?></div>
                    <div class="text-muted" style="font-size:0.75rem;">Karyawan hadir</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border-left: 5px solid #dc3545;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:55px;height:55px;border-radius:14px;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-x-circle-fill fs-3 text-danger"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.82rem;font-weight:600;">TIDAK HADIR</div>
                    <div class="fw-bold text-danger" style="font-size:2rem;line-height:1.1;"><?= $tidakHadir ?></div>
                    <div class="text-muted" style="font-size:0.75rem;">Belum absen</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border-left: 5px solid #ffc107;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:55px;height:55px;border-radius:14px;background:rgba(255,193,7,0.1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-clock-fill fs-3 text-warning"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.82rem;font-weight:600;">TERLAMBAT</div>
                    <div class="fw-bold text-warning" style="font-size:2rem;line-height:1.1;"><?= $terlambatHariIni ?></div>
                    <div class="text-muted" style="font-size:0.75rem;">Karyawan terlambat</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary & Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Status Hari Ini</h6>
            </div>
            <div class="card-body">
                <?php
                $statuses = [
                    'hadir'     => ['label' => 'Hadir',     'color' => 'success', 'icon' => 'check-circle'],
                    'terlambat' => ['label' => 'Terlambat', 'color' => 'warning', 'icon' => 'clock'],
                    'izin'      => ['label' => 'Izin',      'color' => 'info',    'icon' => 'envelope'],
                    'sakit'     => ['label' => 'Sakit',     'color' => 'secondary','icon'=> 'heart-pulse'],
                    'alpha'     => ['label' => 'Alpha',     'color' => 'danger',  'icon' => 'x-circle'],
                ];
                foreach ($statuses as $key => $s):
                    $count = $todayStats[$key] ?? 0;
                ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-<?= $s['icon'] ?>-fill text-<?= $s['color'] ?>"></i>
                        <span><?= $s['label'] ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="width:100px;height:8px;">
                            <?php $pct = $totalKaryawan > 0 ? round($count / $totalKaryawan * 100) : 0; ?>
                            <div class="progress-bar bg-<?= $s['color'] ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                        <span class="badge bg-<?= $s['color'] ?>"><?= $count ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Absensi Hari Ini</h6>
                <a href="/absensi" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($rekapTerbaru)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data absensi hari ini
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rekapTerbaru as $row): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($row['nama']) ?></td>
                                <td><?= esc($row['departemen']) ?></td>
                                <td><?= $row['jam_masuk'] ? substr($row['jam_masuk'], 0, 5) : '<span class="text-muted">-</span>' ?></td>
                                <td><?= $row['jam_keluar'] ? substr($row['jam_keluar'], 0, 5) : '<span class="text-muted">-</span>' ?></td>
                                <td>
                                    <?php
                                    $badgeMap = ['hadir'=>'success','terlambat'=>'warning','izin'=>'info','sakit'=>'secondary','alpha'=>'danger'];
                                    $badge = $badgeMap[$row['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action for Employee -->
<?php if (session()->get('role') === 'karyawan'): ?>
<div class="row g-4">
    <div class="col-12">
        <div class="card" style="border: 2px dashed #0f3460;">
            <div class="card-body text-center py-4">
                <i class="bi bi-fingerprint display-4 text-primary mb-3 d-block"></i>
                <h5 class="fw-bold">Absen Sekarang</h5>
                <p class="text-muted">Klik tombol di bawah untuk mencatat kehadiran Anda</p>
                <a href="/self-absen" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-fingerprint me-2"></i> Absen Sekarang
                </a>
                <div class="mt-2 text-muted" style="font-size:0.85rem;">
                    Waktu sekarang: <strong><?= date('H:i:s') ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
