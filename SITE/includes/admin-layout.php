<?php

declare(strict_types=1);

$adminNav = [
    ['label' => 'Dashboard', 'href' => 'index.php', 'key' => 'dashboard'],
    ['label' => 'ახალი ამბები', 'href' => 'content.php?type=news', 'key' => 'news'],
    ['label' => 'პროექტები', 'href' => 'content.php?type=projects', 'key' => 'projects'],
    ['label' => 'გვერდები', 'href' => 'content.php?type=pages', 'key' => 'pages'],
    ['label' => 'ფაილები', 'href' => 'media.php', 'key' => 'media'],
    ['label' => 'შეტყობინებები', 'href' => 'messages.php', 'key' => 'messages'],
    ['label' => 'პარამეტრები', 'href' => 'settings.php', 'key' => 'settings'],
    ['label' => 'სანაგვე', 'href' => 'trash.php', 'key' => 'trash'],
];

function render_admin_header(string $title, string $currentKey = 'dashboard'): void
{
    send_security_headers();
    global $adminNav;
    $admin = current_admin();
    $flash = admin_flash();
    ?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($title); ?> - Admin</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body class="admin-body">
  <aside class="admin-sidebar" aria-label="Admin navigation">
    <a class="admin-brand" href="index.php">
      <img src="<?php echo e(asset('images/banza-logo.svg')); ?>" alt="ბანძა">
    </a>
    <nav>
      <?php foreach ($adminNav as $item): ?>
        <a href="<?php echo e($item['href']); ?>"<?php echo is_active_nav($currentKey, $item['key']); ?>><?php echo e($item['label']); ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <div>
        <p class="eyebrow">მართვის პანელი</p>
        <h1><?php echo e($title); ?></h1>
      </div>
      <div class="admin-user">
        <span><?php echo e($admin['email'] ?? 'admin'); ?></span>
        <a class="button button-light" href="../index.php">საიტი</a>
        <form method="post" action="logout.php">
          <?php echo csrf_field(); ?>
          <button class="button button-primary" type="submit">გასვლა</button>
        </form>
      </div>
    </header>
    <?php if ($flash): ?>
      <div class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['message']); ?></div>
    <?php endif; ?>
<?php
}

function render_admin_footer(): void
{
    ?>
  </div>
  <script src="<?php echo e(asset('js/main.js')); ?>"></script>
</body>
</html>
<?php
}
