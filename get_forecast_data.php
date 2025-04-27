<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "site";

$conn = new mysqli($servername, $username, $password, $database);

header("Content-Type: application/json");

// Validate request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

$asin = $_POST["asin"] ?? null;

if (!$asin) {
    echo json_encode(["success" => false, "error" => "Missing ASIN"]);
    exit;
}

// Fetch forecast data for the given ASIN
$query = "SELECT product_name, average_sales, non_event_sale, event_sale, total_inventory_required, inventory_left_next_month, sales_average 
          FROM forecast_data 
          WHERE asin = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $asin);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "error" => "No forecast data found"]);
}

$stmt->close();
$conn->close();
?>
