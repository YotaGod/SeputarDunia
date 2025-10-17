<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seputar Dunia | Portal Berita Terkini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <style>
        /* Gaya dasar inspirasi CNN Indonesia */
        .navbar-custom {
            background-color: #CC0000; /* Merah Khas CNN */
        }
        .navbar-custom .nav-link, .navbar-custom .navbar-brand {
            color: white !important;
        }
        .navbar-custom .nav-link:hover {
            color: #FFD700 !important; /* Emas saat hover */
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-2">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="<?= base_url() ?>">SEPUTAR DUNIA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= base_url() ?>">Home</a>
                    </li>
                    <!-- Navigasi Kategori Dinamis -->
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <li class="nav-item">
                                <!-- Link ke Category Controller -->
                                <a class="nav-link" href="<?= base_url('category/' . esc($cat['slug'])) ?>"><?= esc($cat['name']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-light me-2 btn-sm">
                        <i class="fas fa-search"></i>
                    </a>
                    
                    <!-- Tombol Login/Daftar atau Logout Dinamis -->
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('logout') ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-lock"></i> Login / Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="container my-4">