<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-clock-history me-2"></i> Pengaturan Jam Kerja, Shift & Titik Kantor
                </h6>
                <span class="badge bg-primary-subtle text-primary border px-3 py-1">Zona Waktu: WIB (Asia/Jakarta)</span>
            </div>
            <div class="card-body p-4">

                <form action="/pengaturan/update" method="POST">
                    <?= csrf_field() ?>

                    <!-- Bagian 1: Perusahaan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-building me-1 text-primary"></i> Informasi Perusahaan</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Perusahaan / Instansi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" value="<?= esc($setting['nama_perusahaan'] ?? '') ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Bagian 2: Manajemen Shift Kerja (Pagi, Siang, Malam) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-sun-fill text-warning me-1"></i> Manajemen Jam Kerja Shift (Pagi, Siang, Malam)</h6>
                                <p class="text-muted small mb-0">Atur jam masuk dan jam keluar untuk setiap shift. Anda dapat menerapkan jam shift ke jam utama dengan 1-klik.</p>
                            </div>
                        </div>

                        <!-- 3 Kartu Pengaturan Shift -->
                        <div class="row g-3 my-2">
                            <!-- Shift Pagi -->
                            <div class="col-md-4">
                                <div class="card border border-warning-subtle shadow-sm h-100 bg-warning-subtle bg-opacity-10">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark"><i class="bi bi-brightness-high-fill text-warning me-1"></i> Shift Pagi</span>
                                            <span class="badge bg-warning text-dark">Pagi</span>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Jam Masuk</label>
                                            <input type="time" step="1" name="shift_pagi_masuk" id="shiftPagiMasuk" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_pagi_masuk'] ?? '08:00:00') ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold mb-1">Jam Pulang</label>
                                            <input type="time" step="1" name="shift_pagi_keluar" id="shiftPagiKeluar" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_pagi_keluar'] ?? '16:00:00') ?>" required>
                                        </div>
                                        <button type="button" class="btn btn-warning btn-sm w-100 text-dark fw-semibold" onclick="applyShiftPreset('pagi')">
                                            <i class="bi bi-check2-circle me-1"></i> Gunakan Jam Ini
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Shift Siang -->
                            <div class="col-md-4">
                                <div class="card border border-info-subtle shadow-sm h-100 bg-info-subtle bg-opacity-10">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark"><i class="bi bi-cloud-sun-fill text-info me-1"></i> Shift Siang</span>
                                            <span class="badge bg-info text-dark">Siang</span>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Jam Masuk</label>
                                            <input type="time" step="1" name="shift_siang_masuk" id="shiftSiangMasuk" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_siang_masuk'] ?? '14:00:00') ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold mb-1">Jam Pulang</label>
                                            <input type="time" step="1" name="shift_siang_keluar" id="shiftSiangKeluar" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_siang_keluar'] ?? '22:00:00') ?>" required>
                                        </div>
                                        <button type="button" class="btn btn-info btn-sm w-100 text-dark fw-semibold" onclick="applyShiftPreset('siang')">
                                            <i class="bi bi-check2-circle me-1"></i> Gunakan Jam Ini
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Shift Malam -->
                            <div class="col-md-4">
                                <div class="card border border-secondary shadow-sm h-100 bg-dark-subtle bg-opacity-10">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark"><i class="bi bi-moon-stars-fill text-primary me-1"></i> Shift Malam</span>
                                            <span class="badge bg-dark text-white">Malam</span>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Jam Masuk</label>
                                            <input type="time" step="1" name="shift_malam_masuk" id="shiftMalamMasuk" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_malam_masuk'] ?? '22:00:00') ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold mb-1">Jam Pulang</label>
                                            <input type="time" step="1" name="shift_malam_keluar" id="shiftMalamKeluar" class="form-control form-control-sm font-monospace fw-bold" 
                                                value="<?= esc($setting['shift_malam_keluar'] ?? '06:00:00') ?>" required>
                                        </div>
                                        <button type="button" class="btn btn-dark btn-sm w-100 fw-semibold" onclick="applyShiftPreset('malam')">
                                            <i class="bi bi-check2-circle me-1"></i> Gunakan Jam Ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Jam Kerja Operasional Utama & Toleransi -->
                        <div class="card bg-light border p-3 mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i> Jam Operasional Aktif (Default)</span>
                                <span id="activeShiftBadge" class="badge bg-primary">Shift Aktif</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Jam Masuk Utama <span class="text-danger">*</span></label>
                                    <input type="time" step="1" name="jam_masuk" id="jamMasukInput" class="form-control font-monospace fw-bold fs-6" 
                                        value="<?= esc($setting['jam_masuk'] ?? '08:00:00') ?>" required>
                                    <div class="form-text small">Waktu standar masuk kantor</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Jam Pulang Utama <span class="text-danger">*</span></label>
                                    <input type="time" step="1" name="jam_keluar" id="jamKeluarInput" class="form-control font-monospace fw-bold fs-6" 
                                        value="<?= esc($setting['jam_keluar'] ?? '17:00:00') ?>" required>
                                    <div class="form-text small">Waktu selesai jam operasional</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Toleransi Keterlambatan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="toleransi_menit" id="toleransiInput" class="form-control font-monospace fw-bold" 
                                            value="<?= esc($setting['toleransi_menit'] ?? '15') ?>" min="0" max="120" required>
                                        <span class="input-group-text">Menit</span>
                                    </div>
                                    <div class="form-text small text-primary fw-semibold" id="toleransiPreview">Batas toleransi: 08:15 WIB</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Bagian 3: Titik Koordinat & Geofencing Map -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Titik Lokasi Kantor & Radius Geofencing</h6>
                                <p class="text-muted small mb-0">Klik atau geser marker pin merah pada peta di bawah untuk menentukan titik lokasi kantor secara presisi.</p>
                            </div>
                            <button type="button" id="btnDetectLokasi" class="btn btn-sm btn-outline-primary" onclick="detectCurrentLocation()">
                                <i class="bi bi-crosshair me-1"></i> Deteksi Lokasi Saya Saat Ini
                            </button>
                        </div>

                        <!-- Peta Interaktif Leaflet -->
                        <div id="adminMap" style="height: 350px; width: 100%; border-radius: 12px; border: 2px solid #dee2e6; z-index: 1;" class="shadow-sm my-3"></div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Latitude Kantor <span class="text-danger">*</span></label>
                                <input type="text" name="kantor_lat" id="latInput" class="form-control font-monospace" value="<?= esc($setting['kantor_lat'] ?? '-6.200000') ?>" required readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Longitude Kantor <span class="text-danger">*</span></label>
                                <input type="text" name="kantor_lng" id="lngInput" class="form-control font-monospace" value="<?= esc($setting['kantor_lng'] ?? '106.816666') ?>" required readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Radius Toleransi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="kantor_radius" id="radiusInput" class="form-control" value="<?= esc($setting['kantor_radius'] ?? '500') ?>" min="10" max="5000" required>
                                    <span class="input-group-text">Meter</span>
                                </div>
                                <div class="form-text small">Batas jarak maksimal karyawan dapat absen</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-3 fw-bold fs-6 shadow-sm">
                            <i class="bi bi-save me-2"></i> SIMPAN SEMUA PENGATURAN JAM SHIFT & KANTOR
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // Inisialisasi data peta dari database
    const initialLat    = parseFloat("<?= $setting['kantor_lat'] ?? -6.200000 ?>");
    const initialLng    = parseFloat("<?= $setting['kantor_lng'] ?? 106.816666 ?>");
    const initialRadius = parseInt("<?= $setting['kantor_radius'] ?? 500 ?>");

    // Inisialisasi Peta Leaflet
    const map = L.map('adminMap').setView([initialLat, initialLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Marker Kantor (Draggable)
    const marker = L.marker([initialLat, initialLng], {
        draggable: true,
        title: "Geser marker ini untuk memindahkan lokasi kantor"
    }).addTo(map);

    marker.bindPopup("<b>Titik Kantor</b><br>Geser marker ini untuk mengubah posisi kantor.").openPopup();

    // Circle Radius Geofence
    const radiusCircle = L.circle([initialLat, initialLng], {
        color: '#dc3545',
        fillColor: '#dc3545',
        fillOpacity: 0.15,
        radius: initialRadius,
        weight: 2
    }).addTo(map);

    // Update form input saat marker digeser
    function updateLocation(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);
        marker.setLatLng([lat, lng]);
        radiusCircle.setLatLng([lat, lng]);
    }

    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        updateLocation(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        updateLocation(e.latlng.lat, e.latlng.lng);
    });

    // Radius input change event
    const radiusInput = document.getElementById('radiusInput');
    radiusInput.addEventListener('input', function() {
        const rad = parseInt(this.value) || 50;
        radiusCircle.setRadius(rad);
    });

    // Deteksi Lokasi Saya Saat Ini
    function detectCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
            return;
        }

        const btn = document.getElementById('btnDetectLokasi');
        const originalText = '<i class="bi bi-crosshair me-1"></i> Deteksi Lokasi Saya Saat Ini';
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mendeteksi...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                updateLocation(lat, lng);
                map.flyTo([lat, lng], 17);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('✓ Lokasi kantor berhasil disesuaikan dengan posisi GPS Anda saat ini!');
            },
            function(err) {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Gagal mendeteksi lokasi GPS: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    // Fungsi Terapkan Preset Shift (Pagi, Siang, Malam)
    function applyShiftPreset(shiftType) {
        let masukVal = '';
        let keluarVal = '';
        let badgeText = '';

        if (shiftType === 'pagi') {
            masukVal = document.getElementById('shiftPagiMasuk').value;
            keluarVal = document.getElementById('shiftPagiKeluar').value;
            badgeText = 'Shift Pagi (' + masukVal.substr(0, 5) + ' - ' + keluarVal.substr(0, 5) + ' WIB)';
        } else if (shiftType === 'siang') {
            masukVal = document.getElementById('shiftSiangMasuk').value;
            keluarVal = document.getElementById('shiftSiangKeluar').value;
            badgeText = 'Shift Siang (' + masukVal.substr(0, 5) + ' - ' + keluarVal.substr(0, 5) + ' WIB)';
        } else if (shiftType === 'malam') {
            masukVal = document.getElementById('shiftMalamMasuk').value;
            keluarVal = document.getElementById('shiftMalamKeluar').value;
            badgeText = 'Shift Malam (' + masukVal.substr(0, 5) + ' - ' + keluarVal.substr(0, 5) + ' WIB)';
        }

        document.getElementById('jamMasukInput').value = masukVal;
        document.getElementById('jamKeluarInput').value = keluarVal;
        document.getElementById('activeShiftBadge').innerText = badgeText;

        updateToleransiPreview();

        // Flash visual effect
        const jamIn = document.getElementById('jamMasukInput');
        const jamOut = document.getElementById('jamKeluarInput');
        jamIn.classList.add('bg-success-subtle');
        jamOut.classList.add('bg-success-subtle');
        setTimeout(() => {
            jamIn.classList.remove('bg-success-subtle');
            jamOut.classList.remove('bg-success-subtle');
        }, 800);
    }

    // Live preview toleransi jam
    function updateToleransiPreview() {
        const jamVal = document.getElementById('jamMasukInput').value;
        const tolVal = parseInt(document.getElementById('toleransiInput').value) || 0;
        if (jamVal) {
            const parts = jamVal.split(':');
            const d = new Date();
            d.setHours(parseInt(parts[0]), parseInt(parts[1]) + tolVal, parseInt(parts[2] || 0));
            const h = String(d.getHours()).padStart(2, '0');
            const m = String(d.getMinutes()).padStart(2, '0');
            document.getElementById('toleransiPreview').innerText = `Batas maksimal toleransi hadir: ${h}:${m} WIB`;
        }
    }

    document.getElementById('jamMasukInput').addEventListener('input', updateToleransiPreview);
    document.getElementById('toleransiInput').addEventListener('input', updateToleransiPreview);
    updateToleransiPreview();
</script>

<?= $this->endSection() ?>
