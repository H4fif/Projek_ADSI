<?php  # Script buat_akun.php
// This page for creating new account.

// Set the page title and include the header:
$page_title = 'Buat Akun';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATORcan access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || ($_SESSION['user_level'] != 'administrator')) {
    
    header('Location: index.php');  // Redirect the user to homepage.

    exit;  // Exit the script.

}  // End of user validation.

require('mysqli_connect.php');  // Need the database connection.

// $q = 'SELECT kode_pegawai FROM tb_pegawai ORDER BY kode_pegawai ASC';  // Make the query.

$q = 'SELECT pg.kode_pegawai FROM tb_pegawai AS pg LEFT JOIN tb_akun USING (kode_pegawai) WHERE kode_akun IS NULL';  // Make the query.


$r_tp = @mysqli_query($dbc, $q);  // Execute the query.

if ($r_tp) {  // If query succeed, check the returned row.

    if (mysqli_num_rows($r_tp) == 0) {  // If no record is returned, display a message, exit the script.

        echo 'Tidak ada data pegawai untuk dikaitkan.';

        mysqli_free_result($r_tp);  // Free up the resources.

        mysqli_close($dbc);  // Close the database connection.

        include('includes/footer.html');  // Include the footer.
        
        exit();  // Exit the script.
    
    }  // End of if (mysqli_num_rows).

} else {  // If the query failed, display the error message, exit the script.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';

    mysqli_free_result($r_tp);  // Free up the resources.

    mysqli_close($dbc);  // Close the database connection.

    include('includes/footer.html');  // Include the footer.

    exit();  // Exit the script.
}

// Validate form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];  // Initialize variable for error message(s).

    $val = array_map('strip_tags', $_POST);  // Remove any HTML and PHP tags.
    
    $val = array_map('htmlentities', $val);  // Convert all applicable characters to HTML entities.

    $val = array_map('trim', $val);  // Remove any whitespace character.

    // Validate the email:
    if (empty($val['email']) || !filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {  // If email did not match the formatted pattern, set an error message.

        $errors[] = 'Alamat email tidak valid!';  // Add an error message to $errors variable.

    }  // End of email validation.

    // Validate the kata sandi:
    if (empty($val['kata_sandi']) || (strlen($val['kata_sandi']) < 4)) {  // If kata sandi is empty, or have length < 4 or length > 20, set an error message.

        $errors[] = 'Kata sandi tidak valid!';  // Add an error message to $errors variable.

    } else {  // If kata sandi not empty, do one validation again.

        // Validate kata sandi with the konfirmasi kata sandi:
        if ($val['kata_sandi'] != $val['kata_sandi2']) {  // If kata sandi did not match the konfirmasi kata sandi, set an error message.

            $errors[] = 'Kata sandi tidak sama dengan konfirmasi!';  // Add an error message to $errors variable.

        }  // End of konfirmasi kata sandi validation.

    }  // End of kata sandi validation.

    // Validate the akses:
    if (!isset($val['akses']) || ($val['akses'] == -1)) {  // If akses has default value, set an error message.

        $errors[] = 'Hak akses tidak valid!';  // Add an error message to $errors variable.
    
    }  // End of akses validation.

    // Validate the kode pegawai:
    if (empty($val['kode_pegawai']) || ($val['kode_pegawai'] == -1)) {  // If kode pegawai has default value, set an error message.

        $errors[] = 'Kode Pegawai tidak valid!';  // Add an error message to $errors variable.

    }  // End of kode pegawai validation.

    // Validate the $errors variable:
    if (empty($errors)) {  // If no error(s) occurred, continue the script.
        
        $e = mysqli_real_escape_string($dbc, $val['email']);  // Escape the email.

        $kp = mysqli_real_escape_string($dbc, $val['kode_pegawai']);  // Escape the kode pegawai.

        $q = "SELECT * FROM tb_akun WHERE email = '$e' OR kode_pegawai = {$val['kode_pegawai']}";  // Make the query.
        
        $r = @mysqli_query($dbc, $q);  // Execute the query.

        // Validate the query result:
        if ($r) {  // If query succeed, check the returned row.

            // Validate the returned row:
            if (mysqli_num_rows($r) == 1) {  // If there is a record with the same email or kode pegawai, set an error message

                // Add an error message to $errors.
                $errors[] = 'Akun tersebut sudah terdaftar atau pegawai dengan kode pegawai tersebut sudah memiliki akun!';
            
            }  // End of returned row validation.

        } else {  // If query failed, show an error message, exit the script:

            // Show an error message:
            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';

            mysqli_free_result($r);  // Free up the resources.

            mysqli_close($dbc);  // Close the database connection.

            include('includes/footer.html');  // Include the footer.
            
            exit();  // Exit the script.

        }  // End of query result validation.

    }  // End of $errors validation.

    // Validate the $errors variable:
    if (!empty($errors)) {  // If there is any error occurred, show an error message.

        // Set the header, and the start of the paragraph:
        echo '<h1>Terjadi Kesalahan!</h1><p>Kesalahan:<br />';

        foreach ($errors as $v) {  // Display all the errors.
            
            echo ' - ' . $v . '<br />';  // Output the error message.
        }

        echo '</p>';  // Close the paragraph.

    } else {  // If there is no error occurred.

        $p = mysqli_real_escape_string($dbc, $val['kata_sandi']);  // Escape the kata sandi.

        $a = mysqli_real_escape_string($dbc, $val['akses']);  // Escape the akses.

        // Make the query:
        $q = "INSERT INTO tb_akun (email, kata_sandi, akses, kode_pegawai) VALUES ('$e', SHA1('$p'), '$a', $kp)";
        
        $r = @mysqli_query($dbc, $q);  // Execute the query.

        // Validate the query result:
        if ($r) {  // If query succeed, show a message, exit the script.

            if (mysqli_affected_rows($dbc) == 1) {  // If data is saved, close the connection, exit the script.

                echo '<p>Data telah disimpan, terima kasih.';  // Show a message.

                include('includes/footer.html');  // Include the footer.
                
                mysqli_close($dbc);  // Close the database connection.
                
                exit();  // Exit the script.

            } else {  // If data could not be saved, show an error message.

                // Show an error message.
                echo '<h1>Terjadi kesalahan!</h1><p>Maaf, akun tersebut tidak dapat terdaftar karena terjadi kesalahan pada sistem.</p>';

            }  // End of if (mysqli_num_rows($r)).
        
        } else {  // If query failed, show an error message.

            // Show an error message:
            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';

        }  // End of if ($r).

    }  // End of if (!empty($errors)).

}  // End of form submission.

