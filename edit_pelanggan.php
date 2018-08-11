<?php  # Script edit_pelanggan.php
// This script update data pelanggan.

$page_title = 'Data Pelanggan';
include('includes/header.html');

// User validation:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || (!in_array($_SESSION['user_level'], ['administrator', 'kasir']))) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}

include('includes/footer.html');
?> 