<?php
/** @var string $baseUrl */
/** @var array $propStats */
/** @var int $pendingInq */
/** @var int $totalInq */
/** @var array $recentProps */
/** @var array $flash */
require __DIR__ . '/layout.php';

$_imgBase = rtrim(parse_url($baseUrl, PHP_URL_PATH), '/');
$_imgSrc  = function(?string $p) use ($_imgBase): string {
    if (!$p) return '';
    return str_starts_with($p, 'http') ? $p : $_imgBase . '/' . $p;
};
?>

<div class="admin-page-header">
    <div>
        <h2>Admin Dashboard</h2>
        <p class="admin-page-sub">Real-time overview of your rental portfolio performance.</p>
    </div>
    <div class="admin-header-actions">
        <a href="<?= $baseUrl ?>/admin/add" class="btn btn-primary"><i data-lucide="plus"></i> New Property</a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#EFF6FF;color:#2563EB;"><i data-lucide="building-2"></i></div>
        <div class="stat-card-body">
            <p>Total Properties</p>
            <h3><?= $propStats['total'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#F0FDF4;color:#16A34A;"><i data-lucide="check-circle"></i></div>
        <div class="stat-card-body">
            <p>Available Listings</p>
            <h3><?= $propStats['available'] ?? 0 ?></h3>
            <span class="stat-card-sub"><?= $propStats['total'] > 0 ? round(($propStats['available']/$propStats['total'])*100) : 0 ?>% occupancy available</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#FFF7ED;color:#EA580C;"><i data-lucide="inbox"></i></div>
        <div class="stat-card-body">
            <p>Total Inquiries</p>
            <h3><?= $totalInq ?></h3>
            <?php if ($pendingInq > 0): ?>
            <span class="stat-card-sub stat-card-warn"><?= $pendingInq ?> Pending Response</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#FEF9C3;color:#D97706;"><i data-lucide="home"></i></div>
        <div class="stat-card-body">
            <p>Rented / Sold</p>
            <h3><?= ($propStats['rented'] ?? 0) + ($propStats['sold'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<!-- Properties Table -->
<div class="admin-card mt-4">
    <div class="admin-card-header">
        <h3>Properties Overview</h3>
        <div class="admin-card-actions">
            <form action="<?= $baseUrl ?>/admin/properties" method="GET" style="display:flex;gap:8px;">
                <input type="text" name="search" placeholder="Search properties…" class="form-input" style="width:220px;">
                <button type="submit" class="btn btn-sm btn-outline"><i data-lucide="search"></i></button>
            </form>
            <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-sm btn-outline">View All</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Price /mo</th>
                    <th>Rating</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentProps)): ?>
                <tr><td colspan="7" class="text-center text-muted">No properties yet. <a href="<?= $baseUrl ?>/admin/add">Add one!</a></td></tr>
                <?php else: ?>
                <?php foreach ($recentProps as $prop): ?>
                <tr>
                    <td>
                        <div class="table-prop-cell">
                            <?php $src = $_imgSrc($prop['primary_image']); ?>
                            <?php if ($src): ?>
                            <img src="<?= htmlspecialchars($src) ?>" alt="" class="table-prop-img">
                            <?php else: ?>
                            <div class="table-prop-img card-no-img"><i data-lucide="image"></i></div>
                            <?php endif; ?>
                            <div>
                                <strong><?= htmlspecialchars($prop['title']) ?></strong>
                                <small><?= htmlspecialchars($prop['location']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= \App\Models\Property::typeLabel($prop['type']) ?></td>
                    <td><span class="status-pill status-<?= $prop['status'] ?>"><?= ucfirst(str_replace('_', ' ', $prop['status'])) ?></span></td>
                    <td>UGX <?= number_format((float)$prop['price']) ?></td>
                    <td><i data-lucide="star" class="text-gold"></i> <?= number_format((float)$prop['rating'], 1) ?> (<?= $prop['rating_count'] ?>)</td>
                    <td><?= $prop['is_featured'] ? '<i data-lucide="star" class="text-gold"></i>' : '<i data-lucide="star" class="text-muted"></i>' ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="<?= $baseUrl ?>/property/detail/<?= $prop['id'] ?>" target="_blank" class="action-btn" title="View"><i data-lucide="eye"></i></a>
                            <a href="<?= $baseUrl ?>/admin/edit/<?= $prop['id'] ?>" class="action-btn action-edit" title="Edit"><i data-lucide="pencil"></i></a>
                            <form action="<?= $baseUrl ?>/admin/delete/<?= $prop['id'] ?>" method="POST" onsubmit="return confirm('Permanently delete this property?')">
                                <button type="submit" class="action-btn action-delete" title="Delete"><i data-lucide="trash-2"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
