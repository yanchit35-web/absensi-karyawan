<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Absensi Karyawan' ?> - Absensi Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a1a2e;
            --sidebar-active: #0f3460;
            --primary: #0f3460;
            --accent: #533483;
        }
        body { background: #f0f2f5; min-height: 100vh; }
        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--sidebar-bg);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        #sidebar .sidebar-brand {
            padding: 20px 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        #sidebar .sidebar-brand h5 {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            color: white;
        }
        #sidebar .sidebar-brand p {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            margin: 0;
        }
        #sidebar .nav-section {
            padding: 12px 15px 5px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            font-weight: 600;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            text-decoration: none;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: white;
        }
        #sidebar .nav-link i { font-size: 1.1rem; width: 20px; }
        /* Main */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Topbar */
        .topbar {
            background: white;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .topbar-title { font-weight: 700; font-size: 1.15rem; color: #1a1a2e; }
        .page-content { padding: 25px; flex: 1; }
        /* Cards */
        .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .card-header { border-radius: 14px 14px 0 0 !important; border-bottom: 1px solid rgba(0,0,0,0.07); }
        /* Footer */
        footer { background: white; padding: 12px 25px; font-size: 0.8rem; color: #6c757d; border-top: 1px solid #e9ecef; }
        /* Responsive */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
        /* User badge */
        .user-badge {
            display: flex; align-items: center; gap: 8px;
            background: #f8f9fa; border-radius: 30px; padding: 6px 14px;
        }
        .user-badge img, .user-badge .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--primary); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
        }
        .badge-status {
            font-size: 0.65rem; font-weight: 600; padding: 3px 8px; border-radius: 20px;
        }
        /* Sidebar bottom */
        .sidebar-user {
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 10px;
            margin-top: auto;
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--accent); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem; flex-shrink: 0;
        }
        .sidebar-user-info { overflow: hidden; }
        .sidebar-user-info .name { font-size: 0.85rem; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-info .role { font-size: 0.72rem; color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-person-badge-fill fs-4 text-info"></i>
            <h5>Absensi Karyawan</h5>
        </div>
        <p>Sistem Manajemen Kehadiran</p>
    </div>

    <div class="nav-section">Main Menu</div>

    <a href="/dashboard" class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <?php if (session()->get('role') === 'admin'): ?>
    <div class="nav-section">Data Master</div>

    <a href="/karyawan" class="nav-link <?= str_starts_with(uri_string(), 'karyawan') ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Data Karyawan
    </a>

    <div class="nav-section">Kehadiran & Approval</div>

    <a href="/absensi" class="nav-link <?= str_starts_with(uri_string(), 'absensi') ? 'active' : '' ?>">
        <i class="bi bi-calendar2-check-fill"></i> Rekap Absensi
    </a>

    <a href="/pengajuan" class="nav-link <?= str_starts_with(uri_string(), 'pengajuan') ? 'active' : '' ?>">
        <i class="bi bi-clipboard2-check-fill"></i> Persetujuan Cuti & Izin
    </a>

    <a href="/lembur" class="nav-link <?= str_starts_with(uri_string(), 'lembur') ? 'active' : '' ?>">
        <i class="bi bi-clock-history"></i> Persetujuan Lembur
    </a>

    <div class="nav-section">Laporan & Sistem</div>

    <a href="/laporan" class="nav-link <?= str_starts_with(uri_string(), 'laporan') ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Absensi
    </a>

    <a href="/pengaturan" class="nav-link <?= str_starts_with(uri_string(), 'pengaturan') ? 'active' : '' ?>">
        <i class="bi bi-gear-fill"></i> Pengaturan Kantor
    </a>
    <?php else: ?>
    <div class="nav-section">Presensi & Pengajuan</div>

    <a href="/self-absen" class="nav-link <?= uri_string() === 'self-absen' ? 'active' : '' ?>">
        <i class="bi bi-camera-fill text-warning"></i> Absen Selfie & GPS
    </a>

    <a href="/absensi" class="nav-link <?= str_starts_with(uri_string(), 'absensi') ? 'active' : '' ?>">
        <i class="bi bi-calendar-week-fill"></i> Riwayat Absen Saya
    </a>

    <a href="/pengajuan" class="nav-link <?= str_starts_with(uri_string(), 'pengajuan') ? 'active' : '' ?>">
        <i class="bi bi-envelope-paper-fill"></i> Ajukan Cuti & Izin
    </a>

    <a href="/lembur" class="nav-link <?= str_starts_with(uri_string(), 'lembur') ? 'active' : '' ?>">
        <i class="bi bi-clock-fill"></i> Ajukan Lembur
    </a>

    <div class="nav-section">Laporan Pribadi</div>

    <a href="/laporan" class="nav-link <?= str_starts_with(uri_string(), 'laporan') ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-pdf-fill"></i> Unduh Rekap Saya
    </a>

    <div class="nav-section">Pengaturan Akun</div>

    <a href="/profil" class="nav-link <?= uri_string() === 'profil' ? 'active' : '' ?>">
        <i class="bi bi-shield-lock-fill"></i> Ganti Password
    </a>
    <?php endif; ?>

    <!-- Sidebar user info -->
    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: var(--sidebar-bg);">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?></div>
            <div class="sidebar-user-info">
                <div class="name"><?= esc(session()->get('nama')) ?></div>
                <div class="role"><?= ucfirst(session()->get('role')) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div id="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-md-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="topbar-title"><?= $title ?? 'Dashboard' ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted d-none d-md-inline" style="font-size:0.85rem;">
                <i class="bi bi-clock"></i> <?= date('l, d F Y') ?>
            </span>
            <div class="dropdown">
                <div class="user-badge dropdown-toggle" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                    <div class="avatar d-flex align-items-center justify-content-center">
                        <?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline" style="font-size:0.88rem; font-weight:600;"><?= esc(session()->get('nama')) ?></span>
                    <span class="badge-status bg-<?= session()->get('role') === 'admin' ? 'primary' : 'success' ?> text-white d-none d-md-inline">
                        <?= ucfirst(session()->get('role')) ?>
                    </span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Akun Saya</h6></li>
                    <li><a class="dropdown-item" href="/profil">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>Ganti Password
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="px-4 pt-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <?= session()->getFlashdata('info') ?>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        <?= $this->renderSection('content') ?>
    </div>

    <footer>
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-shield-check text-primary"></i> &copy; <?= date('Y') ?> Sistem Absensi Karyawan</span>
            <span class="text-muted">Powered by CodeIgniter 4</span>
        </div>
    </footer>
</div>

<!-- Overlay for mobile -->
<div id="overlay" class="d-none" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;" onclick="closeSidebar()"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('overlay');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('d-none');
    });
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.add('d-none');
    }
</script>
</body>
</html>
