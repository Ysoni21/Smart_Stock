<?php
// Database configuration
define('DB_HOST', 'localhost'); // Hostname
define('DB_USER', 'root');      // Database username
define('DB_PASS', '');          // Database password
define('DB_NAME', 'site');   // Database name

// Create a connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Email exists, now validate the password
        $user = $result->fetch_assoc();

        if ($user['password'] === $password) {
            // Correct password
            header('Location: /site/dashboard.html'); // Redirect to the dashboard or home page
            exit();
        } else {
            // Incorrect password
            header('Location: login.html?error=incorrect_password');
            exit();
        }
    } else {
        // Email not registered
        header('Location: login.html?error=email_not_registered');
        exit();
    }
}

// Close the connection
$conn->close();
?>
