<?php  # Script input_pelanggan.php
// This script insert new data pelanggan to the database.

/***** FUNCTION DICTIONARY *****/

// This function to show query error.
// Need 1 argument, database connection.
function show_query_error($dbc){
    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';
}  // End of show_query_error FUNCTION.

/***** END FUNCTION DICTIONARY *****/

// Set the page title and include the header:
$page_title = 'Data Pelanggan Baru';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || !in_array($_SESSION['user_level'], ['administrator', 'kasir'])) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}  // End of user validation.

// Validate form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $val = array_map('htmlentities', $_POST);
    $val = array_map('trim', $val);

    $errors = [];

    // Validate nama lengkap:
    // It should at least contain 2 characters or 150 characters at maximum.
    if (empty($val['nama_pelanggan']) || !preg_match('/^[\w+|\056*|\'*|\040*]{2,150}$/', $val['nama_pelanggan'])) {
        
        $errors[] = 'Nama tidak valid!';  // Set an error message.
    
    }  // End of nama lengkap validation.

    // Validate jenis kelamin:
    if (!isset($val['jk'])) {  // If it is not set, show an error message.
        
        $errors[] = 'Jenis kelamin tidak valid!';  // Set an error message.

    }  // End of jenis kelamin validation.

    // Validate no telepon:
    // It should at least contain 5 digits or 15 digits at maximum.
    if (empty($val['no_telp']) || !is_numeric($val['no_telp']) || (strlen($val['no_telp']) < 3) || (strlen($val['no_telp']) > 15)) {
        
        $errors[] = 'No telepon tidak valid!';  // Set an error message.

    }  // End of no telepon validation.

    // Validate alamat:
    // It should at least contain 4 characters, at max 255 chars.
    if (empty($val['alamat']) || (strlen($val['alamat']) < 4) || (strlen($val['alamat']) > 255)) {
        
        $errors[] = 'Alamat tidak valid!';  // Set an error message.
    
    }  // End of alamat validation.

    // Validate $errors variable:
    if (!empty($errors)) {
        echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />';

        foreach ($errors as $v) {
            echo ' - ' . $v . '<br />';
        }

        echo '</p>';
    } else {
        require('mysqli_connect.php');

        $np = mysqli_real_escape_string($dbc, $val['nama_pelanggan']);
        $jk = mysqli_real_escape_string($dbc, $val['jk']);
        $nt = mysqli_real_escape_string($dbc, $val['no_telp']);
        $a = mysqli_real_escape_string($dbc, $val['alamat']);

        $q = "SELECT * FROM tb_pelanggan WHERE (nama_pelanggan = '$np' AND jenis_kelamin = '$jk' AND alamat = '$a') OR (no_telepon = '$nt')";
        $r = @mysqli_query($dbc, $q); 

        // Validate the query result:
        if ($r) {
            if (mysqli_num_rows($r) != 0) {
                $errors[] = 'Data pegawai tersebut sudah ada!';
            }
            mysqli_free_result($r);
        } else {
            show_query_error($dbc);
            goto endScript;
        }  // End of IF ($r).

        // Validate the $errors:
        if (!empty($errors)) {
            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />';

            foreach ($errors as $v) {
                echo ' - ' . $v . '<br />';
            }

            echo '</p>';
        } else {
            $q = "INSERT INTO tb_pelanggan (nama_pelanggan, jenis_kelamin, no_telepon, alamat) VALUES ('$np', '$jk', '$nt', '$a')";
            $r = @mysqli_query($dbc, $q);

            // Validate the query result:
            if ($r) {
                if (mysqli_affected_rows($dbc) == 1) {
                    echo '<p>Data berhasil disimpan.</p>';
                    goto endScript;
                } else {
                    echo '<p>Terjadi kesalahan pada sistem saat menyimpan data.</p>';
                }  // End of IF affected_rows.
            } else {
                show_query_error($dbc);
            }  // End of IF ($r).
        }  // End of IF (!empty($errors)).
        mysqli_close($dbc);
        unset($_POST);
    }  // End of IF (!empty($errors)).
}  // End of IF form submission.
// Display the form:
echo '<form name="input_pelanggan" action="input_pelanggan.php" method="post">
  <p>Nama Lengkap: <input name="nama_pelanggan" type="text" minlength="2" maxlength="150" required="required"' . ((isset($val['nama_pelanggan'])) ? ' value="' . $val['nama_pelanggan'] . '"' : '') . '" /></p>
  <p>Jenis Kelamin:
    <input name="jk" type="radio" value="L" required="required"' . ((isset($val['jk'])) ? ' checked="checked"' : '') . ' />Laki-laki
    <input name="jk" type="radio" value="P" required="required"' . ((isset($val['jk'])) ? ' checked="checked"' : '') . ' />Perempuan</p>
  <p>No. Telepon: <input name="no_telp" type="text" minlength="5" maxlength="15" required="required"' . ((isset($val['no_telp'])) ? ' value="' . $val['no_telp'] . '"' : '') . ' /></p>
  <p>Alamat: <textarea name="alamat" cols="40" rows="5" required="required">' . ((isset($val['alamat'])) ? $val['alamat'] : '') . '</textarea></p>
  <p><input name="submit" type="submit" value="Simpan" /></p>
</form>';

endScript:

echo '<p><a class="navlink" href="lihat_pelanggan.php">Kembali</a></p>';
include('includes/footer.html');
?>