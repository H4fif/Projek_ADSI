<?php  # Script lihat_pegawai.php
// This script display data pegawai from database.

// Set the page title and include the header:
$page_title = 'Data Pegawai';
include('includes/header.html');

// Validate the user.
// Only user with access as administratorISTRATOR OR MANAGER can access this page.
// If the user does not have the right access to this page, redirect the user:
if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || (!in_array($_SESSION['user_level'], ['administrator', 'manager']))) {

    header('Location: index.php');  // Rediret the user to homepage.

}  // End of user validation.

$cari_error = FALSE;

// SEARCHING validation:
// If both values have a valid value, secure them.
if (empty($_GET['col']) || empty($_GET['keyword']) || !in_array(strtolower($_GET['col']), ['1', '2', '3', '4'])) {
    
    // unset($_GET['col']);  // Destroy it.

    // unset($_GET['keyword']);  // Destroy it.

    $column = $keyword = FALSE;  // Set flag variables to FALSE.

} else {  // If both values are not set or invalid, represent them as flag variable.

    $column = trim(htmlentities(strip_tags($_GET['col'])));  // Secure the $_GET['col'] value.

    $keyword = trim(htmlentities(strip_tags($_GET['keyword'])));  // Secure the $_GET['keyword'] value.

}  // End of search validation.

// SORTING validation:
// If both variable ($c & $o) have valid values, then get them:
if (isset($_GET['c'], $_GET['o']) && in_array($_GET['c'], [1, 2, 3, 4, 5]) && in_array($_GET['o'], [1, 2])) {
    $c = $_GET['c'];
    $o = $_GET['o'];
    $sort_status = true;
} else {
    $c = $o = 1;
    $sort_status = false;
}  // End of IF (SORTING validation).

// Validate the $c:
switch ($c) {
    case 2 : $c = 'jenis_kelamin';
             break;
    case 3 : $c = 'no_telepon';
             break;
    case 4 : $c = 'alamat';
             break;
    case 5 : $c = 'email';
             break;
    case 1 : $c = 'nama_lengkap';
             break;

}  // End of SWITCH ($c).

// Validate the $o:
if ($o == 2) {
    $ob = 'DESC';
} else {
    $ob = 'ASC';

}  // End of IF ($o).

require('mysqli_connect.php');  // Need the database connection.

