<?php
/** @var string $baseUrl */
/** @var array $inquiries */
/** @var int $total */
/** @var int $page */
/** @var int $pages */
/** @var array $flash */
require __DIR__ . '/layout.php';
?>

<div class="admin-page-header">
    <div>
        <h2>Inquiries</h2>
        <p class="admin-page-sub"><?= number_format($total) ?> total inquir<?= $total !== 1 ? 'ies' : 'y' ?></p>
    </div>
</div>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($inquiries)): ?>
            <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">No inquiries yet.</td></tr>
            <?php else: ?>
            <?php foreach ($inquiries as $inq): ?>
            <tr class="<?= $inq['status'] === 'pending' ? 'row-pending' : '' ?>">
                <td>
                    <strong><?= htmlspecialchars($inq['name']) ?></strong><br>
                    <small><a href="mailto:<?= htmlspecialchars($inq['email']) ?>"><?= htmlspecialchars($inq['email']) ?></a></small>
                    <?php if ($inq['phone']): ?><br><small><?= htmlspecialchars($inq['phone']) ?></small><?php endif; ?>
                </td>
                <td>
                    <?php if ($inq['property_title']): ?>
                    <a href="<?= $baseUrl ?>/property/detail/<?= $inq['property_id'] ?>" target="_blank"><?= htmlspecialchars($inq['property_title']) ?></a>
                    <?php else: ?>
                    <span class="text-muted">General inquiry</span>
                    <?php endif; ?>
                </td>
                <td class="inq-message"><?= htmlspecialchars(substr($inq['message'], 0, 120)) ?><?= strlen($inq['message']) > 120 ? '…' : '' ?></td>
                <td><span class="status-pill status-<?= $inq['status'] ?>"><?= ucfirst($inq['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($inq['created_at'])) ?></td>
                <td>
                    <div class="table-actions">
                        <?php if ($inq['status'] === 'pending'): ?>
                        <form action="<?= $baseUrl ?>/admin/closeinquiry/<?= $inq['id'] ?>" method="POST">
                            <input type="hidden" name="status" value="responded">
                            <button type="submit" class="action-btn action-edit" title="Mark Responded"><i data-lucide="check"></i></button>
                        </form>
                        <?php endif; ?>
                        <a href="mailto:<?= htmlspecialchars($inq['email']) ?>?subject=Re: <?= urlencode($inq['property_title'] ?? 'Your Inquiry') ?>"
                           class="action-btn" title="Reply by email"><i data-lucide="mail"></i></a>
                        <form action="<?= $baseUrl ?>/admin/deleteinquiry/<?= $inq['id'] ?>" method="POST"
                              onsubmit="return confirm('Delete this inquiry?')">
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
        <a href="<?= $baseUrl ?>/admin/inquiries?page=<?= $page-1 ?>" class="page-btn"><i data-lucide="chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($p = max(1,$page-2); $p <= min($pages,$page+2); $p++): ?>
        <a href="<?= $baseUrl ?>/admin/inquiries?page=<?= $p ?>" class="page-btn <?= $p===$page?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
        <a href="<?= $baseUrl ?>/admin/inquiries?page=<?= $page+1 ?>" class="page-btn"><i data-lucide="chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
