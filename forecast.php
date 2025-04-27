<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$database = "site";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (isset($_GET["asin"])) {
    $asin = $conn->real_escape_string($_GET["asin"]);
    $sql = "SELECT asin, product_title, average_sales FROM dashboard WHERE asin = '$asin' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No data found"]);
    }
} else {
    echo json_encode(["error" => "No ASIN provided"]);
}

$conn->close();
?>