if ($column && $keyword) {
    switch ($column) {
        case '1' : $colkey = 'nama_lengkap';
                   $hpc = 'Nama';
                   break;
        case '2' : $colkey = 'no_telepon';
                   $hpc = 'No. Telepon';
                   break;
        case '3' : $colkey = 'alamat';
                   $hpc = 'Alamat';
                   break;
        case '4' : $colkey = 'email';
                   $hpc = 'Email';
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

}  // End od IF ($column).

$q = "SELECT * FROM tb_pegawai $fq ORDER BY $c $ob";  // Make the query.

$r = @mysqli_query($dbc, $q);  // Execute the query.
$total_row = mysqli_num_rows($r);

// Validate the query result:
if ($r) {  // If query succeed, check the returned row.
  
    echo '<form name="pencarian" action="lihat_pegawai.php" method="get">
      <select name="col">
        <option>-- Cari berdasarkan --</option>
        <option value="1"' . ((isset($_GET['col']) && ($_GET['col'] == '1')) ? ' selected="selected"' : '') . '>Nama</option>
        <option value="2"' . ((isset($_GET['col']) && ($_GET['col'] == '2')) ? ' selected="selected"' : '') . '>No. Telepon</option>
        <option value="3"' . ((isset($_GET['col']) && ($_GET['col'] == '3')) ? ' selected="selected"' : '') . '>Alamat</option>
        <option value="4"' . ((isset($_GET['col']) && ($_GET['col'] == '4')) ? ' selected="selected"' : '') . '>Email</option>
      </select>
      <input name="keyword" placeholder="Masukkan kata kunci" type="text" minlength="1" maxlength="255" value="' . ((isset($_GET['keyword']) ? $_GET['keyword'] : '')) . '" />
      <input name="submit" type="submit" value="Cari" />
    </form>';

    if ($search) {
        echo '<h2>Hasil pencarian:</h2>
          <h3>' . $hpc . ': ' . $keyword . '</h3>';
    }

    // Validate the reurned row:
    if ($total_row == 0) {  // If there is no record being returned, display a message, exit the script.

        if ($search) {
            echo '<p>Data tidak ditemukan.</p>';
        } else {
            echo '<p>Tidak ada data.</p>';  // Display a message.
        
        }  // End of IF ($search).

        // echo '<p><a class="navlink" href="input_pegawai.php">Tambah pegawai baru</a></p>';

        goto endDScript;

    }  // End of returned row validation.
    // If there is at least one record being returned, continue the script.

} else {  // If query failed, display an error message, exit the script.

    echo '<h1>Terjadi kesalahan!</h1><p>Kesalahan:<br />' . mysqli_error($dbc) . '</p>';  // Display an error message.

    goto endDScript;

}  // End of query result validation.

// Create the table with some table headers:
echo '<table border="0" cellspacing="5" cellpadding="5">
  <tr>
    <th>No</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>No. Telepon</th>
    <th>Alamat</th>
    <th>Email</th>
  </tr>';

$no = 1;  // For numbering the row.

// Display all records:
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {  // Fetching each record to $row variable.

    // Display the data in one row:
    echo '<tr>
        <td>' . $no . '</td>
        <td>' . $row['nama_lengkap'] . '</td>
        <td>' . (($row['jenis_kelamin'] == 'L') ? 'Laki-Laki' : 'Perempuan') . '</td>
        <td>' . $row['no_telepon'] . '</td>
        <td>' . $row['alamat'] . '</td>
        <td>' . $row['email'] . '</td>
      </tr>';

    $no++;  // Increment the number.

}  // End of WHILE loop.

echo '</table>';

endDScript:

if ($total_row > 0) {

    // Display sorting form:
    echo '<br /><br />
      <form name="sorting" action="lihat_pegawai.php" method="get">
        <select name="c">
          <option>-- Urut Berdasarkan --</option>
          <option value="1"' . ((isset($_GET['c']) && ($_GET['c'] == 1)) ? ' selected="selected"' : '') . '>Nama</option>
          <option value="2"' . ((isset($_GET['c']) && ($_GET['c'] == 2)) ? ' selected="selected"' : '') . '>Jenis Kelamin</option>
          <option value="3"' . ((isset($_GET['c']) && ($_GET['c'] == 3)) ? ' selected="selected"' : '') . '>No. Telepon</option>
          <option value="4"' . ((isset($_GET['c']) && ($_GET['c'] == 4)) ? ' selected="selected"' : '') . '>Alamat</option>
          <option value="5"' . ((isset($_GET['c']) && ($_GET['c'] == 5)) ? ' selected="selected"' : '') . '>Email</option>
        </select>
        <select name="o">
          <option>-- Urutkan dari --</option>
          <option value="1"' . ((isset($_GET['o']) && ($_GET['o'] == 1)) ? ' selected="selected"' : '') . '>A-Z</option>
          <option value="2"' . ((isset($_GET['o']) && ($_GET['o'] == 2)) ? ' selected="selected"' : '') . '>Z-A</option>
        </select>
        <input name="submit" type="submit" value="Refresh" />
      </form>';  // End of the form.

}  // End of IF ($total_row).

// Validate the search or sorting status:
if ($search || $sort_status) {
  echo '<p><a class="navlink" href="lihat_pegawai.php">Kembali Ke Awal</a></p>';
}  // End of IF ($search || $sort_status).

echo '<p><a class="navlink" href="input_pegawai.php">Tambah pegawai baru</a></p>';

mysqli_free_result($r);  // Free up the resources.
mysqli_close($dbc);  // Close the database connection.

endScript:

include('includes/footer.html');  // Include the footer.
?>