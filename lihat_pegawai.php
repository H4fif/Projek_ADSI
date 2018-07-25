<?php  # Script lihat_pegawai.php
// This script display data pegawai from database.

/* FEATURES LIST */

// - Searching (done).
// - Sorting (done).
// - Searching & sorting combined (done).
// - Pagination (to do).
// - Edit.
// - Delete.

/* END FEATURE LIST */

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
$page_title = 'Data Pegawai';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR OR MANAGER can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || (!in_array($_SESSION['user_level'], ['administrator', 'manager']))) {

    header('Location: index.php');  // Rediret the user to homepage.

}  // End of user validation.

// Validate the display:
if (isset($_GET['display']) && check_five($_GET['display'])) {
    $display = $_GET['display'];
} else {
    if (isset($_SESSION['display'])) {
        $display = $_SESSION['display'];
    } else {
        $display = 5;
    }

}  // End of display validation.

$_SESSION['display'] = $display;

if (isset($_GET['start']) && check_start($_GET['start'], $display)) {
    $start = $_GET['start'];
} else {
    $start = 0;
}

$cari_error = FALSE;

// SEARCHING validation:
// If both values have a valid value, secure them.
if (empty($_GET['col']) || empty($_GET['keyword']) || !in_array(strtolower($_GET['col']), ['1', '2', '3', '4'])) {
    
    $col = $keyword = FALSE;  // Set flag variables to FALSE.

} else {  // If both values are not set or invalid, represent them as flag variable.

    $col = trim(htmlentities(strip_tags($_GET['col'])));  // Secure the $_GET['col'] value.

    $keyword = trim(htmlentities(strip_tags($_GET['keyword'])));  // Secure the $_GET['keyword'] value.

}  // End of search validation.

// SORTING validation:
// If both variable ($c & $o) have valid values, then get them:
if (isset($_GET['c'], $_GET['o']) && in_array($_GET['c'], [1, 2, 3, 4]) && in_array($_GET['o'], [1, 2])) {
    $c = $_GET['c'];
    $o = $_GET['o'];
    $sort_status = true;
} else {
    $c = $o = 1;
    $sort_status = false;
}  // End of IF (SORTING validation).

// Validate the $c:
switch ($c) {
    case 1 : $column = 'nama_lengkap';
             break;
    case 2 : $column = 'jenis_kelamin';
             break;
    case 3 : $column = 'no_telepon';
             break;
    case 4 : $column = 'alamat';
             break;

}  // End of SWITCH ($c).

// Validate the $o:
if ($o == 2) {
    $ob = 'DESC';
} else {
    $ob = 'ASC';

}  // End of IF ($o).

require('mysqli_connect.php');  // Need the database connection.

// Validate the search input:
if ($col && $keyword) {
    switch ($col) {
        case '1' : $colkey = 'nama_lengkap';
                   $hpc = 'Nama';
                   break;
        case '2' : $colkey = 'no_telepon';
                   $hpc = 'No. Telepon';
                   break;
        case '3' : $colkey = 'alamat';
                   $hpc = ucwords($colkey);
                   break;
    }  // End of SWITCH ($column).

    $keyword = mysqli_real_escape_string($dbc, $keyword);

    $fq = " WHERE $colkey LIKE '%$keyword%'";
    $search = TRUE;
    // echo "<script>alert('$fq is empty!')</script>";
} else {
    // echo "<script>alert('$fq is empty!')</script>";
    $fq = '';
    $search = FALSE;

}  // End od IF search validation.

$q = "SELECT COUNT(*) FROM tb_pegawai $fq";
$r = @mysqli_query($dbc, $q) or die('<h1>Terjadi kesalahan!</h1><p>Kesalahan: ' . mysqli_error($dbc) . '</p>');
list($total_row) = mysqli_fetch_array($r, MYSQLI_NUM);

if (!$search && ($total_row == 0)) {
    echo '<p>Tidak ada data.</p>';
    goto endDScript;
}

$q = "SELECT * FROM tb_pegawai $fq ORDER BY $column $ob LIMIT $start, $display";  // Make the query.
$r = @mysqli_query($dbc, $q);  // Execute the query.

