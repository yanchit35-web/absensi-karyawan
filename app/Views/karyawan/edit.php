<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-fill text-warning me-2"></i>Edit Karyawan</h6>
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

                <form action="/karyawan/update/<?= $karyawan['id'] ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik"
                                value="<?= old('nik', $karyawan['nik']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama"
                                value="<?= old('nama', $karyawan['nama']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jabatan"
                                value="<?= old('jabatan', $karyawan['jabatan']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="departemen"
                                value="<?= old('departemen', $karyawan['departemen']) ?>" required list="dept-list">
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
                            <input type="email" class="form-control" name="email"
                                value="<?= old('email', $karyawan['email']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" class="form-control" name="telepon"
                                value="<?= old('telepon', $karyawan['telepon']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk"
                                value="<?= old('tgl_masuk', $karyawan['tgl_masuk']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="aktif" <?= old('status', $karyawan['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= old('status', $karyawan['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 pt-2">
                            <hr>
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-check-lg me-2"></i>Update Karyawan
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
