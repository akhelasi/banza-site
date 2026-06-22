<?php
$pageTitle = 'Banza Village Site';
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="page-shell">
    <section class="hero" aria-labelledby="page-title">
      <p class="eyebrow">Local PHP/XAMPP project</p>
      <h1 id="page-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
      <p>
        Starter project for a PHP, SQL, HTML, CSS, and JavaScript website.
        Develop locally with XAMPP, keep source code in GitHub, and deploy to PHP hosting when ready.
      </p>
      <button class="status-button" type="button" id="statusButton">Check JavaScript</button>
      <p class="status-text" id="statusText">JavaScript is waiting.</p>
    </section>
  </main>
  <script src="assets/js/main.js"></script>
</body>
</html>
