<?php
/** @var string $baseUrl */
require __DIR__ . '/partials/header.php';
?>

<div class="container" style="text-align:center;padding:100px 20px;">
    <i data-lucide="search" style="width:80px;height:80px;color:var(--gray-200);margin-bottom:24px;"></i>
    <h1 style="font-size:2rem;color:var(--primary);">Page Not Found</h1>
    <p style="color:var(--gray-400);margin:12px 0 32px;">The page or property you're looking for doesn't exist or has been removed.</p>
    <a href="<?= $baseUrl ?>/" class="btn btn-primary">Back to Home</a>
    <a href="<?= $baseUrl ?>/property" class="btn btn-outline" style="margin-left:12px;">Browse Properties</a>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
