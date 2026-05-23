<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seputar Dunia | Portal Berita Terkini</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg-color: #0b0f19;
            --glass-bg: rgba(11, 15, 25, 0.75);
            --border-color: rgba(255, 255, 255, 0.1);
            --accent-color: #3b82f6; /* Modern Blue Accent */
            --accent-hover: #60a5fa;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
        }
        /* Glassmorphism Navbar */
        .navbar-custom {
            background-color: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-primary) !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar-brand i {
            color: var(--accent-color);
        }
        .nav-link {
            font-weight: 500;
            color: var(--text-secondary) !important;
            transition: color 0.3s ease, transform 0.2s ease;
            position: relative;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-primary) !important;
            transform: translateY(-1px);
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--accent-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 80%;
        }
        
        .btn-accent {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-accent:hover {
            border-color: var(--accent-color);
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--accent-hover);
        }
        .btn-primary-custom {
            background-color: var(--accent-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 0.4rem 1rem;
            font-weight: 500;
            font-size: 0.875rem;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }
        .btn-primary-custom:hover {
            background-color: var(--accent-hover);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="<?= base_url() ?>">
                <i class="fas fa-globe-americas"></i> SEPUTAR DUNIA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" aria-current="page" href="<?= base_url() ?>">Home</a>
                    </li>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= url_is('category/' . esc($cat['slug'])) ? 'active' : '' ?>" href="<?= base_url('category/' . esc($cat['slug'])) ?>"><?= esc($cat['name']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php 
                        $premiumText = '<i class="fas fa-crown me-1"></i> Premium';
                        if (session()->get('isLoggedIn')) {
                            $db = \Config\Database::connect();
                            $sub = $db->table('subscriptions')->where('user_id', session()->get('user_id'))->where('status', 'active')->get()->getRow();
                            if ($sub && strtotime($sub->end_date) > time()) {
                                $premiumText = '<i class="fas fa-crown me-1"></i> Perpanjang';
                            }
                        }
                    ?>
                    <a href="<?= base_url('subscribe') ?>" class="btn btn-outline-warning btn-sm me-2 fw-bold" style="border-radius: 8px;">
                        <?= $premiumText ?>
                    </a>
                    <a href="#" class="btn btn-accent btn-sm">
                        <i class="fas fa-search"></i>
                    </a>
                    
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('logout') ?>" class="btn btn-accent btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary-custom btn-sm">
                            <i class="fas fa-user-circle me-1"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="container my-5">