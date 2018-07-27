<?php  # Script lihat_barang.php
// This page displays data barang from database.

// Set the page title and include the header:
$page_title = 'Data Barang';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR OR MANAGER can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || !in_array($_SESSION['user_level'], ['administrator', 'manager'])) {
    ob_end_clean();
    header('Location: index.php');  // Redirect the user to homepage.
    exit;
}  // End of user validation.

require('mysqli_connect.php');  // Need the database connection:

// Make the query:
$q = 'SELECT * FROM tb_barang INNER JOIN tb_supplier ON tb_barang.kode_supplier = tb_supplier.kode_supplier';

$r = @mysqli_query($dbc, $q);  // Execute the query.

// Validate the query result:
if ($r) {  // If query succeed, check the returned row.
    
    // Validate the returned row:
    if (mysqli_num_rows($r) == 0) {  // If no row is returned, diplay a message, exit the script.

        echo '<p>Tidak ada data.</p>';  // Display a message.

        mysqli_free_result($r);  // Free up the resources.

        mysqli_close($dbc);  // Close the connection.

        include('includes/footer.html');  // Include the footer.

        exit;  // Exit the script

    }  // End of if (mysqli_num_rows).
    // If there is at least one record is returned, continue the script.

} else {  // If query failed, display a message, exit the script.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Display a message.

    mysqli_free_result($r);  // Free up the resources.

    mysqli_close($dbc);  // Close the connection.

    include('includes/footer.html');  // Include the footer.

    exit;  // Exit the script.

}  // End of query result validation.

// If there is any record being returned, then show them.
// Display the table with some table headers:
echo '<table border="0" cellpadding="5" cellspacing="5">
  <tr>
    <th>No</th>
    <th>Kode Barang</th>
    <th>Nama</th>
    <th>Harga</th>
    <th>Kategori</th>
    <th>Stok</th>
    <th>Deskripsi</th>
    <th>Supplier</th>
  </tr>';

$no = 1;  // For numbering the row.

while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetching row from database to $row variable.
    echo '<tr>
        <td>' . $no . '</td>
        <td>' . $row['kode_barang'] . '</td>
        <td>' . $row['nama_barang'] . '</td>
        <td>' . number_format($row['harga'], 2, ',', '.') . '</td>
        <td>' . $row['kategori'] . '</td>
        <td>' . $row['jumlah_stok'] . '</td>
        <td>' . (empty($row['deskripsi']) ? '-' : $row['deskripsi']) . '</td>
        <td>' .  $row['nama_supplier'] . '</td>
      </tr>';
    
    $no++;  // Increment the number.

}  // End of while ($row) -> fetching from database.

echo '</table>';  // Close the table.

mysqli_free_result($r);  // Free up the resources.

mysqli_close($dbc);  // Close the connection.

include('includes/footer.html');  // Include the footer.
?>