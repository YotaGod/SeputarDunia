<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Seputar Dunia</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --admin-bg: #0f172a;
            --admin-sidebar: #1e293b;
            --admin-accent: #3b82f6;
            --admin-accent-hover: #2563eb;
            --admin-border: rgba(255, 255, 255, 0.05);
            --admin-text-main: #f8fafc;
            --admin-text-muted: #94a3b8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg);
            color: var(--admin-text-main);
        }
        
        /* Premium Sidebar */
        #wrapper {
            display: flex;
            min-height: 100vh;
        }
        #sidebar-wrapper {
            width: 260px;
            background-color: var(--admin-sidebar);
            border-right: 1px solid var(--admin-border);
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 15px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--admin-text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--admin-border);
            letter-spacing: 0.5px;
        }
        .sidebar-brand i {
            color: var(--admin-accent);
        }
        .sidebar-nav {
            padding: 1rem 0;
            flex-grow: 1;
        }
        .sidebar-link {
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--admin-text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }
        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            color: var(--admin-text-main);
            background-color: rgba(255, 255, 255, 0.03);
        }
        .sidebar-link:hover i, .sidebar-link.active i {
            color: var(--admin-accent);
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: var(--admin-accent);
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--admin-border);
        }
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-radius: 8px;
            padding: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .logout-btn:hover {
            background-color: #ef4444;
            color: white;
        }

        /* Topbar & Content */
        #page-content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .topbar {
            background-color: var(--admin-sidebar);
            border-bottom: 1px solid var(--admin-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .content-area {
            padding: 2rem;
            flex-grow: 1;
        }
        
        /* Global Card Styling for Admin */
        .card {
            background-color: var(--admin-sidebar);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--admin-border);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        .form-control, .form-select {
            background-color: rgba(0,0,0,0.2);
            border: 1px solid var(--admin-border);
            color: var(--admin-text-main);
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(0,0,0,0.3);
            border-color: var(--admin-accent);
            color: var(--admin-text-main);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
    </style>
</head>
<body>
<div id="wrapper">
    <!-- Premium Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-brand">
            <i class="fas fa-satellite-dish"></i> SD Admin Panel
        </div>
        <div class="sidebar-nav">
            
            <a href="<?= base_url('admin') ?>" class="sidebar-link">
                <i class="fas fa-border-all"></i> Dashboard
            </a>

            <?php if (has_permission('article-create') || has_permission('article-edit-all') || has_permission('article-review')): ?>
                <a href="<?= base_url('admin/articles') ?>" class="sidebar-link">
                    <i class="fas fa-file-signature"></i> Manajemen Artikel
                </a>
            <?php endif; ?>
            
            <?php if (has_permission('role-manage')): ?>
                <a href="<?= base_url('admin/roles') ?>" class="sidebar-link">
                    <i class="fas fa-user-shield"></i> Peran & Hak Akses
                </a>
            <?php endif; ?>

            <?php if (has_permission('article-review')): ?>
                <a href="<?= base_url('admin/comments') ?>" class="sidebar-link">
                    <i class="fas fa-comments"></i> Manajemen Komentar
                </a>
            <?php endif; ?>

            <?php if (has_permission('user-manage')): ?>
                <a href="<?= base_url('admin/users') ?>" class="sidebar-link">
                    <i class="fas fa-users-cog"></i> Manajemen Pengguna
                </a>
            <?php endif; ?>
            
        </div>
        <div class="sidebar-footer">
            <a href="<?= base_url('logout') ?>" class="logout-btn">
                <i class="fas fa-power-off"></i> Logout
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <nav class="topbar sticky-top">
             <h1 class="topbar-title text-muted">
                <?php if (session()->getFlashdata('success')): ?>
                    <span class="text-success"><i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></span>
                <?php else: ?>
                    Overview
                <?php endif; ?>
             </h1>
             <div class="d-flex align-items-center gap-3">
                 <div class="text-end">
                     <div class="fw-bold" style="font-size: 0.9rem;"><?= session()->get('username') ?></div>
                     <div class="text-muted" style="font-size: 0.75rem;"><?= session()->get('role_name') ?? 'Admin' ?></div>
                 </div>
                 <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                     <i class="fas fa-user text-white"></i>
                 </div>
             </div>
        </nav>
        <div class="content-area">
