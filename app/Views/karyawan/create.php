<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Karyawan</h6>
                <a href="/karyawan" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if ($validation): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Terdapat kesalahan validasi:</strong>
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="/karyawan/store" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik" value="<?= old('nik') ?>"
                                placeholder="Nomor Induk Karyawan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" value="<?= old('nama') ?>"
                                placeholder="Nama lengkap karyawan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jabatan" value="<?= old('jabatan') ?>"
                                placeholder="Jabatan / posisi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="departemen" value="<?= old('departemen') ?>"
                                placeholder="Nama departemen" required list="dept-list">
                            <datalist id="dept-list">
                                <option value="IT">
                                <option value="HR">
                                <option value="Finance">
                                <option value="Marketing">
                                <option value="Operations">
                                <option value="Sales">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= old('email') ?>"
                                placeholder="Alamat email (opsional)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" class="form-control" name="telepon" value="<?= old('telepon') ?>"
                                placeholder="Nomor telepon (opsional)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" value="<?= old('tgl_masuk') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="aktif" <?= old('status') !== 'nonaktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= old('status') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 pt-2">
                            <hr>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>Simpan Karyawan
                            </button>
                            <a href="/karyawan" class="btn btn-outline-secondary ms-2">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
