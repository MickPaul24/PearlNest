<?php
/** @var string $baseUrl */
/** @var array $admin */
/** @var array $flash */
require __DIR__ . '/layout.php';
?>

<div class="admin-page-header">
    <div><h2>Settings</h2><p class="admin-page-sub">Manage your account details and security.</p></div>
</div>

<div class="form-two-col">
    <!-- Profile -->
    <div class="form-col">
        <div class="admin-card">
            <div class="admin-card-header"><h3><i data-lucide="user"></i> Profile</h3></div>
            <div class="admin-card-body">
                <form action="<?= $baseUrl ?>/admin/settings" method="POST">
                    <input type="hidden" name="action" value="profile">
                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" name="name" class="form-input"
                               value="<?= htmlspecialchars($admin['name'] ?? '') ?>" placeholder="Your name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-input"
                               value="<?= htmlspecialchars($admin['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" disabled>
                        <small class="text-muted">Username cannot be changed.</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password -->
    <div class="form-col">
        <div class="admin-card">
            <div class="admin-card-header"><h3><i data-lucide="lock"></i> Change Password</h3></div>
            <div class="admin-card-body">
                <form action="<?= $baseUrl ?>/admin/settings" method="POST">
                    <input type="hidden" name="action" value="password">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>New Password <small class="text-muted">(min 6 characters)</small></label>
                        <input type="password" name="new_password" class="form-input" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i data-lucide="key"></i> Change Password</button>
                </form>
            </div>
        </div>

        <div class="admin-card mt-3">
            <div class="admin-card-header"><h3><i data-lucide="info"></i> System Info</h3></div>
            <div class="admin-card-body">
                <table style="width:100%;font-size:14px;">
                    <tr><td class="text-muted" style="padding:6px 0;">PHP Version</td><td><?= phpversion() ?></td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Database</td><td>MySQL via PDO</td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">App</td><td>PearlNest v1.0</td></tr>
                    <tr><td class="text-muted" style="padding:6px 0;">Logged in as</td><td><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
