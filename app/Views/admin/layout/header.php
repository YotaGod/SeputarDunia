<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Seputar Dunia</title>
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Gaya Sederhana untuk Sidebar */
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: 0;
            transition: margin .25s ease-out;
        }
        #page-content-wrapper {
            width: 100%;
        }
        .sidebar-heading {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .list-group-item-action:hover {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-dark border-right text-white" id="sidebar-wrapper">
        <div class="sidebar-heading p-3 fw-bold text-warning fs-5">SD Admin Panel</div>
        <div class="list-group list-group-flush">
            
            <a href="<?= base_url('admin') ?>" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>

            <?php if (has_permission('article-create') || has_permission('article-edit-all') || has_permission('article-review')): ?>
                <a href="<?= base_url('admin/articles') ?>" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-file-alt me-2"></i> Manajemen Artikel
                </a>
            <?php endif; ?>
            
            <?php if (has_permission('role-manage')): ?>
                <a href="<?= base_url('admin/roles') ?>" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-shield-alt me-2"></i> Manajemen Peran & Hak Akses
                </a>
            <?php endif; ?>

            <?php if (has_permission('user-manage')): ?>
                <a href="<?= base_url('admin/users') ?>" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-users me-2"></i> Manajemen Pengguna
                </a>
            <?php endif; ?>
            
            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action bg-danger text-white mt-5">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom sticky-top shadow-sm">
            <div class="container-fluid">
                 <h5 class="my-2">
                    <?php if (session()->getFlashdata('success')): ?>
                        <span class="text-success"><?= session()->getFlashdata('success') ?></span>
                    <?php else: ?>
                        Selamat Datang, <?= session()->get('username') ?>!
                    <?php endif; ?>
                 </h5>
            </div>
        </nav>
        <div class="container-fluid p-4">
