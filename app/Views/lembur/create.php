<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-clock-history me-1"></i> Form Pengajuan Lembur (Overtime)
                </h6>
            </div>
            <div class="card-body p-4">

                <?php if ($validation): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="/lembur/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Lembur <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" required value="17:30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" required value="20:30">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Uraian Tugas Lembur <span class="text-danger">*</span></label>
                        <textarea name="tugas" rows="4" class="form-control" placeholder="Jelaskan pekerjaan atau target tugas yang dikerjakan saat lembur..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/lembur" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan Lembur
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

