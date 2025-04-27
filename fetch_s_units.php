<?php
// Database connection
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "site";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Establish database connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ASIN from request
$asin = isset($_GET['asin']) ? $_GET['asin'] : '';

if ($asin) {
    // Fetch shipped units data for the selected ASIN
    $query = "SELECT DATE_FORMAT(date, '%Y-%m-%d') AS date, shipped_units FROM dashboard WHERE asin = ? ORDER BY date ASC, id ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $asin);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    $totalShippedUnits = 0;
    $count = 0;

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $totalShippedUnits += (int) $row['shipped_units'];
        $count++;
    }
    
    $stmt->close();

    // Calculate and update average sales
    if ($count > 0) {
        $averageSales = round($totalShippedUnits / $count, 2);

        // Update average_sales in the Dashboard table for the selected ASIN
        $updateQuery = "UPDATE dashboard SET average_sales = ? WHERE asin = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ds", $averageSales, $asin);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $conn->close();

    // Return the fetched data to the frontend
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No ASIN selected"]);
}
?>
