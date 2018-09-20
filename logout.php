<?php  # Script logout.php

session_start();  // Start the session.

// Validate the user:
// If the user has not logged in and try to access this page directly, redirect the user.
if (!isset($_SESSION['agent'], $_SESSION['user_id']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']))) {

    // ob_end_clean();
    header('Location: index.php');  // Redirect the user to homepage.
    exit;

} else {  // If the right user access this page, destroy the session and cookie.

    $_SESSION = [];  // Set the $_SESSION to an empty array.
    session_destroy();  // Destroy the session.
    setcookie(session_name(), '', time()-3600);  // Destroy the cookie.

}  // End of user validation.

$page_title = 'Logout';  // Set the page title.

include('includes/header.html');  // Include the header.

echo "<p>Anda telah logout.</p>";  // Display a message.

include('includes/footer.html');  // Include the footer.
?>