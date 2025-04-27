<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost"; 
$username = "root"; // Change if needed
$password = ""; // Change if needed
$database = "site"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Fetch ASINs
$sql = "SELECT DISTINCT asin FROM dashboard";
$result = $conn->query($sql);

$asins = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $asins[] = $row['asin'];
    }
}

$conn->close();

// Return JSON
echo json_encode($asins);
?>
