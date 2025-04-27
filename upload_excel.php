<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'site');

// Create a connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the JSON input data from JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['data']) && is_array($data['data'])) {
    $stmt = $conn->prepare("
        INSERT INTO dashboard (date, asin, product_title, brand, store_code, ordered_revenue, ordered_units, shipped_revenue, shipped_units, customer_returns) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        date = VALUES(date),
        product_title = VALUES(product_title),
        brand = VALUES(brand),
        store_code = VALUES(store_code),
        ordered_revenue = VALUES(ordered_revenue),
        ordered_units = VALUES(ordered_units),
        shipped_revenue = VALUES(shipped_revenue),
        shipped_units = VALUES(shipped_units),
        customer_returns = VALUES(customer_returns)
    ");

    if (!$stmt) {
        die(json_encode(["success" => false, "message" => "SQL prepare failed: " . $conn->error]));
    }

    foreach ($data['data'] as $row) {
        $date = $row['date'];
        $asin = $row['asin'];
        $productTitle = $row['productTitle'];
        $brand = $row['brand'];
        $storeCode = $row['storeCode'];
        $orderedRevenue = $row['orderedRevenue'];
        $orderedUnits = $row['orderedUnits'];
        $shippedRevenue = $row['shippedRevenue'];
        $shippedUnits = $row['shippedUnits'];
        $customerReturns = $row['customerReturns'];

        $stmt->bind_param(
            "ssssssiiii",
            $date,
            $asin,
            $productTitle,
            $brand,
            $storeCode,
            $orderedRevenue,
            $orderedUnits,
            $shippedRevenue,
            $shippedUnits,
            $customerReturns
        );

        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
            exit;
        }
    }

    echo json_encode(["success" => true, "message" => "Data uploaded successfully."]);
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid data received."]);
}

$conn->close();
?>
