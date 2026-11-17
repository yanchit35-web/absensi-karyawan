<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    .pulse-marker {
        width: 16px;
        height: 16px;
        background: #0d6efd;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        70% { box-shadow: 0 0 0 14px rgba(13, 110, 253, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
    .clock-banner {
        background: linear-gradient(135deg, #0f3460, #16213e);
        color: #ffffff;
        border-radius: 12px;
        padding: 15px 20px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- Banner Waktu Akurat Real-Time -->
        <div class="clock-banner mb-4 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-info text-dark px-2.5 py-1 fw-bold mb-1">
                    <i class="bi bi-clock-history me-1"></i> WAKTU SERVER AKURAT (WIB)
                </span>
                <h4 class="fw-bold mb-0 text-white" id="liveDateStr">
                    <?= date('l, d F Y') ?>
                </h4>
                <small class="text-white-50">
                    Jadwal Masuk: <strong><?= substr($setting['jam_masuk'] ?? '08:00', 0, 5) ?> WIB</strong> &bull; 
                    Toleransi: <strong><?= $setting['toleransi_menit'] ?? 15 ?> menit</strong> &bull; 
                    Radius Kantor: <strong><?= $setting['kantor_radius'] ?? 500 ?> meter</strong>
                </small>
            </div>
            <div class="text-md-end">
                <div class="bg-black bg-opacity-30 px-4 py-2 rounded-3 border border-white border-opacity-20 d-inline-block">
                    <span id="liveTime" class="font-monospace fw-bolder fs-3 text-warning">
                        <?= date('H:i:s') ?>
                    </span>
                    <span class="text-white-50 fw-bold small"> WIB</span>
                </div>
                <div id="liveStatusBadge" class="mt-1 small fw-semibold">
                    <!-- Updated by JS: Tepat Waktu / Terlambat -->
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-shield-check me-2"></i> Presensi Mandiri (Verifikasi Wajah & GPS Akurat)
                </h5>
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">
                    Kantor: <?= esc($setting['nama_perusahaan'] ?? 'Pusat') ?>
                </span>
            </div>
            <div class="card-body p-4">

                <!-- Alert Status Absen Hari Ini -->
                <?php if (!$existing): ?>
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill fs-3 me-3 text-primary"></i>
                        <div>
                            <strong>Absensi Masuk Hari Ini:</strong> Pastikan Anda telah mengaktifkan izin <strong>Kamera</strong> dan <strong>Lokasi GPS</strong> dengan mode akurasi tinggi.
                        </div>
                    </div>
                <?php elseif ($existing && !$existing['jam_keluar']): ?>
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-clock-history fs-3 me-3 text-warning"></i>
                        <div>
                            <strong>Anda sudah Absen Masuk pada pukul <?= substr($existing['jam_masuk'], 0, 5) ?> WIB.</strong>
                            <br>Silakan ambil foto selfie untuk melakukan <strong>Absensi Pulang</strong>.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                        <div>
                            <strong>Absensi Hari Ini Lengkap!</strong>
                            <br>Jam Masuk: <strong><?= substr($existing['jam_masuk'], 0, 5) ?> WIB</strong> &bull; Jam Pulang: <strong><?= substr($existing['jam_keluar'], 0, 5) ?> WIB</strong>.
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$existing || ($existing && !$existing['jam_keluar'])): ?>
                <form action="/self-absen" method="POST" id="absenForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="foto" id="fotoBase64">
                    <input type="hidden" name="tipe" value="<?= ($existing) ? 'keluar' : 'masuk' ?>">

                    <div class="row g-4">
                        <!-- Kolom 1: Kamera Selfie -->
                        <div class="col-md-5 text-center">
                            <label class="form-label fw-bold d-block text-start">
                                <i class="bi bi-camera me-1 text-primary"></i> 1. Verifikasi Foto Selfie Wajah
                            </label>
                            
                            <div class="position-relative d-inline-block rounded-3 overflow-hidden shadow-sm bg-dark" style="width: 100%; height: 260px;">
                                <video id="cameraVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                <canvas id="photoCanvas" style="display: none; width: 100%; height: 100%; object-fit: cover;"></canvas>
                            </div>

                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btnCapture">
                                    <i class="bi bi-camera-fill me-1"></i> Ambil Foto Wajah
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnRetake" style="display: none;">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ambil Ulang
                                </button>
                            </div>
                            <div id="cameraStatus" class="small text-muted mt-2">Mengaktifkan kamera depan...</div>
                        </div>

                        <!-- Kolom 2: Peta Geofencing & Koordinat GPS -->
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold mb-0">
                                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i> 2. Deteksi Titik Lokasi GPS & Radius Kantor
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-info py-0 px-2" id="btnRefreshGPS">
                                    <i class="bi bi-arrow-clockwise"></i> Perbarui GPS
                                </button>
                            </div>

                            <!-- Peta Leaflet Real-Time -->
                            <div id="userMap" style="height: 200px; width: 100%; border-radius: 10px; border: 1.5px solid #dee2e6; z-index: 1;" class="shadow-sm mb-2"></div>

                            <!-- Informasi Jarak & Koordinat -->
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="row g-2 small">
                                    <div class="col-sm-6">
                                        <div class="text-muted">Titik Lokasi Anda:</div>
                                        <div id="gpsCoords" class="font-monospace fw-bold text-primary">Mendeteksi GPS...</div>
                                        <div id="gpsAccuracy" class="text-muted" style="font-size: 0.75rem;"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted">Jarak ke Titik Kantor:</div>
                                        <div id="distanceInfo" class="fw-bold fs-6">Menghitung jarak...</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Batas Radius: <?= (int)$setting['kantor_radius'] ?> meter</div>
                                    </div>
                                </div>
                                <div id="radiusAlert" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Tombol Konfirmasi Absensi -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-<?= ($existing) ? 'warning' : 'primary' ?> btn-lg py-3 fw-bold" id="btnSubmitAbsen">
                            <i class="bi bi-check2-circle me-1"></i>
                            <?= ($existing) ? 'KONFIRMASI ABSENSI PULANG' : 'KONFIRMASI ABSENSI MASUK' ?>
                        </button>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // Konfigurasi dari Admin
    const officeLat = <?= (float)($setting['kantor_lat'] ?? -6.200000) ?>;
    const officeLng = <?= (float)($setting['kantor_lng'] ?? 106.816666) ?>;
    const maxRadius = <?= (int)($setting['kantor_radius'] ?? 500) ?>;
    const jamMasukStr = "<?= esc($setting['jam_masuk'] ?? '08:00:00') ?>";
    const toleransiMenit = <?= (int)($setting['toleransi_menit'] ?? 15) ?>;

    let userLat = null;
    let userLng = null;
    let isWithinRadius = false;
    let photoTaken = false;

    // Inisialisasi Peta Leaflet
    const map = L.map('userMap').setView([officeLat, officeLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Marker Kantor (Pin Merah)
    const officeIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    const officeMarker = L.marker([officeLat, officeLng], { icon: officeIcon }).addTo(map);
    officeMarker.bindPopup('<b>Kantor Pusat</b><br>Titik referensi absensi.');

    // Lingkaran Radius Kantor
    const radiusCircle = L.circle([officeLat, officeLng], {
        color: '#198754',
        fillColor: '#198754',
        fillOpacity: 0.15,
        radius: maxRadius
    }).addTo(map);

    // Marker Posisi User (Pin Biru)
    let userMarker = null;
    let connectLine = null;

    // Haversine Formula untuk kalkulasi jarak presisi (meter)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radius bumi dalam meter
        const phi1 = lat1 * Math.PI / 180;
        const phi2 = lat2 * Math.PI / 180;
        const deltaPhi = (lat2 - lat1) * Math.PI / 180;
        const deltaLambda = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                  Math.cos(phi1) * Math.cos(phi2) *
                  Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    // Update Tampilan Posisi & Jarak pada Peta
    function updatePositionOnMap(lat, lng, accuracy) {
        userLat = lat;
        userLng = lng;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('gpsCoords').innerText = lat.toFixed(6) + ', ' + lng.toFixed(6);
        if (accuracy) {
            document.getElementById('gpsAccuracy').innerText = 'Akurasi GPS: ±' + Math.round(accuracy) + ' meter';
        }

        const distance = calculateDistance(lat, lng, officeLat, officeLng);
        document.getElementById('distanceInfo').innerText = distance + ' meter dari titik kantor';

        // Update/create user marker
        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            const userIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
            userMarker.bindPopup('<b>Posisi Anda Saat Ini</b>');
        }

        // Draw line between office and user
        if (connectLine) {
            connectLine.setLatLngs([[officeLat, officeLng], [lat, lng]]);
        } else {
            connectLine = L.polyline([[officeLat, officeLng], [lat, lng]], {
                color: '#6c757d',
                dashArray: '5, 10'
            }).addTo(map);
        }

        // Fit map bounds to show both office and user
        const group = new L.featureGroup([officeMarker, userMarker, radiusCircle]);
        map.fitBounds(group.getBounds().pad(0.2));

        // Validasi Radius
        const alertBox = document.getElementById('radiusAlert');
        const submitBtn = document.getElementById('btnSubmitAbsen');

        if (distance <= maxRadius) {
            isWithinRadius = true;
            radiusCircle.setStyle({ color: '#198754', fillColor: '#198754' });
            alertBox.innerHTML = '<div class="alert alert-success p-2 mb-0 d-flex align-items-center gap-2 small">' +
                '<i class="bi bi-check-circle-fill fs-5"></i>' +
                '<span><strong>Lokasi Valid:</strong> Anda berada di dalam radius kantor (' + distance + 'm &le; ' + maxRadius + 'm).</span>' +
                '</div>';
            if (submitBtn) submitBtn.disabled = false;
        } else {
            isWithinRadius = false;
            radiusCircle.setStyle({ color: '#dc3545', fillColor: '#dc3545' });
            alertBox.innerHTML = '<div class="alert alert-danger p-2 mb-0 d-flex align-items-center gap-2 small">' +
                '<i class="bi bi-exclamation-triangle-fill fs-5"></i>' +
                '<span><strong>Di Luar Radius:</strong> Anda berada ' + distance + ' meter dari kantor (Batas maksimal ' + maxRadius + 'm). Harap mendekat ke kantor untuk melakukan absen!</span>' +
                '</div>';
            if (submitBtn) submitBtn.disabled = true;
        }
    }

    // Get Real GPS with High Accuracy
    function getAccurateLocation() {
        if (!navigator.geolocation) {
            document.getElementById('gpsCoords').innerHTML = '<span class="text-danger">Geolocation tidak didukung browser ini.</span>';
            return;
        }

        document.getElementById('gpsCoords').innerText = 'Mengunci sinyal satelit GPS...';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                updatePositionOnMap(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
            },
            function(err) {
                document.getElementById('gpsCoords').innerHTML = '<span class="text-danger">Gagal mendeteksi GPS: ' + err.message + '</span>';
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
        );

        // Continuous tracking watcher
        navigator.geolocation.watchPosition(
            function(pos) {
                updatePositionOnMap(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
            },
            null,
            { enableHighAccuracy: true, maximumAge: 1000 }
        );
    }

    getAccurateLocation();
    document.getElementById('btnRefreshGPS').addEventListener('click', getAccurateLocation);

    // Kamera Selfie
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('photoCanvas');
    const btnCapture = document.getElementById('btnCapture');
    const btnRetake = document.getElementById('btnRetake');
    const cameraStatus = document.getElementById('cameraStatus');
    const fotoBase64 = document.getElementById('fotoBase64');

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
            .then(stream => {
                video.srcObject = stream;
                cameraStatus.innerText = 'Kamera aktif. Posisikan wajah Anda lalu tekan Ambil Foto.';
            })
            .catch(err => {
                cameraStatus.innerHTML = '<span class="text-danger">Kamera tidak dapat diakses (' + err.message + '). Pastikan izin kamera aktif.</span>';
            });
    }

    btnCapture.addEventListener('click', () => {
        canvas.width = video.videoWidth || 320;
        canvas.height = video.videoHeight || 240;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        fotoBase64.value = dataUrl;
        photoTaken = true;

        video.style.display = 'none';
        canvas.style.display = 'block';
        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-block';
        cameraStatus.innerHTML = '<span class="text-success fw-bold">✓ Foto wajah berhasil diambil!</span>';
    });

    btnRetake.addEventListener('click', () => {
        video.style.display = 'block';
        canvas.style.display = 'none';
        btnCapture.style.display = 'inline-block';
        btnRetake.style.display = 'none';
        fotoBase64.value = '';
        photoTaken = false;
        cameraStatus.innerText = 'Kamera aktif. Silakan ambil foto ulang.';
    });

    // Validasi saat form di-submit
    const form = document.getElementById('absenForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!userLat || !userLng) {
                e.preventDefault();
                alert('Peringatan: Koordinat lokasi GPS belum terdeteksi! Harap tunggu sinyal GPS terkunci.');
                return false;
            }
            if (!isWithinRadius) {
                e.preventDefault();
                alert('Peringatan: Anda berada di luar radius kantor yang telah ditetapkan admin! Absensi ditolak.');
                return false;
            }
        });
    }

    // Jam Real-Time Akurat & Status Keterlambatan
    function updateClockAndStatus() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveTime').innerText = `${hh}:${mm}:${ss}`;

        // Kalkulasi keterlambatan
        const parts = jamMasukStr.split(':');
        const scheduleMinutes = parseInt(parts[0]) * 60 + parseInt(parts[1]);
        const toleranceMinutes = scheduleMinutes + toleransiMenit;
        const currentMinutes = now.getHours() * 60 + now.getMinutes();

        const badgeEl = document.getElementById('liveStatusBadge');
        if (currentMinutes <= toleranceMinutes) {
            badgeEl.innerHTML = '<span class="badge bg-success px-3 py-1"><i class="bi bi-check-circle me-1"></i> Tepat Waktu (Batas: ' + 
                String(Math.floor(toleranceMinutes/60)).padStart(2,'0') + ':' + String(toleranceMinutes%60).padStart(2,'0') + ' WIB)</span>';
        } else {
            const telat = currentMinutes - scheduleMinutes;
            badgeEl.innerHTML = '<span class="badge bg-warning text-dark px-3 py-1"><i class="bi bi-exclamation-triangle me-1"></i> Terlambat ' + telat + ' menit</span>';
        }
    }

    setInterval(updateClockAndStatus, 1000);
    updateClockAndStatus();
</script>

<?= $this->endSection() ?>
