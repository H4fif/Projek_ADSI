<?php # Script edit_pegawai.php
// This script update data pegawai.

// testing section

/* $a = array('a1' => '', 'a2' => '');
$a = array_map('empty', $a);
// $a1 = array();

var_dump(empty($a));

exit; */

// end of testing section


$page_title = 'Edit Data Pegawai';
include('includes/header.html');

// User validation:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']))) {
    header('Location: index.php');
}  // End of IF user validation.

// Validate the USER ID:
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
} else {
  idError:
    echo '<p>Terjadi kesalahan saat mencoba mengakses halaman ini.<p>';
    goto endScript;
}  // End of IF user id validation.

require('mysqli_connect.php');

$q = "SELECT * FROM tb_pegawai WHERE kode_pegawai = $id";
$r = @mysqli_query($dbc, $q);
if ($r) {
    if (mysqli_num_rows($r) == 1) {
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
    } else {
        goto idError;
    }  // End of IF mysqli_num_rows.
} else {
    goto idError;
}  // End of IF ($r).

// Validate the FORM SUBMISSION:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $val = array_map('strip_tags', $_POST);
    $val = array_map('htmlentities', $val);
    $val = array_map('trim', $val);

    $errors = [];

    // Validate nama:
    if (empty($val['nama']) && !preg_match("/^[\w+|\040*|\'*|\056*]{2,100}$/", $val['nama'])) {
        $errors[] = 'Nama tidak valid!';
    }  // End IF.

    // Validate jenis kelamin:
    if (empty($val['jk']) || !in_array($val['jk'], ['L', 'P'])) {
        $errors[] = 'Jenis kelamin tidak valid!';
    }  // End IF.

    // Validate nomor telepon:
    if (empty($val['no_telp']) || !is_numeric($val['no_telp']) || (strlen($val['no_telp']) < 3) || (strlen($val['no_telp']) > 15)) {
        $errors[] = 'No. Telepon tidak valid!';
    }  // End IF.

    // Validate alamat:
    if (empty($val['alamat']) || (strlen($val['alamat']) < 4) || (strlen($val['alamat']) > 255)) {
        $errors[] = 'Alamat tidak valid!';
    }  // End IF.

    checkError:
    if (!empty($errors)) {
        echo '<h2>Terjadi kesalahan!</h2><p>';
        foreach ($errors as $e) {
            echo ' - ' . $e . '<br />';
        }
        echo '</p>';
        goto formEdit;
    } else {
        $nl = mysqli_real_escape_string($dbc, $val['nama']);
        $jk = mysqli_real_escape_string($dbc, $val['jk']);
        $nt = mysqli_real_escape_string($dbc, $val['no_telp']);
        $a = mysqli_real_escape_string($dbc, $val['alamat']);

        $q = "SELECT * FROM tb_pegawai WHERE (kode_pegawai != $id AND ((nama_lengkap = '$nl' AND jenis_kelamin = '$jk' AND alamat = '$a') OR (no_telepon = '$nt')))";

        $r = @mysqli_query($dbc, $q);
        if ($r) {
            if (mysqli_num_rows($r) != 0) {
                $errors[] = 'Data sudah ada!';
                goto checkError;
            } else {
                $q = "UPDATE tb_pegawai SET nama_lengkap = '$nl', jenis_kelamin = '$jk', no_telepon = '$nt', alamat = '$a' WHERE kode_pegawai = $id LIMIT 1";
                $r = @mysqli_query($dbc, $q);
                if ($r) {
                    if (mysqli_affected_rows($dbc) == 1) {
                        echo '<p>Perubahan berhasil disimpan.</p>';
                    } else {
                        echo '<p>Tidak ada perubahan yang disimpan.</p>';
                    }
                } else {
                    echo '<h2>Terjadi kesalahan sistem!</h2><p>Query: ' . mysqli_error($dbc) . '</p>';
                }  // End of IF ($r).
            }  // End of IF (mysqli_num_rows).
        } else {
            echo '<h2>Terjadi kesalahan sistem!</h2><p>Query: ' . mysqli_error($dbc) . '</p>';
        }  // End of IF ($r).
    }  // End of IF (!empty($error)).
} else {

// UPDATE THIS SOON!

// Make the form sticky
  formEdit:

    // Make the nama sticky:
    if (isset($val['nama'])) {
        $stNama = $val['nama'];
    } else {
        $stNama = $row['nama_lengkap'];
    }

    // Make the jenis kelamin sticky:
    if (isset($val['jk'])) {
        $stJk = $val['jk'];
    } else {
        $stJk = $row['jenis_kelamin'];
    }

    // Make the no_telp sticky:
    if (isset($val['no_telp'])) {
        $stNt = $val['no_telp'];
    } else {
        $stNt = $row['no_telepon'];
    }

    // Make the alamat sticky:
    if (isset($val['alamat'])) {
        $stA = $val['alamat'];
    } else {
        $stA = $row['alamat'];
    }

    echo '<form action="edit_pegawai.php" method="post">
        <p>Nama: <input name="nama" type="text" minlength="2" maxlength="100" value="' . $stNama . '" /></p>
        <p>Jenis Kelamin: <input name="jk" type="radio" value="L" ' . (($stJk == 'L') ? ' checked="checked"' : '') . '/>Laki-laki <input name="jk" type="radio" value="P" ' . (($stJk == 'P') ? ' checked="checked"' : '') . ' />Perempuan</p>
        <p>No. Telepon: <input name="no_telp" type="text" minlength="3" maxlength="15" value="' . $stNt . '" /></p>
        <p>Alamat: <textarea name="alamat" row="6" col="50">' . $stA . '</textarea></p>
        <p><input name="id" type="hidden" value="' . $id . '" /></p>
        <p><input name="submit" type="submit" value="Simpan" /></p>
      </form>';
}

endScript:

mysqli_close($dbc);
echo '<p><a class="navlink" href="lihat_pegawai.php">Kembali</a></p>';

include('includes/footer.html');
?>