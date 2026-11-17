<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Karyawan & Akun Login</h6>
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
                    
                    <!-- Bagian 1: Data Pribadi & Kepegawaian -->
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-badge fs-5 text-primary"></i>
                        <span class="fw-bold text-dark">Data Kepegawaian</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik" value="<?= old('nik', $nextNik ?? '') ?>"
                                placeholder="Nomor Induk Karyawan" required>
                            <small class="text-muted">Format otomatis: <code><?= esc($nextNik ?? '') ?></code></small>
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
                    </div>

                    <!-- Bagian 2: Akun Login Karyawan (Otomatis) -->
                    <div class="card bg-light border-primary-subtle p-3 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark">Akun Login Karyawan (Otomatis Terbuat)</span>
                            <span class="badge bg-primary ms-auto">Login Otomatis</span>
                        </div>
                        <p class="text-muted small mb-3">
                            Akun user untuk login ke aplikasi akan otomatis dibuat bersamaan dengan penambahan data karyawan ini.
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-fill text-muted"></i></span>
                                    <input type="text" class="form-control font-monospace fw-bold" name="username" 
                                        value="<?= old('username', $nextUsername ?? 'emp006') ?>" required>
                                </div>
                                <small class="text-muted">Otomatis berurutan (misal: <code><?= esc($nextUsername ?? 'emp006') ?></code>).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password Awal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key-fill text-muted"></i></span>
                                    <input type="text" class="form-control font-monospace" name="password" id="inputPassword"
                                        value="<?= old('password', 'password123') ?>" required minlength="6">
                                </div>
                                <small class="text-muted">Default: <code>password123</code> (dapat diubah jika diinginkan).</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Simpan & Buat Akun
                        </button>
                        <a href="/karyawan" class="btn btn-outline-secondary ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
