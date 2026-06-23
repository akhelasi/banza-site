<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();
render_admin_header('სანაგვე', 'trash');
?>

<section class="admin-card empty-admin-state">
  <h2>სანაგვე ცარიელია</h2>
  <p>Soft delete შემდეგ ფაზაში ჩაირთვება. წაშლილი კონტენტი აქ გამოჩნდება restore და permanent delete მოქმედებებით.</p>
</section>

<?php render_admin_footer(); ?>