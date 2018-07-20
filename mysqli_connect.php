<?php  # Script 3 - mysqli_connect.php
// This script set up database connection.

// Set database access information:
define('DB_USER', 'root');
define('DB_HOST', 'localhost');
define('DB_PASS', '');
define('DB_NAME', 'projek_adsi');

// Connect to the database:
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$dbc) {  // If connection failed show error message, exit the script.
    echo '<h1>System Error</h1>
      <p>An error occurred when connecting to the database.<br />' . mysqli_connect_error($dbc) . '</p>';
    exit();
}

mysqli_set_charset($dbc, 'utf8');  // Set database charset.