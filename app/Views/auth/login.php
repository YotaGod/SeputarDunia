<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | Seputar Dunia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg-color: #0b0f19;
            --glass-bg: rgba(11, 15, 25, 0.75);
            --border-color: rgba(255, 255, 255, 0.1);
            --accent-color: #3b82f6; 
            --accent-hover: #60a5fa;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--accent-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        .btn-primary-custom {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
        }
        .btn-google {
            background-color: white;
            color: #333;
            border: 1px solid #ddd;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-google:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-secondary);
            margin: 1.5rem 0;
            font-size: 0.875rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }
        .divider:not(:empty)::before { margin-right: .25em; }
        .divider:not(:empty)::after { margin-left: .25em; }
        .brand-logo {
            font-size: 2rem;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .brand-logo i { color: var(--accent-color); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-logo">
            <i class="fas fa-globe-americas"></i>
        </div>
        <h4 class="text-center mb-1 fw-bold">Selamat Datang</h4>
        <p class="text-center text-secondary mb-4 small">Sign in ke Seputar Dunia</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="border-radius: 8px; font-size: 0.9rem; border: none; background: rgba(220, 53, 69, 0.1); color: #ff6b6b;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="border-radius: 8px; font-size: 0.9rem; border: none; background: rgba(25, 135, 84, 0.1); color: #20c997;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?= form_open('login') ?>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label small text-secondary">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="nama@email.com" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label small text-secondary">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 mb-3">Sign In</button>
        <?= form_close() ?>

        <div class="divider">atau lanjutkan dengan</div>

        <a href="<?= base_url('auth/google') ?>" class="btn btn-google w-100">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" width="20">
            Sign in with Google
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>