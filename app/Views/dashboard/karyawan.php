<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .employee-meta-pill {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 50rem;
        padding: 5px 14px 5px 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
    }
    .employee-meta-pill:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.32);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
    }
    .icon-bubble {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #0f3460 0%, #16213e 55%, #1a1a2e 100%);">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Avatar Icon -->
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #0f3460, #533483); font-size: 1.6rem; border: 2px solid rgba(255,255,255,0.35); box-shadow: 0 0 15px rgba(83, 52, 131, 0.4);">
                            <?= strtoupper(substr(esc($profile['nama'] ?? session()->get('nama') ?? 'U'), 0, 1)) ?>
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge px-3 py-1 fw-bold" 
                                      style="background: rgba(13, 202, 240, 0.15); color: #00f2fe; border: 1px solid rgba(13, 202, 240, 0.35); font-size: 0.72rem; letter-spacing: 0.8px;">
                                    <i class="bi bi-shield-check me-1"></i>PORTAL KARYAWAN
                                </span>
                            </div>

                            <h3 class="fw-bolder mb-2 text-white" style="letter-spacing: -0.5px;">
                                Halo, <?= esc($profile['nama'] ?? session()->get('nama')) ?>! 👋
                            </h3>

                            <!-- Badges Informasi Karyawan (Estetik, Selaras Tema & Sangat Jelas) -->
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                <!-- NIK: Kartu Biru Muda (Info) -->
                                <div class="employee-meta-pill">
                                    <div class="icon-bubble" style="background: rgba(13, 202, 240, 0.22); border: 1px solid rgba(13, 202, 240, 0.6); color: #00f2fe;">
                                        <i class="bi bi-person-vcard-fill"></i>
                                    </div>
                                    <span style="color: rgba(255, 255, 255, 0.65); font-size: 0.8rem; font-weight: 500;">NIK:</span>
                                    <span class="text-white fw-bold font-monospace" style="font-size: 0.92rem; letter-spacing: 0.5px;"><?= esc($profile['nik'] ?? '-') ?></span>
                                </div>

                                <!-- Jabatan: Koper Kuning (Warning) -->
                                <div class="employee-meta-pill">
                                    <div class="icon-bubble" style="background: rgba(255, 193, 7, 0.22); border: 1px solid rgba(255, 193, 7, 0.6); color: #ffda6a;">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                    <span style="color: rgba(255, 255, 255, 0.65); font-size: 0.8rem; font-weight: 500;">Jabatan:</span>
                                    <span class="text-white fw-bold" style="font-size: 0.92rem;"><?= esc($profile['jabatan'] ?? '-') ?></span>
                                </div>

                                <!-- Departemen: Gedung Hijau (Success) -->
                                <div class="employee-meta-pill">
                                    <div class="icon-bubble" style="background: rgba(25, 135, 84, 0.28); border: 1px solid rgba(32, 201, 151, 0.6); color: #20c997;">
                                        <i class="bi bi-building-fill"></i>
                                    </div>
                                    <span style="color: rgba(255, 255, 255, 0.65); font-size: 0.8rem; font-weight: 500;">Departemen:</span>
                                    <span class="text-white fw-bold" style="font-size: 0.92rem;"><?= esc($profile['departemen'] ?? '-') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-md-end p-3 rounded-4" 
                         style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                        <div class="badge px-3 py-2 mb-1 shadow-sm font-monospace fs-6" 
                             style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                            <i class="bi bi-clock-fill text-warning me-1"></i> <span id="liveClock" class="fw-bold"><?= date('H:i:s') ?></span> WIB
                        </div>
                        <div class="text-white fw-semibold small mt-1" style="opacity: 0.9;">
                            <i class="bi bi-calendar3 me-1 text-info"></i> <?= date('l, d F Y') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Absen Hari Ini Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">Status Kehadiran Hari Ini</h5>
                        <p class="text-muted small mb-0">Jam Kerja: <?= substr($setting['jam_masuk'] ?? '08:00', 0, 5) ?> - <?= substr($setting['jam_keluar'] ?? '17:00', 0, 5) ?> WIB (Toleransi: <?= $setting['toleransi_menit'] ?? 15 ?> menit)</p>
                    </div>
                    <div class="mt-3 mt-md-0 d-flex gap-2">
                        <?php if (!$absenHariIni): ?>
                            <a href="/self-absen" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-camera me-1"></i> Absen Masuk Sekarang
                            </a>
                        <?php elseif ($absenHariIni && !$absenHariIni['jam_keluar']): ?>
                            <span class="badge bg-success-subtle text-success p-2 px-3 me-2 fs-6">
                                <i class="bi bi-check-circle me-1"></i> Masuk: <?= substr($absenHariIni['jam_masuk'], 0, 5) ?> WIB
                            </span>
                            <a href="/self-absen" class="btn btn-warning px-4 py-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Absen Pulang
                            </a>
                        <?php else: ?>
                            <span class="badge bg-success p-2 px-3 fs-6">
                                <i class="bi bi-check2-all me-1"></i> Selesai (Masuk: <?= substr($absenHariIni['jam_masuk'], 0, 5) ?> | Pulang: <?= substr($absenHariIni['jam_keluar'], 0, 5) ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Bulan Ini (Data Pribadi Karyawan) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Hadir Bulan Ini</div>
                    <div class="fs-4 fw-bold"><?= $totalHadirBulanIni ?> Hari</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Terlambat Bulan Ini</div>
                    <div class="fs-4 fw-bold"><?= $totalTerlambatBulanIni ?> Kali</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-clipboard-pulse fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Izin / Sakit</div>
                    <div class="fs-4 fw-bold"><?= $totalIzinSakitBulanIni ?> Hari</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Pengajuan Pending</div>
                    <div class="fs-4 fw-bold"><?= $pengajuanPending ?> Menunggu</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Absensi Pribadi Saja -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-1 text-primary"></i> 10 Riwayat Absensi Terakhir Anda
                </h6>
                <a href="/absensi" class="btn btn-sm btn-outline-primary">Lihat Semua Riwayat</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayatPribadi)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat absensi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riwayatPribadi as $r): ?>
                                    <tr>
                                        <td><strong><?= date('d M Y', strtotime($r['tanggal'])) ?></strong></td>
                                        <td><?= $r['jam_masuk'] ? substr($r['jam_masuk'], 0, 5) . ' WIB' : '-' ?></td>
                                        <td><?= $r['jam_keluar'] ? substr($r['jam_keluar'], 0, 5) . ' WIB' : '-' ?></td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'hadir'     => 'bg-success',
                                                'terlambat' => 'bg-warning text-dark',
                                                'izin'      => 'bg-info text-dark',
                                                'sakit'     => 'bg-secondary',
                                                'alpha'     => 'bg-danger',
                                            ];
                                            $badgeClass = $badges[$r['status']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= strtoupper($r['status']) ?></span>
                                        </td>
                                        <td><small class="text-muted"><?= esc($r['keterangan'] ?? '-') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const str = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClock');
        if (clockEl) clockEl.innerText = str;
    }
    setInterval(updateClock, 1000);
</script>

<?= $this->endSection() ?>

