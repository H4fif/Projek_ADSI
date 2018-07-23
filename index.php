<?php  # Script index.php
// This is the home page.

// Set the page title and include the header.
$page_title = 'Home';
include('includes/header.html');

// Validate the user:
if (isset($_SESSION['user_id'])) {  // If the user has logged in, show a welcome message.

    // Some random text as a welcome message:
    echo '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nobis deleniti commodi totam, fugiat corrupti laudantium perferendis earum, voluptatibus cumque sed nemo id a, aliquam facere quibusdam voluptatum deserunt, nam autem.</p>';

} else {  // If the user has not logged in yet, show a message to login.

    // Show a message to login:
    echo '<p>Silakan <a class="navlink" href="login.php">Login</a> untuk menggunakan website ini.</p>'; 

}  // End of user validation.

include('includes/footer.html');  // Include the footer.
?>