<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-pencil-square me-1"></i> Form Pengajuan Cuti / Izin / Sakit
                </h6>
            </div>
            <div class="card-body p-4">

                <?php if ($validation): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="/pengajuan/store" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Pengajuan <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis Pengajuan --</option>
                            <option value="cuti">Cuti Tahunan / Khusus</option>
                            <option value="izin">Izin Kepentingan Pribadi / Keluarga</option>
                            <option value="sakit">Sakit (Lampirkan Surat Dokter)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_mulai" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_selesai" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan / Keterangan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" rows="3" class="form-control" placeholder="Jelaskan keperluan atau keterangan cuti/izin Anda..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Surat Dokter / Berkas Pendukung (Opsional)</label>
                        <input type="file" name="file_surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text small text-muted">Format: PDF, JPG, PNG (Maksimal 3 MB)</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/pengajuan" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

