<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #0f3460, #533483);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header .icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 35px 35px 30px;
        }
        .btn-login {
            background: linear-gradient(135deg, #0f3460, #533483);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: opacity 0.2s;
        }
        .btn-login:hover { opacity: 0.9; color: white; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1.5px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #0f3460;
            box-shadow: 0 0 0 0.2rem rgba(15,52,96,0.15);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1.5px solid #dee2e6;
            border-right: none;
        }
        .input-group .form-control { border-radius: 0 10px 10px 0; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="bi bi-person-badge"></i></div>
            <h4 class="fw-bold mb-1">Absensi Karyawan</h4>
            <p class="mb-0 opacity-75">Sistem Manajemen Kehadiran</p>
        </div>

        <div class="login-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username"
                            value="<?= old('username') ?>" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <p class="text-center text-muted mt-4 mb-0" style="font-size:0.85rem;">
                <i class="bi bi-shield-check"></i> &copy; <?= date('Y') ?> Sistem Absensi Karyawan
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
