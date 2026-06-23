<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';

ensure_session();

if (is_admin_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'უსაფრთხოების token არასწორია. სცადეთ თავიდან.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (attempt_admin_login($email, $password)) {
            admin_flash('წარმატებით შეხვედით admin panel-ში.');
            redirect('index.php');
        }

        $error = 'ელფოსტა ან პაროლი არასწორია.';
    }
}

send_security_headers();
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - ბანძა</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body class="admin-login-body">
  <main class="login-panel" aria-labelledby="login-title">
    <img src="<?php echo e(asset('images/banza-logo.svg')); ?>" alt="ბანძა">
    <p class="eyebrow">მართვის პანელი</p>
    <h1 id="login-title">შესვლა</h1>
    <p class="muted-note">Demo credential: admin@banza.local / AdminDemo2026! შეცვალეთ production-მდე.</p>
    <?php if ($error !== ''): ?>
      <div class="flash flash-error"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post" class="login-form" novalidate>
      <?php echo csrf_field(); ?>
      <label><span>ელფოსტა</span><input type="email" name="email" autocomplete="username" required></label>
      <label><span>პაროლი</span><input type="password" name="password" autocomplete="current-password" required></label>
      <button class="button button-primary full-width" type="submit">შესვლა</button>
    </form>
    <a class="inline-link" href="../index.php">საიტზე დაბრუნება →</a>
  </main>
</body>
</html>
