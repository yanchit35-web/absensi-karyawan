<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-calendar2-check-fill text-primary me-2"></i>
            <?= ($isKaryawan) ? 'Riwayat Kehadiran Pribadi Saya' : 'Rekap Absensi Seluruh Karyawan' ?>
        </h6>
        <?php if (!$isKaryawan): ?>
        <div class="d-flex gap-2">
            <a href="/absensi/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Input Absensi Manual
            </a>
        </div>
        <?php else: ?>
        <a href="/self-absen" class="btn btn-warning btn-sm fw-bold">
            <i class="bi bi-camera me-1"></i> Absen Hari Ini
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter -->
    <div class="card-body border-bottom">
        <form method="GET" action="/absensi" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Tanggal</label>
                <input type="date" class="form-control" name="tanggal" value="<?= esc($tanggal ?? '') ?>">
            </div>

            <?php if (!$isKaryawan): ?>
            <!-- Admin Filter Karyawan -->
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Karyawan</label>
                <select class="form-select" name="karyawan_id">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($karyawan as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($karyawan_id ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama']) ?> (<?= esc($k['departemen']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="<?= ($isKaryawan) ? 'col-md-4' : 'col-md-3' ?>">
                <label class="form-label mb-1 small fw-semibold">Status Kehadiran</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= ($status ?? '') === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="terlambat" <?= ($status ?? '') === 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                    <option value="izin" <?= ($status ?? '') === 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= ($status ?? '') === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpha" <?= ($status ?? '') === 'alpha' ? 'selected' : '' ?>>Alpha</option>
                </select>
            </div>
            
            <div class="<?= ($isKaryawan) ? 'col-md-5' : 'col-md-3' ?>">
                <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/absensi" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="40">#</th>
                        <th>Tanggal</th>
                        <?php if (!$isKaryawan): ?>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Departemen</th>
                        <?php endif; ?>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Foto / GPS</th>
                        <th>Keterangan</th>
                        <?php if (!$isKaryawan): ?>
                            <th width="90">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($absensi)): ?>
                    <tr>
                        <td colspan="<?= ($isKaryawan) ? '7' : '10' ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            Tidak ada catatan kehadiran.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($absensi as $i => $row): ?>
                    <?php
                        $badgeMap = ['hadir'=>'success','terlambat'=>'warning text-dark','izin'=>'info text-dark','sakit'=>'secondary','alpha'=>'danger'];
                        $badge = $badgeMap[$row['status']] ?? 'secondary';
                    ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><strong><?= date('d/m/Y', strtotime($row['tanggal'])) ?></strong></td>
                        <?php if (!$isKaryawan): ?>
                            <td><code><?= esc($row['nik']) ?></code></td>
                            <td class="fw-semibold"><?= esc($row['nama']) ?></td>
                            <td><?= esc($row['departemen']) ?></td>
                        <?php endif; ?>
                        <td><?= $row['jam_masuk'] ? '<span class="text-success fw-semibold">'.substr($row['jam_masuk'],0,5).' WIB</span>' : '<span class="text-muted">-</span>' ?></td>
                        <td><?= $row['jam_keluar'] ? '<span class="text-danger fw-semibold">'.substr($row['jam_keluar'],0,5).' WIB</span>' : '<span class="text-muted">-</span>' ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= strtoupper($row['status']) ?></span></td>
                        <td>
                            <?php if (!empty($row['foto'])): ?>
                                <a href="/uploads/absensi/<?= esc($row['foto']) ?>" target="_blank" class="badge bg-light text-dark border me-1 text-decoration-none">
                                    <i class="bi bi-camera"></i> Foto
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($row['latitude']) && !empty($row['longitude'])): ?>
                                <a href="https://maps.google.com/?q=<?= esc($row['latitude']) ?>,<?= esc($row['longitude']) ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                    <i class="bi bi-geo-alt"></i> Peta
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= esc($row['keterangan'] ?? '-') ?></td>
                        <?php if (!$isKaryawan): ?>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/absensi/edit/<?= $row['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="/absensi/delete/<?= $row['id'] ?>" class="btn btn-outline-danger" title="Hapus"
                                   onclick="return confirm('Hapus data absensi ini?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