// Create the form:
echo '<form name="buat_akun" action="buat_akun.php" method="post">
  <p>Email: <input name="email" type="email" minlength="6" maxlength="40" required="required" value="' . ((isset($val['email'])) ? $val['email'] : '') . '" /></p>
  <p>Kata Sandi: <input name="kata_sandi" type="password" minlength="4" maxlength="20" required="required" value="' . ((isset($val['kata_sandi'])) ? $val['kata_sandi'] : '') . '" /></p>
  <p>Konfirmasi Kata Sandi: <input name="kata_sandi2" type="password" minlength="4" maxlength="20" required="required" value="' . ((isset($val['kata_sandi2'])) ? $val['kata_sandi2'] : '') . '" /></p>
  <p>Akses: <select name="akses"><option value="-1">-- Pilih Akses --</option>';

// Initialize $arr_akses with akses values for use in loop.
// This array has the total of 5 values, with 1 default value.
$arr_akses = ['administrator', 'manager', 'gudang', 'kasir'];

// Show the akses option values:
// for ($i = 0; $i < 5; $i++) {  // Iterate 5 times.

foreach ($arr_akses as $v) {

    echo '<option value="' . $v . '"';  // Show the option and the value.

    // Validate the akses:
    if (isset($val['akses'])) {  // If the akses is set, make the option object sticky.

        // Validate the akses value with certain numbers:
        if ($val['akses'] == $v) {  // If the akses is the same with the certain number, make the html document object sticky.
            
            echo ' selected="selected"';  // Make the html document object sticky.
        
        }  // End of akses value validation.

    }  // End of akses validation.

    echo '>' . ucfirst($v) . '</option>';  // Show the caption.

}  // End of for loop (output akses option).

unset($arr_akses);

// Continue the form:
echo '</select></p>
  <p>Kode Pegawai: <select name="kode_pegawai">
  <option value="-1">-- Pilih Satu --</option>';

// Display the kode pegawai as the select object elements:
while ($row = mysqli_fetch_array($r_tp, MYSQLI_NUM)) {  // Fetching from the database.

    echo '<option value="' . $row[0] . '"';  // Output the option value.

    // Validate the kode pegawai:
    if (isset($val['kode_pegawai'])) {  // If the kode pegawai is set, make the html document object sticky.
      
        // Validate the kode pegawai's value the certain value from database:
        if ($row[0] == $val['kode_pegawai']) {  // If the kode pegawai is the same with certain value, make the html document object sticky.
            
            echo ' selected="selected"';  // Make the html document object sticky.
        
        }  // End of kode pegawai validation.

    }  // End of kode pegawai's value validation.

    echo '>' . $row[0] . '</option>';  // Show the caption.
}

// Continue the from.
echo '</select></p>
    <p><input type="submit" name="submit" value="Buat Akun!" /></p>
  </form>';

mysqli_free_result($r_tp);  // Free up the resources.

mysqli_close($dbc);  // Close the database connection.

echo '<p><a class="navlink" href="lihat_akun.php">Kembali</a></p>';
include('includes/footer.html');  // Include the footer.
?>