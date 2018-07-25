<?php  # Script edit_akun.php
// This page for editing existing akun.

// Set the page title:
$page_title = 'Edit Akun';

// Include the header:
include('includes/header.html');

// Validate the user.
// Only user that has logged in can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || ($_SESSION['user_level'] != 'administrator')) {
    
    header('Location: index.php');  // Redirect the user to homepage.

    goto endScript;

}  // End of user validation.

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
} else {
  idError:
    echo '<p>Terjadi kesalahan saat mencoba mengakses halaman ini.</p>';
    goto endScript;
}

// Validate the user access.
// Only user with level administrator or that has logged in could access this page.
// If the user does not have the right access, display an error message:
/* if (($_SESSION['user_level'] != 'administrator') && ($_SESSION['user_id'] != $_GET['id'])) {
    
    echo '<p>Halaman ini tidak dapat diakses, hubungi administrator.</p>';
    goto endScript;
    
}  // End of user access validation. */

require('mysqli_connect.php');

$q = "SELECT * FROM tb_akun WHERE kode_akun = $id";
$r2 = @mysqli_query($dbc, $q);

// Validate the query result whether it succeed or failed.
if ($r2) {

    // Validate the returned row, whether it is a valid user or not.
    if (mysqli_num_rows($r2) != 1) {
        goto idError;
    } else {
        $row = mysqli_fetch_array($r2, MYSQLI_ASSOC);
    }  // End of if (mysqli_num_rows).

} else {
    echo '<h1>Terjadi kesalahan saat menjalankan query</h1><p>Query: ' . mysqli_error($dbc) . '</p>';
    goto endDScript;
}  // End of if ($r).

// If it passes all validation, do this:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $val = array_map('strip_tags', $_POST);
    $val = array_map('htmlentities', $val);
    $val = array_map('trim', $val);

    $errors = [];

    if (empty($val['email']) || !filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid!';
    }

    if (!empty($val['kata_sandi']) && (strlen($val['kata_sandi']) >= 4)) {

        if ($val['kata_sandi'] != $val['kata_sandi2']) {

            $errors[] = 'Kata sandi tidak sama dengan konfirmasi kata sandi!';

        }

    } else {
        $errors[] = 'Kata sandi tidak valid!';
    }

    if (!empty($val['kode_pegawai']) && is_numeric($val['kode_pegawai'])) {
        $kp = mysqli_real_escape_string($dbc, $val['kode_pegawai']);

        $q = "SELECT COUNT(*) FROM tb_akun WHERE kode_pegawai = $kp";
        $r = @mysqli_query($dbc, $q);
        $row = mysqli_fetch_array($r, MYSQLI_NUM);
        if ($row[0] != 1) {
            $errors[] = 'Kode pegawai tidak valid!';
        }
    } else {
        $errors[] = 'Kode pegawai tidak boleh kosong!';
    }

    if (!empty($errors)) {
        echo '<h1>Terjadi kesalahan!</h1><p stype="color: red">';
        foreach ($errors as $e) {
            echo ' - ' . $e . '<br />';
        }
        echo '</p>';

        goto finput;
        
    } else {

        $e = mysqli_real_escape_string($dbc, $val['email']);
        $ks = mysqli_real_escape_string($dbc, $val['kata_sandi']);
        $id = mysqli_real_escape_string($dbc, $val['id']);

        $q = "UPDATE tb_akun SET email = '$e', kata_sandi = SHA1('$ks')";

        if ($_SESSION['user_level'] != 'administrator') {

            $a = mysqli_real_escape_string($dbc, $val['akses']);
            $kp = mysqli_real_escape_string($dbc, $val['kode_pegawai']);

            $q .= ", akses = '$a', kode_pegawai = $kp";

        }  // End of user validation.

        $q .= " WHERE kode_akun = '$id' LIMIT 1";
        $r = @mysqli_query($dbc, $q);

        // Validate the query result, whether it succeed or failed.
        if ($r) {
            if (mysqli_affected_rows($dbc) == 1) {

                echo '<p>Perubahan berhasil disimpan!</p>';

            } else {

                echo '<p>Tidak ada perubahan yang disimpan.</p>';

            }  // End of if (mysqli_affected_rows).

            echo '<p><a class="navlink" href="edit_akun.php?id=' . $id . '">Kembali</a></p>
                  <p><a class="navlink" href="lihat_akun.php">Daftar Semua Akun</a></p>';

        } else {

            echo '<h2>Terjadi kesalahan saat menjalankan query</h2><p>Query: ' . mysqli_error($dbc) . '</p>';

        }  // End of if ($r).
      
    }  // End of if (empty($val)).
} else {
    finput:

    echo '<form action="edit_akun.php" method="post">
        <p>Email: <input name="email" type="email" maxlength="40" required="required" value="' . $row['email'] . '"/></p>
        <p>Kata Sandi Baru: <input name="kata_sandi" type="password" minlength="4" maxlength="20" required="required" /></p>
        <p>Konfirmasi Kata Sandi: <input name="kata_sandi2" type="password" minlength="4" maxlength="20" required="required" /></p>';

    // Validate the user to display additional option to be updated:
    if (($_SESSION['user_level'] == 'administrator') && ($_SESSION['user_id'] != $id)) {

        $arr_akses = ['administrator', 'manager', 'gudang', 'kasir'];

        echo '<p>Akses: <select name="akses"><option value="default">-- Pilih hak akses --</option>';

        foreach ($arr_akses as $v) {
            echo '<option value="' . $v . '"';
            if ($v == $row['akses']) {
                echo ' selected="selected"';
            }
            echo '>' . ucfirst($v) . '</option>';
        }

        echo '</select></p>
          <p>Kode Pegawai: <input name="kode_pegawai" type="text" minlength="1" maxlength="11" required="required" value="' . $row['kode_pegawai'] . '" /></p>';
        unset($arr_akses);

    }  //  End of user validation.

    echo '<p><input name="id" type="hidden" value="' . $id . '" /></p>
      <p><input name="submit" type="submit" value="Simpan" /></p></form>';

}  // End of form submission.

endDScript:

mysqli_free_result($r2);
mysqli_close($dbc);

endScript:
echo '<p><a class="navlink" href="lihat_akun.php">Kembali</a></p>';

include('includes/footer.html');
?>