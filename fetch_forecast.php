<?php
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "site";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$asin = $_GET["asin"] ?? null;
if (!$asin) {
    echo json_encode(["error" => "ASIN not provided"]);
    exit;
}

// Fetch product details from dashboard
$query = "SELECT product_name, average_sales FROM dashboard WHERE asin = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $asin);
$stmt->execute();
$stmt->bind_result($product_name, $average_sales);
$stmt->fetch();
$stmt->close();

if (!$product_name) {
    echo json_encode(["error" => "ASIN not found in dashboard"]);
    exit;
}

// Fetch forecast data if exists
$query = "SELECT non_event_sale, event_sale, total_inventory_required, inventory_left_next_month, sales_average 
          FROM forecast_data WHERE asin = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $asin);
$stmt->execute();
$stmt->bind_result($non_event_sale, $event_sale, $total_inventory_required, $inventory_left_next_month, $sales_average);
$forecast_exists = $stmt->fetch();
$stmt->close();
$conn->close();

// Response with available data
$response = [
    "product_name" => $product_name,
    "average_sales" => $average_sales,
    "forecast_exists" => $forecast_exists
];

if ($forecast_exists) {
    $response["non_event_sale"] = $non_event_sale;
    $response["event_sale"] = $event_sale;
    $response["total_inventory_required"] = $total_inventory_required;
    $response["inventory_left_next_month"] = $inventory_left_next_month;
    $response["sales_average"] = $sales_average;
}

echo json_encode($response);
?>
