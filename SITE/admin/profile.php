<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();

$credentials = active_admin_credentials();
$currentEmail = strtolower(trim((string) ($credentials['email'] ?? '')));
$configPath = admin_runtime_config_path();
$credentialSource = (string) ($credentials['source'] ?? 'config');
$credentialStorageNote = $credentialSource === 'mysql'
    ? 'ცვლილება შეინახება MySQL admins table-ში.'
    : 'ცვლილება ინახება untracked SITE/includes/config.php ფაილში. ეს ფაილი Git-ში არ იტვირთება და production credential-ებისთვისაა განკუთვნილი.';
$errors = [];
$oldEmail = $currentEmail;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'უსაფრთხოების token არასწორია. განაახლეთ გვერდი და სცადეთ თავიდან.';
    } else {
        $oldEmail = strtolower(trim((string) ($_POST['email'] ?? '')));
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
        $currentHash = (string) ($credentials['password_hash'] ?? '');

        if (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'შეიყვანეთ სწორი ელფოსტა.';
        }

        if ($currentHash === '' || !password_verify($currentPassword, $currentHash)) {
            $errors[] = 'მიმდინარე პაროლი არასწორია.';
        }

        if (strlen($newPassword) < 12) {
            $errors[] = 'ახალი პაროლი მინიმუმ 12 სიმბოლო უნდა იყოს.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'ახალი პაროლი და გამეორება არ ემთხვევა.';
        }

        if ($currentPassword !== '' && $newPassword !== '' && hash_equals($currentPassword, $newPassword)) {
            $errors[] = 'ახალი პაროლი განსხვავებული უნდა იყოს მიმდინარე პაროლისგან.';
        }

        if ($errors === []) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            if (!write_admin_credentials($oldEmail, $newHash)) {
                $errors[] = $credentialSource === 'mysql'
                    ? 'MySQL admins table-ში credential ვერ განახლდა. გადაამოწმეთ database config/logs.'
                    : 'config.php ვერ ჩაიწერა. გადაამოწმეთ SITE/includes ფოლდერის write permission.';
            } else {
                update_current_admin_session($oldEmail);
                admin_flash($credentialSource === 'mysql'
                    ? 'Admin credential განახლდა MySQL admins table-ში.'
                    : 'Admin credential განახლდა. ახალი მონაცემები ჩაიწერა untracked SITE/includes/config.php ფაილში.');
                redirect('profile.php');
            }
        }
    }
}

render_admin_header('პროფილი', 'profile');
?>

<section class="admin-card">
  <div class="admin-card-heading">
    <div>
      <p class="eyebrow">Account security</p>
      <h2>Admin credential</h2>
    </div>
  </div>

  <p class="muted-note"><?php echo e($credentialStorageNote); ?></p>
  <?php if ($credentialSource !== 'mysql'): ?>
    <p class="muted-note">Config path: <code><?php echo e($configPath); ?></code></p>
  <?php endif; ?>

  <?php if ($errors !== []): ?>
    <div class="flash flash-error">
      <?php foreach ($errors as $error): ?>
        <p><?php echo e($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="admin-form" method="post" autocomplete="off" novalidate>
    <?php echo csrf_field(); ?>
    <div class="admin-form-grid">
      <label><span>Admin email</span><input type="email" name="email" value="<?php echo e($oldEmail); ?>" required autocomplete="username"></label>
      <label><span>მიმდინარე პაროლი</span><input type="password" name="current_password" required autocomplete="current-password"></label>
    </div>
    <div class="admin-form-grid">
      <label><span>ახალი პაროლი</span><input type="password" name="new_password" required minlength="12" autocomplete="new-password"></label>
      <label><span>გაიმეორეთ ახალი პაროლი</span><input type="password" name="confirm_password" required minlength="12" autocomplete="new-password"></label>
    </div>
    <button class="button button-primary" type="submit">credential-ის განახლება</button>
  </form>
</section>

<?php render_admin_footer(); ?>
