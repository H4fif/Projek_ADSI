<?php  # Script lihat_akun.php
// This page for view registered accounts in the database.

/* FUNCTIONS */

// Function to check whether the number is kelipatan 5.
// Need 1 parameter $value, should be numeric int.
function check_five($value) {
    if (($value % 5) == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function check_start($value, $display) {
    if (($value % $display) == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/* END FUNCTIONS */

// Set the page title and include the header:
$page_title = 'Lihat Akun';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] !=   md5($_SERVER['HTTP_USER_AGENT'])) || ($_SESSION['user_level'] != 'administrator')) {
    ob_end_clean();
    header('Location: index.php');  // Redirect the user to the home page.
    exit();  // Exit the script.
}  // End of user validation.

$cari_error = FALSE;

// Validate the search:
// If both values have a valid value, secure them.
if (!empty($_GET['col']) && !empty($_GET['keyword']) && in_array(strtolower($_GET['col']), [1, 2, 3])) {
    
    $column = trim(htmlentities(strip_tags($_GET['col'])));  // Secure the $_GET['col'] value.

    $keyword = trim(htmlentities(strip_tags($_GET['keyword'])));  // Secure the $_GET['keyword'] value.

} else {  // If both values are not set or invalid, represent them as flag variable.

    $column = $keyword = FALSE;  // Set flag variables to FALSE.

}  // End of search validation.

// Validate the sorting value:
// If it is not set or have invalid value, set the $s to default value.

  // Validate the SORTING and ORDER BY value:
if (
  !isset($_GET['s'], $_GET['o']) || !in_array(strtolower($_GET['s']), ['email', 'akses', 'kode_pegawai']) || !in_array(filter_var($_GET['o'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2))), [1, 2])) {

    $s = 'email';  // Set the default value.

    $o = 1;  // Set the sort to ASCENDING.

} else {

    $s = $_GET['s'];  // Set the $s variable with the given value.
    
    $o = $_GET['o'];  // Set the $o variable with the given value.

} // End of sorting type validation.

// Validate the DISPLAY value:
if (!isset($_GET['display']) || (!is_numeric($_GET['display'])) || !check_five($_GET['display']) || !in_array($_GET['display'], [5, 10, 15])) {

    if (isset($_SESSION['display'])) {
        $display = $_SESSION['display'];
    } else {

        $display = 5;
    }

} else {
    $display = $_GET['display'];
}

$_SESSION['display'] = $display;

// Set the sorting type:
// 1 -> for ASCENDING.
// 2 -> for DESCENDING.
if ($o == 1) {  // If it's 1, set to ASCENDING.
    
    $ot = 'ASC';  // Set $ot to ASCENDING.

} else {  // If it's 2, set to DESCENDING.

    $ot = 'DESC';  // Set $ot to descending.

}  // End of if ($o == 1).

require('mysqli_connect.php');  // Need the database connection.

// Validate the search input:
if ($column && $keyword) {  // If both are true, continue.

    // Validate the $column variable.
    // Determine the variable for the query search.
    switch ($column) {
        case '1' :
            $colkey = 'email';
            $hpc = 'Email';
            break;
        case '2' :
            $colkey = 'akses';
            $hpc = 'Akses';
            break;
        case '3' :
            $colkey = 'kode_pegawai';
            $hpc = 'Kode Pegawai';
            break;
    
    }  // End of SWITCH.

    $keyword = mysqli_real_escape_string($dbc, $keyword);  // Escape the $keyword.

    $colkey = mysqli_real_escape_string($dbc, $colkey);  // Escape the $colkey.

    $fq = " WHERE $colkey LIKE '%$keyword%'";  // Set the addition text for query.

    $search = TRUE;  // Flag variable.

} else {  // If both are FALSE, set the flag variable to FALSE.
    
    $fq = "";  // Set the addition text query to empty

    $search = FALSE;  // Flag variable.

}  // End of search input validation.

$q = "SELECT COUNT(*) FROM tb_akun $fq";
$r = @mysqli_query($dbc, $q) or die('<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>');
list($total_row) = mysqli_fetch_array($r, MYSQLI_NUM);
mysqli_free_result($r);