// Validate the query result:
if ($r) {  // If query succeed, check the returned row.
  
    echo '<form name="pencarian" action="lihat_pegawai.php" method="get">
      <select name="col">
        <option value="">-- Filter berdasarkan --</option>
        <option value="1"' . ((isset($_GET['col']) && ($_GET['col'] == '1')) ? ' selected="selected"' : '') . '>Nama</option>
        <option value="2"' . ((isset($_GET['col']) && ($_GET['col'] == '2')) ? ' selected="selected"' : '') . '>No. Telepon</option>
        <option value="3"' . ((isset($_GET['col']) && ($_GET['col'] == '3')) ? ' selected="selected"' : '') . '>Alamat</option>
      </select>
      <input name="keyword" placeholder="Masukkan kata kunci" type="text" minlength="1" maxlength="255" value="' . ((isset($_GET['keyword']) ? $_GET['keyword'] : '')) . '" />
      <input name="submit" type="submit" value="Filter" />
    </form><br />';

    if ($search) {
        echo '<h2>Hasil filter:</h2>
          <h3>' . $hpc . ': ' . stripslashes($keyword) . '</h3>';
    }

    // Validate the reurned row:
    if ($total_row == 0) {  // If there is no record being returned, display a message, exit the script.

        if ($search) {
            echo '<p>Data tidak ditemukan.</p>';
        } else {
            echo '<p>Tidak ada data.</p>';  // Display a message.
        
        }  // End of IF ($search).

    } else {
        // Create the table with some table headers:
        echo '<table border="0" cellspacing="5" cellpadding="5">
          <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>No. Telepon</th>
            <th>Alamat</th>
            <th>Edit</th>
            <th>Hapus</th>
          </tr>';

        $no = $start + 1;  // For numbering the row.

        // Display all records:
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetching each record to $row variable.

            // NEED TO BE UPDATED!
            // Display the data in one row:
            echo '<tr>
                <td>' . $no . '</td>
                <td>' . $row['nama_lengkap'] . '</td>
                <td>' . (($row['jenis_kelamin'] == 'L') ? 'Laki-Laki' : 'Perempuan') . '</td>
                <td>' . $row['no_telepon'] . '</td>
                <td>' . $row['alamat'] . '</td>
                <td><a class="navlink" href="edit_pegawai.php?id=' . $row['kode_pegawai'] . '">Edit</a></td>
                <td><a class="navlink" href="hapus_pegawai.php?id=' . $row['kode_pegawai'] . '">Hapus</a></td>
              </tr>';

            $no++;  // Increment the number.

        }  // End of WHILE loop.

        echo '</table>';
        
        /* PAGINATION SECTION */

        if (isset($_GET['c'])) {
            $c = $_GET['c'];
        } else {
            $c = NULL;
        }

        if (isset($_GET['o'])) {
            $o = $_GET['o'];
        } else {
            $o = NULL;
        }

        if ($total_row > $display) {
          $page = ceil($total_row / $display);
        } else {
          $page = 1;
        }

        if ($page > 1) {
            echo '<p>Halaman ';
            $current_page = ($start / $display) + 1;

            // Prev button:
            if ($current_page != 1) {
                echo '<a class="navlink" href="lihat_pegawai.php?col=' . $col . '&keyword=' . $keyword . '&start=' . ($start - $display) . '&display=' . $display . '&c=' . $c . '&o=' . $o . '">Sebelumnya</a> ';
            }

            // Numbered link page:
            for ($i = 1; $i <= $page; $i++) {
            
                if ($i == $current_page) {
                    echo $i . ' ';
                } else {
                    echo '<a class="navlink" href="lihat_pegawai.php?col=' . $col . '&keyword=' . $keyword . '&start=' . ($display * ($i - 1)) . '&display=' . $display . '&c=' . $c . '&o=' . $o . '">' . $i . ' </a>';
                }  // End of IF.

            }  // End of FOR loop.

            // Next button:
            if ($current_page != $page) {
                echo ' <a class="navlink" href="lihat_pegawai.php?col=' . $col . '&keyword=' . $keyword . '&start=' . ($start + $display) . '&display=' . $display . '&c=' . $c . '&o=' . $o . '">Selanjutnya</a>';
            }

            echo '</p>';

        }

        /* END SECTION */

        echo '<p>Total: ' . $total_row . ' data.</p>';

        // Display sorting form:
        echo '<br /><br />
          <form name="sorting" action="lihat_pegawai.php" method="get">
            <label>Tampilkan per halaman: </label>
            <select name="display">
              <option value="5"' . (($display == 5) ? ' selected="selected"' : '') . '>5</option>
              <option value="10"' . (($display == 10) ? ' selected="selected"' : '') . '>10</option>
              <option value="15"' . (($display == 15) ? ' selected="selected"' : '') . '>15</option>
            </select>
            <br />
            <br />
            <select name="c">
              <option value="">-- Urut Berdasarkan --</option>
              <option value="1"' . ((isset($_GET['c']) && ($_GET['c'] == 1)) ? ' selected="selected"' : '') . '>Nama</option>
              <option value="2"' . ((isset($_GET['c']) && ($_GET['c'] == 2)) ? ' selected="selected"' : '') . '>Jenis Kelamin</option>
              <option value="3"' . ((isset($_GET['c']) && ($_GET['c'] == 3)) ? ' selected="selected"' : '') . '>No. Telepon</option>
              <option value="4"' . ((isset($_GET['c']) && ($_GET['c'] == 4)) ? ' selected="selected"' : '') . '>Alamat</option>
            </select>
            <select name="o">
              <option value="">-- Urutkan dari --</option>
              <option value="1"' . ((isset($_GET['o']) && ($_GET['o'] == 1)) ? ' selected="selected"' : '') . '>A-Z</option>
              <option value="2"' . ((isset($_GET['o']) && ($_GET['o'] == 2)) ? ' selected="selected"' : '') . '>Z-A</option>
            </select>';

        if ($col && $keyword) {
            echo '<input name="col" type="hidden" value="' . $col . '" /><input name="keyword" type="hidden" value="' . $keyword . '" />';
        }

        echo '<input name="submit" type="submit" value="Refresh" />
          </form>';  // End of the form.

    }  // End of IF ($total_row).

    if ($search) {
        echo '<p><a class="navlink" href="lihat_pegawai.php">Kembali</a></p>';
    }

} else {  // If query failed, display an error message, exit the script.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Display an error message.

}  // End of query result validation.

mysqli_free_result($r);  // Free up the resources.

endDScript:

echo '<p><a class="navlink" href="input_pegawai.php">Tambah pegawai baru</a></p>';
mysqli_close($dbc);  // Close the database connection.

endScript:

include('includes/footer.html');  // Include the footer.
?>