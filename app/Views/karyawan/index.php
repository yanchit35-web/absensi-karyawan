<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Data Karyawan</h6>
        <a href="/karyawan/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Karyawan
        </a>
    </div>

    <!-- Filter -->
    <div class="card-body border-bottom">
        <form method="GET" action="/karyawan" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Nama / NIK / Jabatan..."
                        value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Departemen</label>
                <select class="form-select" name="departemen">
                    <option value="">Semua Departemen</option>
                    <?php foreach ($departemens as $dep): ?>
                        <option value="<?= esc($dep['departemen']) ?>"
                            <?= ($departemen ?? '') === $dep['departemen'] ? 'selected' : '' ?>>
                            <?= esc($dep['departemen']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small fw-semibold">Shift Kerja</label>
                <select class="form-select" name="shift">
                    <option value="">Semua Shift</option>
                    <option value="pagi" <?= ($shift ?? '') === 'pagi' ? 'selected' : '' ?>>☀️ Pagi</option>
                    <option value="siang" <?= ($shift ?? '') === 'siang' ? 'selected' : '' ?>>⛅ Siang</option>
                    <option value="malam" <?= ($shift ?? '') === 'malam' ? 'selected' : '' ?>>🌙 Malam</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/karyawan" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="40">#</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Username Login</th>
                        <th>Shift Kerja</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Telepon</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($karyawan)): ?>
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            Tidak ada data karyawan
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($karyawan as $i => $k): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><code><?= esc($k['nik']) ?></code></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0f3460,#533483);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                    <?= strtoupper(substr($k['nama'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= esc($k['nama']) ?></div>
                                    <div class="text-muted small"><?= esc($k['email'] ?? '-') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($k['username'])): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-monospace">
                                    <i class="bi bi-person-fill me-1"></i><?= esc($k['username']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">Belum ada akun</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($k['shift'] ?? 'pagi') === 'siang'): ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">⛅ Siang</span>
                            <?php elseif (($k['shift'] ?? 'pagi') === 'malam'): ?>
                                <span class="badge bg-dark text-white px-2 py-1">🌙 Malam</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">☀️ Pagi</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($k['jabatan']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= esc($k['departemen']) ?></span></td>
                        <td><?= esc($k['telepon'] ?? '-') ?></td>
                        <td><?= $k['tgl_masuk'] ? date('d/m/Y', strtotime($k['tgl_masuk'])) : '-' ?></td>
                        <td>
                            <?php if ($k['status'] === 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/karyawan/edit/<?= $k['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="/karyawan/delete/<?= $k['id'] ?>" class="btn btn-outline-danger"
                                    title="Hapus"
                                    onclick="return confirm('Yakin ingin menghapus karyawan ini? Data absensi terkait juga akan terhapus.')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white text-muted small">
        Total: <strong><?= count($karyawan) ?></strong> karyawan
    </div>
</div>

<?= $this->endSection() ?>