if (!$search && ($total_row == 0)) {
    echo '<p>Tidak ada data.</p>';
    goto endDScript;
}

// Validate the START value for pagination:
if (isset($_GET['start']) && check_start($_GET['start'], $display)) {
    $start = $_GET['start'];
} else {
    $start = 0;
}

/* // Set the $pages value:
if ($total_row > $display) {
    $pages = ceil($total_row / $display);
} else {
    $pages = 1;
} */

$q = "SELECT * FROM tb_akun $fq ORDER BY $s $ot LIMIT $start, $display";  // Make the query.
$r = @mysqli_query($dbc, $q);  // Execute the query.

// Validate the query result:
if ($r) {  // If query succeed, check the returned row.

    // Dislay a form for searching.
    echo '<form name="pencarian" action="lihat_akun.php" method="get">
        <select name="col">
          <option value="">-- Filter berdasarkan --</option>
          <option value="1"';

    // Make the option -> EMAIL sticky.
    if (isset($_GET['col']) && ($_GET['col'] == '1')) {

        echo ' selected="selected"';  // Make it sticky.

    }  // End of sticky EMAIL.

    // Comlete the email option.
    echo '>Email</option>
          <option value="2"';

    // Make the option -> AKSES sticky.
    if (isset($_GET['col']) && ($_GET['col'] == '2')) {

        echo ' selected="selected"';  // Make it sticky.

    }  // End of sticky AKSES.

    // Complete the akses option.
    echo '>Akses</option>
          <option value="3"';

    // Make the option -> KODE PEGAWAI sticky.
    if (isset($_GET['col']) && ($_GET['col'] == '3')) {

        echo ' selected="selected"';  // Make it sticky.

    }  // End of sticky KODE PEGAWAI.

    // Complete the form and create the table.
    echo '>Kode Pegawai</option>
        </select>
        <input name="keyword" type="text" placeholder="Masukkan kata kunci" value="' . ((isset($_GET['keyword'])) ? $_GET['keyword'] : '') . '" />
        <input name="submit" type="submit" value="Filter" />
      </form><br />';

    // Validatethe search:
    if ($search) {
        echo '<h2>Hasil filter:</h2>
          <h3>' . $hpc . ': ' . stripslashes($keyword) . '</h3>';
    }

    // Validate the returned row:
    if ($search && $total_row == 0) {  //  If no record was returned, display a message, exit the script.
        echo '<p>Data tidak ditemukan.</p>';
            
    } else {  // If it has any record in return, show them.

        // Set the $pages value:
        if ($total_row > $display) {
            $pages = ceil($total_row / $display);
        } else {
            $pages = 1;
        }

        echo '<table border="0" cellpadding="5" cellspacing="5">
          <tr>
            <th>No.</th>
            <th>Email</th>
            <th>Akses</th>
            <th>Kode Pegawai</th>
            <th>Ubah</th>
            <th>Hapus</th>
          </tr>';

        $no = $start + 1;

        // Display all records:
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetching each record to $row variable.

            // Display the record:
            echo '<tr>
              <td>' . $no . '</td>
              <td>' . $row['email'] . '</td>
              <td>' . $row['akses'] . '</td>
              <td>' . $row['kode_pegawai'] . '</td>
              <td><a href="edit_akun.php?id=' . $row['kode_akun'] . '" class="navlink">Ubah</a></td>
              <td><a href="hapus_akun.php?id=' . $row['kode_akun'] . '" class="navlink">Hapus</a></td></tr>';

            $no++;  // Increment the number.

        }  // End of WHILE loop.

        echo '</table>';  // Close the table.

        if (isset($_GET['s'])) {
            $s = $_GET['s'];
        } else {
            $s = NULL;
        }

        if (isset($_GET['o'])) {
            $o = $_GET['o'];
        } else {
            $o = NULL;
        }

        // PAGINATION SECTION :
        if ($pages > 1) {
            echo '<p>Halaman ';
            
            $current_page = ($start / $display) + 1;

            if ($current_page != 1) {
                echo '<a class="navlink" href="lihat_akun.php?start=' . ($start - $display) . '&display=' . $display . '&col=' . $column . '&keyword='. $keyword . '&s=' . $s . '&o=' . $o . '">Sebelumnya</a> ';
            }

            for ($i = 1; $i <= $pages; $i++) {
                if ($current_page != $i) {
                    echo '<a class="navlink" href="lihat_akun.php?start=' . ($display * ($i - 1)) . '&display=' . $display . '&col=' . $column . '&keyword='. $keyword . '&s=' . $s . '&o=' . $o . '">' . $i . '</a> ';
                } else {
                    echo $i . ' ';
                }
            }

            if ($current_page != $pages) {
                echo '<a class="navlink" href="lihat_akun.php?start=' . ($start + $display) . '&display=' . $display . '&col=' . $column . '&keyword='. $keyword . '&s=' . $s . '&o=' . $o . '">Selanjutnya</a>';
            }

            echo '</p>';
        
        }  // End of pagination.

        echo '<p>Total: ' . $total_row . ' data.</p><br />';  // Display the total of record.

        // Validate the number of record:
        // If it has more than 1 record, show the sorting feature.
        if ($total_row > 1) {

            // Create the form.
            echo '<form action="lihat_akun.php" method="get">
              <p>Tampilkan per halaman: <select name="display">
                <option value="5"';

            if ($display == 5) {
                echo ' selected="selected"';
            } else {
                echo '';
            }

            echo '>5</option>
                <option value="10"';

            if ($display == 10) {
                echo ' selected="selected"';
            } else {
                echo '';
            }

            echo '>10</option>
                <option value="15"';

            if ($display == 15) {
                echo ' selected="selected"';
            } else {
                echo '';
            }

            echo '>15</option>
              </select></p>
              <p><select name="s">
                <option value="">-- Urut berdasarkan --</option>
                <option value="email"';

            // Make the email sticky.
            if (isset($_GET['s']) && ($_GET['s'] == 'email')) {

                echo ' selected="selected"';  // Make it sticky.
            
            }  // End of sticky email.

            // Complete the email option.
            echo '>Email</option>
                  <option value="akses"';

            // Make the akses sticky.
            if (isset($_GET['s']) && ($_GET['s'] == 'akses')) {

                echo ' selected="selected"';  // Make it sticky.

            }  // End of sticky akses.

            // Complete the option.
            echo '>Akses</option>
                  <option value="kode_pegawai"';

            // Make the kode pegawai sticky.
            if (isset($_GET['s']) && ($_GET['s'] == 'kode_pegawai')) {

                echo ' selected="selected"';  // Make it sticky.

            }  // End of sticky kode pegawai.

            // Complete the kode pegawai option.
            echo '>Kode Pegawai</option>
                </select>
                <select name="o">
                  <option value="">-- Urut dari --</option>
                  <option value="1"';

            // Make the first order sticky.
            if (isset($_GET['o']) && ($_GET['o'] == '1')) {

                echo ' selected="selected"';  // Make it sticky.

            }  // End of sticky first order.

            // Complete the option for first order type.
            echo '>A-Z</option>
                  <option value="2"';
            
            // Make the second order sticky.
            if (isset($_GET['o']) && ($_GET['o'] == '2')) {

                echo ' selected="selected"';  // Make it sticky.

            }  // End of sticky second order.

            // Complete the option and the form.
            echo '>Z-A</option>
                </select>';

            if ($column && $keyword) {
                echo '<input type="hidden" name="col" value="' . $column . '" />
                  <input type="hidden" name="keyword" value="' . $keyword . '" />';
            }

            echo '<input name="submit" type="submit" value="Refresh" /></p></form>';

        }  // End of total record validation.

    }  // End of returned row validation.

    if ($search) {
        echo '<p><a class="navlink" href="lihat_akun.php">Kembali</a></p>';
    }

} else {  // If query failed, display a message.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Display a message.

}  // End of query result validaton.

mysqli_free_result($r);  // Free up the resources.

endDScript:

echo '<p><a href="buat_akun.php" class="navlink">Buat akun baru</a></p>';

mysqli_close($dbc);  // Close the database connection.

endScript:

include('includes/footer.html');  // Include the footer.
?>