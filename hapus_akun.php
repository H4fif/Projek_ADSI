<?php  # Script hapus_akun.php
// This page deletes akun.

$page_title = 'Hapus Akun';
include('includes/header.html');

// Validate the user.
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) || ($_SESSION['user_level'] != 'administrator'))) {

    header('Location: index.php');

    goto endScript;

}  // End of user validation.

// Validate the input id:
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
} else {
    echo '<h1>Terjadi kesalahan!</h1><p>Terjadi kesalahan saat mencoba mengakses halaman ini.</p>';

    goto endScript;

}  // End of id validation.

require('mysqli_connect.php');

// Validate the id against the list:
$q = "SELECT * FROM tb_akun WHERE kode_akun = $id";
$r2 = @mysqli_query($dbc, $q);
if ($r2) {
    if (mysqli_num_rows($r2) == 1) {
        $row = mysqli_fetch_array($r2, MYSQLI_ASSOC);
    } else {
        echo '<h1>Terjadi kesalahan!</h1><p>Tidak dapat mengaitkan akun manapun dengan id ' . $id . '</p>';
        goto endDScript;
    }
} else {
    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan saat menjalan query: ' . mysqli_error($dbc) . '</p>';
    goto endDScript;
}

// Validate the form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $q = "DELETE FROM tb_akun WHERE kode_akun = $id LIMIT 1";
    $r = @mysqli_query($dbc, $q);
    if ($r) {
        if (mysqli_affected_rows($dbc) == 1) {
            echo '<h1>Data berhasil dihapus.</h1>';
        } else {
            echo '<h1>Tidak ada perubahan yang terjadi.</h1>';
        }
    } else {
        echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan saat menjalankan query: ' . mysqli_error($dbc) . '</p>';
    }
} else {
    echo '<form action="hapus_akun.php" method="post">
        <h1>Hapus akun : ' . $id . ' ?</h1>
        <p>Email: ' . $row['email'] . '</p>
        <p>Akses: ' . $row['akses'] . '</p>
        <p>Kode Pegawai: ' . $row['kode_pegawai'] . '</p>
        <p>
          <input name="konfirmasi" type="radio" value="1" />Ya
          <input name="konfirmasi" type="radio" value="0" checked="checked" />Tidak
        </p>
        <p><input name="id" type="hidden" value="' . $id . '" /></p>
        <p><input name="submit" type="submit" value="Hapus" /></p>
      </form>';

}  // End of form submission.

endDScript:

mysqli_free_result($r2);
mysqli_close($dbc);

endScript:

include('includes/footer.html');
?>