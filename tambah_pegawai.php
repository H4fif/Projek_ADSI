<?php  # Script tambah_pegawai.php
// This script add new pegawai to database.

$page_title = 'Pegawai Baru';
include('includes/header.html');

if (!isset($_SESSION['agent'], $_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['administrator', 'manager']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']))) {
    header('Location: index.php');
}

