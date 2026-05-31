<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PearlNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
</head>
<body class="admin-login-body">

<div class="login-wrap">
    <div class="login-box">
        <div class="login-brand">
            <div class="logo-icon logo-icon-lg"><i data-lucide="home"></i></div>
            <h1>Pearl<span class="logo-accent">Nest</span></h1>
            <p>Admin Portal</p>
        </div>

        <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-error">
            <i data-lucide="alert-circle"></i>
            <?= htmlspecialchars($flash['error']) ?>
        </div>
        <?php endif; ?>

        <form action="<?= $baseUrl ?>/admin/login" method="POST" class="login-form">
            <div class="form-group">
                <label><i data-lucide="user"></i> Username</label>
                <input type="text" name="username" class="form-input" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label><i data-lucide="lock"></i> Password</label>
                <div class="pw-wrap">
                    <input type="password" name="password" id="pw" class="form-input" placeholder="••••••••" required>
                    <button type="button" class="pw-toggle" onclick="togglePw()"><i data-lucide="eye" id="pwIcon"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-2">
                <i data-lucide="log-in"></i> Log In
            </button>
        </form>

        <div class="login-back">
            <a href="<?= $baseUrl ?>/"><i data-lucide="arrow-left"></i> Back to public site</a>
        </div>
    </div>
</div>

<script src="<?= $baseUrl ?>/js/lucide.min.js"></script>
<script>
lucide.createIcons();

function togglePw() {
    const pw = document.getElementById('pw');
    const iconEl = document.querySelector('#pwIcon');
    if (pw.type === 'password') {
        pw.type = 'text';
        iconEl.setAttribute('data-lucide', 'eye-off');
    } else {
        pw.type = 'password';
        iconEl.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}
</script>
</body>
</html>
