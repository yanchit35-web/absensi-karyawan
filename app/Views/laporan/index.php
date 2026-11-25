<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter Form -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-funnel-fill text-primary me-2"></i>
            <?= ($isKaryawan) ? 'Filter Laporan Kehadiran Saya' : 'Filter Laporan Kehadiran Seluruh Karyawan' ?>
        </h6>
    </div>
    <div class="card-body">
        <form action="/laporan/generate" method="POST" class="row g-3 align-items-end">
            <?= csrf_field() ?>
            <div class="<?= ($isKaryawan) ? 'col-md-5' : 'col-md-2' ?>">
                <label class="form-label fw-semibold">Bulan</label>
                <select class="form-select" name="bulan">
                    <?php
                    $bulanNames = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                                   '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($bulanNames as $num => $name):
                        $numStr = str_pad((string)$num, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?= $numStr ?>" <?= str_pad((string)($bulan ?? date('m')), 2, '0', STR_PAD_LEFT) === $numStr ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="<?= ($isKaryawan) ? 'col-md-4' : 'col-md-2' ?>">
                <label class="form-label fw-semibold">Tahun</label>
                <select class="form-select" name="tahun">
                    <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= ($tahun ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <?php if (!$isKaryawan): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Karyawan (opsional)</label>
                <select class="form-select" name="karyawan_id">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($karyawan as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($karyawan_id ?? '') == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Departemen (opsional)</label>
                <select class="form-select" name="departemen">
                    <option value="">Semua Departemen</option>
                    <?php foreach ($departemens as $dep): ?>
                    <option value="<?= esc($dep['departemen']) ?>" <?= ($departemen ?? '') === $dep['departemen'] ? 'selected' : '' ?>>
                        <?= esc($dep['departemen']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="<?= ($isKaryawan) ? 'col-md-3' : 'col-md-2' ?>">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-table text-primary me-2"></i>
            <?= ($isKaryawan) ? 'Rekap Kehadiran Saya' : 'Hasil Rekap Laporan' ?>
            <?php if (!empty($absensi)): ?>
            <span class="badge bg-primary ms-1"><?= count($absensi) ?> data kehadiran</span>
            <?php endif; ?>
        </h6>

        <?php if (!empty($absensi)): ?>
        <div class="d-flex gap-2">
            <?php
            $qParams = http_build_query([
                'bulan'       => $bulan,
                'tahun'       => $tahun,
                'karyawan_id' => ($isKaryawan) ? '' : ($karyawan_id ?? ''),
                'departemen'  => ($isKaryawan) ? '' : ($departemen ?? ''),
            ]);
            ?>
            <a href="/laporan/export-pdf?<?= $qParams ?>" class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-pdf-fill me-1"></i> Export PDF
            </a>
            <a href="/laporan/export-excel?<?= $qParams ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel-fill me-1"></i> Export Excel
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <?php if (empty($absensi)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-file-earmark-bar-graph fs-1 d-block mb-2 opacity-25"></i>
                <p class="mb-0">Pilih bulan dan tahun lalu klik "Tampilkan" untuk melihat rekap laporan</p>
            </div>
        <?php else: ?>
            <!-- Statistik Singkat Hasil Laporan -->
            <?php
                $statHadir     = 0;
                $statTerlambat = 0;
                $statIzin      = 0;
                $statSakit     = 0;
                $statAlpha     = 0;
                foreach ($absensi as $item) {
                    if ($item['status'] === 'hadir') $statHadir++;
                    elseif ($item['status'] === 'terlambat') $statTerlambat++;
                    elseif ($item['status'] === 'izin') $statIzin++;
                    elseif ($item['status'] === 'sakit') $statSakit++;
                    elseif ($item['status'] === 'alpha') $statAlpha++;
                }
            ?>
            <div class="p-3 bg-light border-bottom d-flex flex-wrap gap-3">
                <span class="badge bg-success px-3 py-2">Hadir: <?= $statHadir ?></span>
                <span class="badge bg-warning text-dark px-3 py-2">Terlambat: <?= $statTerlambat ?></span>
                <span class="badge bg-info text-dark px-3 py-2">Izin: <?= $statIzin ?></span>
                <span class="badge bg-secondary px-3 py-2">Sakit: <?= $statSakit ?></span>
                <span class="badge bg-danger px-3 py-2">Alpha: <?= $statAlpha ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">#</th>
                            <?php if (!$isKaryawan): ?>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen</th>
                            <?php endif; ?>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absensi as $i => $row): ?>
                        <?php
                            $badgeMap = ['hadir'=>'success','terlambat'=>'warning text-dark','izin'=>'info text-dark','sakit'=>'secondary','alpha'=>'danger'];
                            $badge = $badgeMap[$row['status']] ?? 'secondary';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <?php if (!$isKaryawan): ?>
                                <td><code><?= esc($row['nik']) ?></code></td>
                                <td class="fw-semibold"><?= esc($row['nama']) ?></td>
                                <td><?= esc($row['departemen']) ?></td>
                            <?php endif; ?>
                            <td><strong><?= date('d/m/Y', strtotime($row['tanggal'])) ?></strong></td>
                            <td><?= $row['jam_masuk'] ? substr($row['jam_masuk'], 0, 5) . ' WIB' : '-' ?></td>
                            <td><?= $row['jam_keluar'] ? substr($row['jam_keluar'], 0, 5) . ' WIB' : '-' ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= strtoupper($row['status']) ?></span></td>
                            <td class="text-muted small"><?= esc($row['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
