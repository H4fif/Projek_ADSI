<?php  # Script lihat_pembelian.php
// This page display all purchase the comopany has been made.

$page_title = 'Pembelian';  // Set the page title.

include('includes/header.html');  // Include the header.

// Validate the user:
// If the user has not appropirate access, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || !in_array($_SESSION['user_level'], ['administrator', 'manager', 'gudang'])) {

    header('Location: index.php');  // Redirect the user to home page.

}  // End of user validation.

require('mysqli_connect.php');  // Need the database connection.

// Make the query:
$q = 'SELECT kode_faktur, tanggal_beli, nama_lengkap FROM tb_pembelian INNER JOIN tb_pegawai USING (kode_pegawai)';

$r = @mysqli_query($dbc, $q);  // Execute the query.

// Validate the query result:
if ($r) {  // If the query succeed, validate the returned row.

    // Validate the returned row:
    if (mysqli_num_rows($r) == 0) {  // If there is no record being returned, display a message.

        echo '<p>Tidak ada data.</p>';  // Display a message.

    } else {  // If there is any number of record returned, show them.

        // Create the table and begin with table headers:
        echo '<table border="0" cellpadding="5" cellspacing="5">
          <tr>
            <th>No.</th>
            <th>Kode Faktur</th>
            <th>Tanggal Beli</th>
            <th>Nama Pegawai</th>
          </tr>';

        $no = 1;  // For numbering the row.

        // Display all the records:
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetch each record to $row variable.

            // Display each record in one row.
            echo '<tr>
                <td>' . $no . '</td>
                <td>' . $row['kode_faktur'] . '</td>
                <td>' . $row['tanggal_beli'] . '</td>
                <td>' . $row['nama_lengkap'] . '</td>
              </tr>';

            $no++;  // Increment the number.

        }  // End of WHILE loop.

        echo '</table>';  // Close the table.

    }  // End of returned row validation.

} else {  // If the query failed, show an error message.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Display an error message.

}  // End of query result validation.

mysqli_free_result($r);  // Free up the resources.

mysqli_close($dbc);  // Close the database connection.

include('includes/footer.html');  // Include the footer.
?>