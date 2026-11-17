<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-clipboard2-check-fill text-primary me-2"></i>
            <?= ($isKaryawan) ? 'Riwayat Pengajuan Cuti, Izin & Sakit Saya' : 'Persetujuan Pengajuan Cuti, Izin & Sakit Karyawan' ?>
        </h6>
        <?php if ($isKaryawan): ?>
        <a href="/pengajuan/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Cuti / Izin Baru
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter Status -->
    <div class="card-body border-bottom">
        <form method="GET" action="/pengajuan" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status Pengajuan</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Menunggu Persetujuan (Pending)</option>
                    <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>Disetujui (Approved)</option>
                    <option value="rejected" <?= ($status === 'rejected') ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/pengajuan" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Reset</a>
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
                        <th>Jenis</th>
                        <th>Rentang Tanggal</th>
                        <th>Keterangan / Alasan</th>
                        <th>Bukti / Surat</th>
                        <th>Status</th>
                        <?php if (!$isKaryawan): ?>
                        <th width="180">Tindakan Admin</th>
                        <?php else: ?>
                        <th>Catatan Admin</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pengajuan)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                            Belum ada data pengajuan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pengajuan as $i => $p): ?>
                    <?php
                        $badgeStatus = [
                            'pending'  => 'bg-warning text-dark',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                        ];
                        $badgeClass = $badgeStatus[$p['status']] ?? 'bg-secondary';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <?php if (!$isKaryawan): ?>
                        <td>
                            <strong><?= esc($p['nama']) ?></strong>
                            <div class="small text-muted">NIK: <?= esc($p['nik']) ?></div>
                        </td>
                        <td><?= esc($p['departemen']) ?></td>
                        <?php endif; ?>
                        <td>
                            <span class="badge bg-info text-dark px-2 py-1"><?= strtoupper($p['jenis']) ?></span>
                        </td>
                        <td>
                            <strong><?= date('d/m/Y', strtotime($p['tgl_mulai'])) ?></strong>
                            s/d
                            <strong><?= date('d/m/Y', strtotime($p['tgl_selesai'])) ?></strong>
                        </td>
                        <td><?= esc($p['keterangan']) ?></td>
                        <td>
                            <?php if (!empty($p['file_surat'])): ?>
                            <a href="/uploads/surat/<?= esc($p['file_surat']) ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                <i class="bi bi-paperclip"></i> Lihat Berkas
                            </a>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= strtoupper($p['status']) ?></span>
                        </td>

                        <?php if (!$isKaryawan): ?>
                        <td>
                            <?php if ($p['status'] === 'pending'): ?>
                            <div class="d-flex gap-1">
                                <form action="/pengajuan/approve/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Setujui pengajuan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                        <i class="bi bi-check-lg"></i> Terima
                                    </button>
                                </form>
                                <form action="/pengajuan/reject/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Tolak pengajuan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <small class="text-muted"><?= esc($p['catatan_admin'] ?? '-') ?></small>
                            <?php endif; ?>
                        </td>
                        <?php else: ?>
                        <td><small class="text-muted"><?= esc($p['catatan_admin'] ?? '-') ?></small></td>
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

