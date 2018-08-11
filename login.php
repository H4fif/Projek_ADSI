<?php  # Script login.php
// This is login page.

// Validate the user.
// Check if the user has logged in, and try to access this page directly:
if (isset($_SESSION['user_id'])) {
    
    ob_end_clean();
    header('Location: index.php');  // Redirect the user to homepage.
    exit();  // Exit the script.

}  // End of user validation.

// Set the page title and include the header:
$page_title = 'Login';
include('includes/header.html');

// Form submission validation:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];  // Initialize the variable to store error messages.

    // Secure the HTML input objects:

    $val = array_map('strip_tags', $_POST);  // Remove all PHP and HTML tags.

    $val = array_map('htmlentities', $val);  // Convert all applicable HTML characters to HTML entities.

    $val = array_map('trim', $val);  // Remove all white spaces.

    // End of HTML input object security.

    // Validate the email:
    if (empty($val['email']) || !filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {  // If email is not match with the format, set an error message.
        
        $errors[] = 'Email tidak valid!';  // Set an error message.

    }  // End of email validation.

    // Validate the password:
    if (empty($val['password']) && !preg_match('/^(\w){4,20}$/', $val['password'])) {  // If password did not match the format, set an error message.

        $errors[] = 'Kata sandi tidak valid!';  // Set an error message.

    }  // End of password validation.

    // Validate the $errors array variable:
    if (empty($errors)) {  // If it is empty, do some validation more:

        require('mysqli_connect.php');  // Need the database connection.

        $e = mysqli_real_escape_string($dbc, $val['email']);  // Escape the email.

        $p = mysqli_real_escape_string($dbc, $val['password']);  // Escape the password.

        $q = "SELECT * FROM tb_akun WHERE email = '$e' AND kata_sandi = SHA1('$p')";  // Make the query.

        $r = @mysqli_query($dbc, $q);  // Execute the query.
        
        // Validate the query result:
        if (!$r) {  // If query failed, show an error message.

            echo '<h1>Terjadi kesalahan!</h1><p>Query:<br />' . mysqli_error($dbc) . '</p>';  // Display an error message.

        } else {  // If query succeed, validate the returned row.

            // Validate the retuned row:
            if (mysqli_num_rows($r) == 1) {  // If there is a match, set the SESSION, exit the script.

                $row = mysqli_fetch_array($r, MYSQLI_ASSOC);  // Fetching the record to $row variable.

                $_SESSION['user_id'] = $row['kode_akun'];  // Set the SESSION of user_id with kode_akun.

                $_SESSION['user_level'] = $row['akses'];  // Set the SESSION of user_level with akses.

                $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);  // Set the SESSION of agent with the hashed of browser name/

                echo '<p>Login succeed!</p>';  // Display a message.
                header('Location: http://localhost/projek_adsi/');  // Redirect the user to homepage.
                ob_end_clean();
                exit();  // Exit the script.

            } else {  // If there is no match, display a message:

                echo "<h1>Login Gagal!</h1><p>Akun Anda tidak terdaftar!<br /></p>";  // Display a message.
            
            }  // End of returned row validation.

        }  // End of query result validation.

    } else {  // If it is not empty, display all the error messages:

        echo '<h2>Login Gagal!</h2><p>Kesalahan:<br />';  // Display a header.

        // Display all the error messages:
        foreach ($errors as $e) {

            echo '- ' . $e . '<br />';  // Display an error message with a break line.

        }  // End of FOREACH loop.

        echo '</p>';  // Close the paragraph.

    }  // End of $errors validation.

}  // End of form submission validation.

// Display the form:
echo '<form name="login" action="login.php" method="post">
    <p>Email: <input name="email" type="text" minlength="5" maxlength="40" required="required"' . ((isset($val['email']) ? ' value="' . $val['email'] . '"' : '')) . ' /></p>
    <p>Kata Sandi: <input name="password" type="password" minlength="4" maxlength="20" required="required"' . ((isset($val['password']) ? ' value="' . $val['password'] . '"' : '')) . ' /></p>
    <p><input type="submit" value="Login" /></p>
  </form>';

include('includes/footer.html');  // Include the footer.
?>