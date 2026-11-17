<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-fill text-warning me-2"></i>Edit Data Karyawan & Akun Login</h6>
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
                    
                    <!-- Bagian 1: Data Pribadi & Kepegawaian -->
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-badge fs-5 text-primary"></i>
                        <span class="fw-bold text-dark">Data Kepegawaian</span>
                    </div>

                    <div class="row g-3 mb-4">
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
                            <label class="form-label fw-semibold">Shift Kerja <span class="text-danger">*</span></label>
                            <select class="form-select fw-semibold" name="shift" required>
                                <option value="pagi" <?= old('shift', $karyawan['shift'] ?? 'pagi') === 'pagi' ? 'selected' : '' ?>>☀️ Shift Pagi</option>
                                <option value="siang" <?= old('shift', $karyawan['shift'] ?? 'pagi') === 'siang' ? 'selected' : '' ?>>⛅ Shift Siang</option>
                                <option value="malam" <?= old('shift', $karyawan['shift'] ?? 'pagi') === 'malam' ? 'selected' : '' ?>>🌙 Shift Malam</option>
                            </select>
                            <small class="text-muted">Jam kerja otomatis mengikuti shift yang dipilih.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="aktif" <?= old('status', $karyawan['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= old('status', $karyawan['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bagian 2: Kelola Akun Login Karyawan -->
                    <div class="card bg-light border-warning-subtle p-3 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                            <span class="fw-bold text-dark">Akun Login Karyawan</span>
                            <?php if ($user): ?>
                                <span class="badge bg-success ms-auto"><i class="bi bi-check-circle me-1"></i>Akun Terhubung</span>
                            <?php else: ?>
                                <span class="badge bg-secondary ms-auto">Belum Ada Akun (Akan Dibuat)</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-3">
                            Administrator dapat mengubah username dan mengatur ulang (reset) password login karyawan ini kapan saja.
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username Login <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-fill text-muted"></i></span>
                                    <input type="text" class="form-control font-monospace fw-bold" name="username"
                                        value="<?= old('username', $suggestedUsername) ?>" required minlength="3" maxlength="100">
                                </div>
                                <small class="text-muted">Username unik untuk login karyawan.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key-fill text-muted"></i></span>
                                    <input type="password" class="form-control font-monospace" name="password" id="inputEditPassword"
                                        placeholder="Kosongkan jika tidak ingin diubah" minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleEditPassword()">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Isi hanya jika ingin mereset password (minimal 6 karakter).</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 pt-2">
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan & Akun
                        </button>
                        <a href="/karyawan" class="btn btn-outline-secondary ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEditPassword() {
    const input = document.getElementById('inputEditPassword');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?= $this->endSection() ?>
