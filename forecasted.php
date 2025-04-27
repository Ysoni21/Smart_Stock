<?php
// Database connection
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "site";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json"); // Ensure JSON response

// Debugging: Log received data
file_put_contents("debug_log.txt", "Received POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// 🔴 Missing connection: FIXED
$conn = new mysqli($host, $username, $password, $database);

// 🔍 Check if database connection is successful
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Get and validate input data
$asin = $_POST["asin"] ?? null;
$product_name = $_POST["product_name"] ?? null;
$average_sales = (int) ($_POST["average_sales"] ?? 0);
$non_event_sale = (int) ($_POST["non_event_sale"] ?? 0);
$event_sale = (int) ($_POST["event_sale"] ?? 0);
$total_inventory_required = (int) ($_POST["total_inventory_required"] ?? 0);
$inventory_left_next_month = (int) ($_POST["inventory_left_next_month"] ?? 0);
$sales_average = (int) ($_POST["sales_average"] ?? 0);

// Validate required fields
if (!$asin || !$product_name) {
    echo json_encode(["success" => false, "error" => "Missing ASIN or Product Name"]);
    exit;
}

// Fetch dashboard_id using ASIN
$query = "SELECT id FROM dashboard WHERE asin = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL Error (SELECT): " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $asin);
$stmt->execute();
$stmt->bind_result($dashboard_id);

if (!$stmt->fetch()) {
    file_put_contents("debug_log.txt", "ASIN not found in dashboard: $asin\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "ASIN not found in dashboard"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Debug: Log retrieved dashboard_id
file_put_contents("debug_log.txt", "Fetched dashboard_id: $dashboard_id\n", FILE_APPEND);

// Insert data into forecast_data table
$insert_query = "INSERT INTO forecast_data (dashboard_id, asin, product_name, average_sales, non_event_sale, event_sale, total_inventory_required, inventory_left_next_month, sales_average) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert_query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL Error (INSERT): " . $conn->error]);
    exit;
}

$stmt->bind_param("issiiiiii", $dashboard_id, $asin, $product_name, $average_sales, $non_event_sale, $event_sale, $total_inventory_required, $inventory_left_next_month, $sales_average);

if ($stmt->execute()) {
    file_put_contents("debug_log.txt", "Data inserted successfully for ASIN: $asin\n", FILE_APPEND);
    echo json_encode(["success" => true]);
} else {
    file_put_contents("debug_log.txt", "Insert Error: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
