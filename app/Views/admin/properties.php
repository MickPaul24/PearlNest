<?php
/** @var string $baseUrl */
/** @var array $properties */
/** @var int $total */
/** @var int $page */
/** @var int $pages */
/** @var string $search */
/** @var string $statusFilter */
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
        <h2>Properties List</h2>
        <p class="admin-page-sub"><?= number_format($total) ?> total propert<?= $total !== 1 ? 'ies' : 'y' ?></p>
    </div>
    <a href="<?= $baseUrl ?>/admin/add" class="btn btn-primary"><i data-lucide="plus"></i> Add Property</a>
</div>

<form action="<?= $baseUrl ?>/admin/properties" method="GET" class="admin-filter-bar">
    <input type="text" name="search" placeholder="Search by title, location…"
           value="<?= htmlspecialchars($search) ?>" class="form-input" style="flex:1;min-width:200px;">
    <select name="status" class="form-input" style="width:160px;">
        <option value="">All Statuses</option>
        <?php foreach (['available','rented','sold','under_review'] as $s): ?>
        <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary"><i data-lucide="search"></i> Filter</button>
    <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-outline">Clear</a>
</form>

<div class="admin-card">
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
            <?php if (empty($properties)): ?>
            <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">No properties found. <a href="<?= $baseUrl ?>/admin/add">Add your first property</a>.</td></tr>
            <?php else: ?>
            <?php foreach ($properties as $prop): ?>
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
                <td>
                    <form action="<?= $baseUrl ?>/admin/setstatus/<?= $prop['id'] ?>" method="POST" style="display:inline;">
                        <select name="status" class="status-select status-<?= $prop['status'] ?>" onchange="this.form.submit()">
                            <?php foreach (['available','rented','sold','under_review'] as $s): ?>
                            <option value="<?= $s ?>" <?= $prop['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
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

    <?php if ($pages > 1): ?>
    <div class="pagination" style="padding:16px;">
        <?php if ($page > 1): ?>
        <a href="<?= $baseUrl ?>/admin/properties?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn"><i data-lucide="chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($p = max(1,$page-2); $p <= min($pages,$page+2); $p++): ?>
        <a href="<?= $baseUrl ?>/admin/properties?page=<?= $p ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>"
           class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
        <a href="<?= $baseUrl ?>/admin/properties?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn"><i data-lucide="chevron-right"></i></a>
        <?php endif; ?>
        <span class="page-info">Showing <?= (($page-1)*10)+1 ?>–<?= min($page*10,$total) ?> of <?= $total ?></span>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
