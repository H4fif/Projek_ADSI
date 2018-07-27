<?php  # Script input_pegawai.php
// This script insert new data pegawai to the database.

// Set the page title and include the header:
$page_title = 'Data Pegawai Baru';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || ($_SESSION['user_level'] != 'administrator')) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}  // End of user validation.

// Validate form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $val = array_map('strip_tags', $_POST);  // Remove any HTML and PHP tags.

    $val = array_map('htmlentities', $val);  // Convert all applicable characters to HTML entities.
    
    $val = array_map('trim', $val);  // Remove any whitespaces.

    $errors = [];  // Initialize $errors variable to store error messages.

    // Validate nama lengnkap:
    // It should at least contain 2 characters or 100 characters at maximum.
    if (empty($val['nama_lengkap']) || !preg_match('/^[\w+|\056*|\'*|\040*]{2,100}$/', $val['nama_lengkap'])) {
        
        $errors[] = 'Nama tidak valid!';  // Set an error message.
    
    }  // End of nama lengkap validation.

    // Validate jenis kelamin:
    if (!isset($val['jenis_kelamin'])) {  // If it is not set, show an error message.
        
        $errors[] = 'Jenis kelamin tidak valid!';  // Set an error message.

    }  // End of jenis kelamin validation.

    // Validate no telepon:
    // It should at least contain 5 digits or 15 digits at maximum.
    if (empty($val['no_telp']) || !is_numeric($val['no_telp']) || (strlen($val['no_telp']) < 3) || (strlen($val['no_telp']) > 15)) {
        
        $errors[] = 'No telepon tidak valid!';  // Set an error message.

    }  // End of no telepon validation.

    // Validate alamat:
    // It should at least contain 4 characters.
    if (empty($val['alamat']) || (strlen($val['alamat']) < 4)) {
        
        $errors[] = 'Alamat tidak valid!';  // Set an error message.
    
    }  // End of alamat validation.

    // Validate $errors variable:
    if (!empty($errors)) {  // If there is any error occurred, show the error message.
        
        // Display the heading and start the paragraph.
        echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />'; 

        // Show all the errors occurred:
        foreach ($errors as $v) {
            
            echo ' - ' . $v . '<br />';  // Show the error message.

        }  // End of foreach loop.

        echo '</p>';  // End of paragraph.

    } else {  // If there is no error occurred, continue the script.

        require('mysqli_connect.php');  // Need the database connection.

        $nl = mysqli_real_escape_string($dbc, $val['nama_lengkap']);  // Escape the nama lengkap.

        $jk = mysqli_real_escape_string($dbc, strtoupper($val['jenis_kelamin']));  // Escape the jenis kelamin.

        $nt = mysqli_real_escape_string($dbc, $val['no_telp']);  // Escape the no telepon.

        $a = mysqli_real_escape_string($dbc, $val['alamat']);  // Escape the alamat.

        // Make the query:
        $q = "SELECT * FROM tb_pegawai WHERE nama_lengkap = '$nl' AND jenis_kelamin = '$jk' AND no_telepon = '$nt'";
        
        $r = @mysqli_query($dbc, $q);  // Execute the query.
        
        // Validate the query result:
        if ($r) {  // If query succeed, check the returned row.

            // Validate the returned row:
            if (mysqli_num_rows($r) != 0) {  // If there is a duplicate data, set an error message.
                
                $errors[] = 'Data pegawai tersebut sudah ada!';  // Set an error message.

            }  // End of returned row validation.

        } else {  // If query failed, show an error message.

            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Show an error message.
            goto endScript;
        }  // End of query result validation.
        mysqli_free_result($r);  // Free up the resources.

        // Validate the $errors variable:
        if (!empty($errors)) {  // If there is any error occurred, show them.

            echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />';  // Show an error message.
            
            // Show all the error messages:
            foreach ($errors as $v) {

                echo ' - ' . $v . '<br />';  // Show the error message.

            }  // End of foreach loop.

            echo '</p>';  // End of the paragraph.
            // goto inputForm;

        } else {  // If there is no error occurred, continue the script.

            // Make the query to validate the user:
            $q = "INSERT INTO tb_pegawai (nama_lengkap, jenis_kelamin, no_telepon, alamat) VALUES ('$nl', '$jk', '$nt', '$a')";
            
            $r = @mysqli_query($dbc, $q);  // Execute the query.
            
            // Validate the query result:
            if ($r) {  // If query succeed, check the returned row.

                // Valdiate the returned row:
                if (mysqli_affected_rows($dbc) == 1) {  // If data succeed to be saved, display a message, exit the script.
                    
                    echo '<p>Data berhasil disimpan.</p>';  // Display a message.
                    goto endScript;
                } else {  // If data failed to be saved, display a message.

                    echo '<p>Terjadi kesalahan pada sistem saat menyimpan data.</p>';  // Display a message.

                }  // End of returned row validation.

            } else {  // If query failed, display a message.

                echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  //  Display a message.
        
            }  // End of query result validation.
        
        }  // End of $errors validation.
    
        mysqli_close($dbc);
        unset($_POST);
    }  // End of $errors validation.
} //else {
  // inputForm:
    // Create the form:
    echo '<form name="input_data_pegawai" action="input_pegawai.php" method="post">
        <p>Nama Lengkap: <input name="nama_lengkap" type="text" minlength="2" maxlength="100" required="required"' . ((isset($val['nama_lengkap']) ? 'value="' . $val['nama_lengkap'] . '"' : '')) . '" /></p>
        <p>Jenis Kelamin:
          <input name="jenis_kelamin" type="radio" value="L" required="required"' . ((isset($val['jenis_kelamin']) && ($val['jenis_kelamin'] == 'L')) ? ' checked="checked"' : '') .
          '/>Laki-Laki <input name="jenis_kelamin" type="radio" value="P" required="required"' . ((isset($val['jenis_kelamin']) && ($val['jenis_kelamin'] == 'P')) ? ' checked="checked"' : '') . '/>Perempuan</p>
        <p>No. Telepon: <input name="no_telp" type="text" minlength="5" maxlength="15" required="required"' . ((isset($val['no_telp'])) ? ' value="' . $val['no_telp'] . '"' : '') . ' /></p>
        <p>Alamat: <textarea name="alamat" cols="40" rows="5" required="required">' . ((isset($val['alamat'])) ? $val['alamat'] : '') . '</textarea></p>
        <p><input name="submit" type="submit" value="Simpan" /></p>
      </form>';
// }  // End of FORM submission.

endScript:

echo '<p><a class="navlink" href="lihat_pegawai.php">Kembali</a></p>';
include('includes/footer.html');  // Include the footer.
?>