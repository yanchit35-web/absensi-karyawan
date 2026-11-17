<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-fill text-warning me-2"></i>Edit Absensi</h6>
                <a href="/absensi" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if ($validation): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="/absensi/update/<?= $absensi['id'] ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php foreach ($karyawan as $k): ?>
                                    <option value="<?= $k['id'] ?>"
                                        <?= old('karyawan_id', $absensi['karyawan_id']) == $k['id'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama']) ?> (<?= esc($k['nik']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal"
                                value="<?= old('tanggal', $absensi['tanggal']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Masuk</label>
                            <input type="time" class="form-control" name="jam_masuk"
                                value="<?= old('jam_masuk', $absensi['jam_masuk'] ? substr($absensi['jam_masuk'], 0, 5) : '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Keluar</label>
                            <input type="time" class="form-control" name="jam_keluar"
                                value="<?= old('jam_keluar', $absensi['jam_keluar'] ? substr($absensi['jam_keluar'], 0, 5) : '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                <?php
                                $statuses = ['hadir' => '✅ Hadir', 'terlambat' => '⏰ Terlambat', 'izin' => '📝 Izin', 'sakit' => '🏥 Sakit', 'alpha' => '❌ Alpha'];
                                foreach ($statuses as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= old('status', $absensi['status']) === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2"><?= old('keterangan', $absensi['keterangan']) ?></textarea>
                        </div>
                        <div class="col-12 pt-2">
                            <hr>
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-check-lg me-2"></i>Update Absensi
                            </button>
                            <a href="/absensi" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
