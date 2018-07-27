<?php  # Script lihat_pelanggan.php
// This script display data pelanggan from database.

// Set the page title and include the header:
$page_title = 'Data Pelanggan';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR OR MANAGER can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || (!in_array($_SESSION['user_level'], ['administrator', 'manager', 'gudang']))) {
    ob_end_clean();
    header('Location: index.php');  // Redirect the user to homepage.
    exit;
}  // End of user validation.

// Validate the search:
if (isset($_GET['colCari'], $_GET['colKeyword']) && in_array($_GET['colCari'], [1])) {

}

require('mysqli_connect.php');  // Need the database connection.

$q = 'SELECT * FROM tb_pelanggan';  // Make the query.
$r = @mysqli_query($dbc, $q);  // Execute the query.

// Validate the query result:
if ($r) {  // If query succeed, check the returned row.
    
    // Validate the returned row:
    if (mysqli_num_rows($r) == 0) {  // If no record is returned, display a message, exit the script.
        echo '<p>Tidak ada data.</p>';  // Display a message.
        goto endDScript;
    }  // End of returned row validation.
    // If there is at least one record being returned, continue the script.

} else {  // If query failed, display an error message, exit the script.
    echo '<h1>Terjadi kesalahan!</h1><p>Kesalaan:<br />' . mysqli_error($dbc) . '</p>';  // Display an error message.
    goto endDScript;
}  // End of query result validation.

// Create the table with some table headers:
echo '<table border="0" cellspacing="5" cellpadding="5">
  <tr>
    <th>No.</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>No. Telepon</th>
    <th>Alamat</th>
    <th>Ubah</th>
    <th>Hapus</th>
  </tr>';

$no = 1;  // For numbering the row:

// Display all the records:
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetching each record to $row variable.

    // Display each data in one row:
    echo '<tr>
        <td>' . $no . '</td>
        <td>' . $row['nama_pelanggan'] . '</td>
        <td>' . (($row['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan') . '</td>
        <td>' . $row['no_telepon'] . '</td>
        <td>' . $row['alamat'] . '</td>
        <td><a class="navlink" href="#">Ubah</a></td>
        <td><a class="navlink" href="#">Hapus</a></td>
      </tr>';

    $no++;  // Increment the number.

}  // End of WHILE loop.

echo '</table>';  // Close the table.

endDScript:
mysqli_free_result($r);  // Free up the resources.

endScript:

mysqli_close($dbc);  // Close the database connection.
include('includes/footer.html');  // Include the footer.
?>