<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';

ensure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    admin_flash('გასვლის მოთხოვნა ვერ დადასტურდა.', 'error');
    redirect('index.php');
}

logout_admin();
redirect('login.php');