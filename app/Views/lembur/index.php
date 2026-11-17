<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-clock-history text-primary me-2"></i>
            <?= ($isKaryawan) ? 'Riwayat Pengajuan Lembur Saya' : 'Persetujuan Pengajuan Lembur Karyawan' ?>
        </h6>
        <?php if ($isKaryawan): ?>
        <a href="/lembur/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Lembur Baru
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter Status -->
    <div class="card-body border-bottom">
        <form method="GET" action="/lembur" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status Lembur</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                    <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>Disetujui (Approved)</option>
                    <option value="rejected" <?= ($status === 'rejected') ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/lembur" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="40">#</th>
                        <?php if (!$isKaryawan): ?>
                        <th>Karyawan</th>
                        <th>Departemen</th>
                        <?php endif; ?>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Uraian Tugas Lembur</th>
                        <th>Status</th>
                        <?php if (!$isKaryawan): ?>
                        <th width="180">Tindakan Admin</th>
                        <?php else: ?>
                        <th>Catatan Admin</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lembur)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                            Belum ada permohonan lembur.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($lembur as $i => $l): ?>
                    <?php
                        $badgeStatus = [
                            'pending'  => 'bg-warning text-dark',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                        ];
                        $badgeClass = $badgeStatus[$l['status']] ?? 'bg-secondary';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <?php if (!$isKaryawan): ?>
                        <td>
                            <strong><?= esc($l['nama']) ?></strong>
                            <div class="small text-muted">NIK: <?= esc($l['nik']) ?></div>
                        </td>
                        <td><?= esc($l['departemen']) ?></td>
                        <?php endif; ?>
                        <td><strong><?= date('d/m/Y', strtotime($l['tanggal'])) ?></strong></td>
                        <td><?= substr($l['jam_mulai'], 0, 5) ?> WIB</td>
                        <td><?= substr($l['jam_selesai'], 0, 5) ?> WIB</td>
                        <td><?= esc($l['tugas']) ?></td>
                        <td>
                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= strtoupper($l['status']) ?></span>
                        </td>

                        <?php if (!$isKaryawan): ?>
                        <td>
                            <?php if ($l['status'] === 'pending'): ?>
                            <div class="d-flex gap-1">
                                <form action="/lembur/approve/<?= $l['id'] ?>" method="POST" onsubmit="return confirm('Setujui lembur ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                        <i class="bi bi-check-lg"></i> Terima
                                    </button>
                                </form>
                                <form action="/lembur/reject/<?= $l['id'] ?>" method="POST" onsubmit="return confirm('Tolak lembur ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <small class="text-muted"><?= esc($l['catatan_admin'] ?? '-') ?></small>
                            <?php endif; ?>
                        </td>
                        <?php else: ?>
                        <td><small class="text-muted"><?= esc($l['catatan_admin'] ?? '-') ?></small></td>
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

