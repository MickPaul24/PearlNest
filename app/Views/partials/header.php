<?php /** @var string $baseUrl */ /** @var string $title */ /** @var array $flash */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PearlNest') ?> | PearlNest Uganda</title>
    <meta name="description" content="PearlNest — Uganda's trusted property broker connecting you to the finest hostels and rentals in Kampala and beyond.">
    <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-inner">
        <a href="<?= $baseUrl ?>/" class="navbar-brand">
            <div class="logo-icon"><i data-lucide="home"></i></div>
            <span class="logo-text">Pearl<span class="logo-accent">Nest</span></span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <i data-lucide="menu"></i>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= $baseUrl ?>/property" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/property') ? 'active' : '' ?>">Browse Properties</a></li>
            <li><a href="<?= $baseUrl ?>/about"    class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/about')    ? 'active' : '' ?>">About Us</a></li>
            <li><a href="<?= $baseUrl ?>/contact"  class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/contact')  ? 'active' : '' ?>">Contact</a></li>
            <li><a href="<?= $baseUrl ?>/admin/login" class="nav-link">Admin Portal</a></li>
        </ul>
    </div>
</nav>
