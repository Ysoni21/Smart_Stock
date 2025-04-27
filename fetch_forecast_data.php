<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "site";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

header("Content-Type: application/json");

$query = "SELECT asin, product_name, average_sales, non_event_sale, event_sale, total_inventory_required, inventory_left_next_month, sales_average FROM forecast_data";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
