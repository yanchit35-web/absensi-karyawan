<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-gear-fill me-2"></i> Pengaturan Jam Kerja & Titik Koordinat Kantor
                </h6>
                <span class="badge bg-info text-dark px-3 py-1">Zona Waktu: WIB (Asia/Jakarta)</span>
            </div>
            <div class="card-body p-4">

                <form action="/pengaturan/update" method="POST">
                    <?= csrf_field() ?>

                    <!-- Bagian 1: Perusahaan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-building me-1"></i> Informasi Perusahaan</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Perusahaan / Instansi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" value="<?= esc($setting['nama_perusahaan'] ?? '') ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Bagian 2: Jam Kerja & Toleransi -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-clock-history me-1"></i> Jam Operasional & Batas Toleransi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Masuk Kerja <span class="text-danger">*</span></label>
                                <input type="time" step="1" name="jam_masuk" id="jamMasukInput" class="form-control" value="<?= esc($setting['jam_masuk'] ?? '08:00:00') ?>" required>
                                <div class="form-text small">Waktu standar mulai kerja</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Pulang Kerja <span class="text-danger">*</span></label>
                                <input type="time" step="1" name="jam_keluar" class="form-control" value="<?= esc($setting['jam_keluar'] ?? '17:00:00') ?>" required>
                                <div class="form-text small">Waktu selesai jam operasional</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Toleransi Keterlambatan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="toleransi_menit" id="toleransiInput" class="form-control" value="<?= esc($setting['toleransi_menit'] ?? '15') ?>" min="0" max="120" required>
                                    <span class="input-group-text">Menit</span>
                                </div>
                                <div class="form-text small" id="toleransiPreview">Batas toleransi: 08:15 WIB</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Bagian 3: Titik Koordinat & Geofencing Map -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Titik Lokasi Kantor & Radius Geofencing (Akurat)</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="detectCurrentLocation()">
                                <i class="bi bi-crosshair me-1"></i> Deteksi Lokasi Saya Saat Ini
                            </button>
                        </div>
                        <p class="text-muted small mb-3">Klik atau geser marker pin merah pada peta di bawah untuk menentukan titik lokasi kantor secara presisi.</p>

                        <!-- Peta Interaktif Leaflet -->
                        <div id="adminMap" style="height: 350px; width: 100%; border-radius: 12px; border: 2px solid #dee2e6; z-index: 1;" class="shadow-sm mb-3"></div>

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
                                <div class="form-text small">Karyawan harus berada di dalam radius ini</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-3 fw-bold fs-6">
                            <i class="bi bi-save me-2"></i> SIMPAN PENGATURAN KANTOR & JAM KERJA
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
    let initialLat = <?= (float)($setting['kantor_lat'] ?? -6.200000) ?>;
    let initialLng = <?= (float)($setting['kantor_lng'] ?? 106.816666) ?>;
    let initialRadius = <?= (int)($setting['kantor_radius'] ?? 500) ?>;

    // Initialize Leaflet Map
    const map = L.map('adminMap').setView([initialLat, initialLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Draggable Office Marker
    let officeMarker = L.marker([initialLat, initialLng], {
        draggable: true,
        title: 'Titik Lokasi Kantor'
    }).addTo(map);

    officeMarker.bindPopup('<b>Titik Lokasi Kantor</b><br>Geser pin ini untuk mengubah lokasi.').openPopup();

    // Radius Circle
    let radiusCircle = L.circle([initialLat, initialLng], {
        color: '#0d6efd',
        fillColor: '#0d6efd',
        fillOpacity: 0.15,
        radius: initialRadius
    }).addTo(map);

    // Function to update coordinates & circle
    function updateLocation(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);
        officeMarker.setLatLng([lat, lng]);
        radiusCircle.setLatLng([lat, lng]);
    }

    // Marker drag event
    officeMarker.on('dragend', function(e) {
        const position = officeMarker.getLatLng();
        updateLocation(position.lat, position.lng);
    });

    // Map click event
    map.on('click', function(e) {
        updateLocation(e.latlng.lat, e.latlng.lng);
    });

    // Radius input change event
    const radiusInput = document.getElementById('radiusInput');
    radiusInput.addEventListener('input', function() {
        const rad = parseInt(this.value) || 50;
        radiusCircle.setRadius(rad);
    });

    // Detect Current Location
    function detectCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
            return;
        }

        const btn = event.target;
        const originalText = btn.innerHTML;
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
