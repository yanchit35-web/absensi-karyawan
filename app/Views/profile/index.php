<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Kolom 1: Informasi Profil Akun -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #0f3460, #533483); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 2rem; box-shadow: 0 4px 15px rgba(15, 52, 96, 0.3);">
                    <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= esc($user['nama']) ?></h5>
                <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'success' ?> px-3 py-1 mb-3">
                    <i class="bi bi-shield-check me-1"></i><?= ucfirst($user['role']) ?>
                </span>

                <div class="list-group list-group-flush text-start small border-top pt-2">
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-person me-2"></i>Username</span>
                        <code class="fw-bold text-dark fs-6"><?= esc($user['username']) ?></code>
                    </div>
                    <?php if ($karyawan): ?>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-card-text me-2"></i>NIK</span>
                        <span class="fw-semibold"><?= esc($karyawan['nik']) ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-briefcase me-2"></i>Jabatan</span>
                        <span class="fw-semibold"><?= esc($karyawan['jabatan']) ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-building me-2"></i>Departemen</span>
                        <span class="badge bg-light text-dark border"><?= esc($karyawan['departemen']) ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-envelope me-2"></i>Email</span>
                        <span><?= esc($karyawan['email'] ?? '-') ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-telephone me-2"></i>Telepon</span>
                        <span><?= esc($karyawan['telepon'] ?? '-') ?></span>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Tgl Masuk</span>
                        <span><?= $karyawan['tgl_masuk'] ? date('d/m/Y', strtotime($karyawan['tgl_masuk'])) : '-' ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom 2: Form Ganti Password & Informasi Username -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-key-fill text-primary me-2"></i>Pengaturan Akun & Ganti Password
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if ($validation): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <!-- Bagian Username (Read-Only / Terkunci untuk Karyawan) -->
                <div class="card bg-light border p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark">
                            <i class="bi bi-person-fill text-primary me-1"></i>Username Akun
                        </label>
                        <span class="badge bg-secondary-subtle text-secondary border">
                            <i class="bi bi-lock-fill me-1"></i>Terkunci (Hanya Baca)
                        </span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control bg-white font-monospace fw-bold" 
                            value="<?= esc($user['username']) ?>" readonly disabled>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        <strong>Catatan:</strong> Username Anda tidak dapat diubah secara mandiri. Perubahan username hanya dapat dilakukan oleh <strong>Administrator</strong>.
                    </small>
                </div>

                <!-- Form Ganti Password -->
                <form action="/profil/update-password" method="POST">
                    <?= csrf_field() ?>
                    
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-shield-lock me-1 text-primary"></i>Ganti Password
                    </h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Saat Ini (Lama) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" class="form-control" name="password_lama" id="passLama"
                                placeholder="Masukkan password yang saat ini digunakan" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passLama', 'iconLama')">
                                <i class="bi bi-eye" id="iconLama"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" class="form-control" name="password_baru" id="passBaru"
                                    placeholder="Minimal 6 karakter" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passBaru', 'iconBaru')">
                                    <i class="bi bi-eye" id="iconBaru"></i>
                                </button>
                            </div>
                            <small class="text-muted">Gunakan kombinasi yang aman & mudah diingat.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-check2-circle text-muted"></i></span>
                                <input type="password" class="form-control" name="konfirmasi_password" id="passKonf"
                                    placeholder="Ulangi password baru" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passKonf', 'iconKonf')">
                                    <i class="bi bi-eye" id="iconKonf"></i>
                                </button>
                            </div>
                            <small class="text-muted">Harus sama dengan password baru di samping.</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Simpan Password Baru
                        </button>
                        <a href="/dashboard" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
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
