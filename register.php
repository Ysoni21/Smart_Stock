<?php
// Database configuration
define('DB_HOST', 'localhost'); // Hostname
define('DB_USER', 'root');      // Database username
define('DB_PASS', '');          // Database password (default is empty for XAMPP)
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

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        // Email already exists
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    } else {
        // Insert the data into the database
        $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login page
            header('Location: /site/login.html');
            exit(); // Always call exit() after a header redirect
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }
    }
}

// Close the connection
$conn->close();
?>
