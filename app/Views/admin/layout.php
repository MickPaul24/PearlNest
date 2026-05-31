<?php /** @var string $baseUrl */ /** @var string $title */ /** @var array $flash */ /** @var int $pendingInq */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin') ?> | PearlNest Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
</head>
<body class="admin-body">

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <div class="logo-icon"><i data-lucide="home"></i></div>
            <div>
                <span class="logo-text">Pearl<span class="logo-accent">Nest</span></span>
                <span class="admin-sub">Admin Panel</span>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="<?= $baseUrl ?>/admin/dashboard"   class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard')   ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="<?= $baseUrl ?>/admin/properties"  class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'properties')  ? 'active' : '' ?>">
                <i data-lucide="building-2"></i> Properties
            </a>
            <a href="<?= $baseUrl ?>/admin/add"         class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/add')   ? 'active' : '' ?>">
                <i data-lucide="plus-circle"></i> Add Property
            </a>
            <a href="<?= $baseUrl ?>/admin/inquiries"   class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'inquiries')   ? 'active' : '' ?>">
                <i data-lucide="inbox"></i> Inquiries
                <?php if (isset($pendingInq) && $pendingInq > 0): ?>
                <span class="badge-count"><?= $pendingInq ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $baseUrl ?>/admin/settings"    class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'settings')    ? 'active' : '' ?>">
                <i data-lucide="settings"></i> Settings
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <a href="<?= $baseUrl ?>/" target="_blank" class="admin-nav-link">
                <i data-lucide="external-link"></i> View Site
            </a>
            <a href="<?= $baseUrl ?>/admin/logout" class="admin-nav-link logout-link">
                <i data-lucide="log-out"></i> Logout
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="admin-main">
        <header class="admin-header">
            <button class="admin-menu-toggle" id="adminMenuToggle">
                <i data-lucide="menu"></i>
            </button>
            <div class="admin-header-right">
                <span class="admin-user-name"><i data-lucide="user-circle"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
            </div>
        </header>

        <div class="admin-content">
            <!-- Flash messages -->
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                <div class="alert alert-<?= $type ?>">
                    <i data-lucide="<?= $type === 'success' ? 'check-circle' : 'alert-circle' ?>"></i>
                    <?= htmlspecialchars($msg) ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
