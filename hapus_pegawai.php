<?php  # Script hapus_pegawai.php
// This script delete data pegawai from database.

$page_title = 'Hapus Data Pegawai';
include('includes/header.html');

if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || (!in_array($_SESSION['user_level'], ['administrator', 'manager']))) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}  // End of user validation.

// Validate the input id:
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
} else {
  errorMessage:
    echo '<p>Terjadi kesalahan saat mencoba mengakses halaman ini.</p>';
    goto endScript;
}  // End of IF input validation.

require('mysqli_connect.php');

// Validate the id against the list:
$q = "SELECT * FROM tb_pegawai WHERE kode_pegawai = $id";
$r2 = @mysqli_query($dbc, $q);

// Validate the query result:
if ($r2) {
    if (mysqli_num_rows($r2) == 1) {
        $row = mysqli_fetch_array($r2, MYSQLI_ASSOC);
    } else {
        goto errorMessage;
    }  // End of IF (mysqli_num_rows).
} else {
    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan saat menjalankan query: ' . mysqli_error($dbc) . '</p>';
}  // End of IF ($r2).

// Validate the FORM SUBMISSION:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['konfirmasi'] == '1') {
        $q = "DELETE FROM tb_pegawai WHERE kode_pegawai = $id";
        $r = @mysqli_query($dbc, $q);

        // Validate the query result:
        if ($r) {
            if (mysqli_affected_rows($dbc) == 1) {
                echo '<p>Data telah berhasil di hapus.</p>';
            } else {
                echo '<p>Tidak ada perubahan yang disimpan.</p>';
            }  // End of IF (mysqli_affected_rows).
        } else {
            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan saat menjalankan query: ' . mysqli_error($dbc) . '</p>';
        } // End of IF($r).
    } else {
        echo '<p>Tidak ada perubahan yang disimpan.</p>';
    } // End of IF ($_POST['konfirmasi'] == '1').
} else {
    echo '<form action="hapus_pegawai.php" method="post">
        <h1>Yakin Anda akan menghapus data tersebut?</h1>
        <p>Kode Pegawai: ' . $id . '</p>
        <p>Nama: ' . $row['nama_lengkap'] . '</p>
        <p>Jenis Kelamin: ' . (($row['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan') . '</p>
        <p>No. Telepon: ' . $row['no_telepon'] . '</p>
        <p>Alamat: ' . $row['alamat'] . '</p>
        <p><input name="id" type="hidden" value="' . $id . '" /></p>
        <p><input name="konfirmasi" type="radio" value="1" />Ya <input name="konfirmasi" type="radio" value="0" checked="checked" />Tidak</p>
        <p><input name="submit" type="submit" value="Hapus" /></p>
      </form>';
}  // End of IF FORM SUBMISSION VALIDATION.

endScript:

echo '<p><a class="navlink" href="lihat_pegawai.php">Kembali</a></p>';

include('includes/footer.html');
?>