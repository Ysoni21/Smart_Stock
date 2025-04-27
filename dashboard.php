<?php
$servername = "localhost";
$username = "root"; // Change if necessary
$password = "";
$dbname = "site";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🔹 If the request is a POST (Form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $asin = $_POST['asin'];
    $productTitle = $_POST['product-title'];
    $brand = $_POST['brand'];
    $storeCode = $_POST['store_code'];
    $orderedRevenue = $_POST['ordered_revenue'];
    $orderedUnits = $_POST['ordered_units'];
    $shippedRevenue = $_POST['shipped_revenue'];
    $shippedUnits = $_POST['shipped_units'];
    $customerReturns = $_POST['customer_returns'];

    // Insert data into the database
    $sql = "INSERT INTO dashboard (date, asin, product_title, brand, store_code, ordered_revenue, ordered_units, shipped_revenue, shipped_units, customer_returns) 
            VALUES ('$date', '$asin', '$productTitle', '$brand', '$storeCode', '$orderedRevenue', '$orderedUnits', '$shippedRevenue', '$shippedUnits', '$customerReturns')";

    if ($conn->query($sql) === TRUE) {
        header('Location: /site/dashboard.html'); // Redirect to the dashboard or home page
            exit();// ✅ Send success message to JavaScript
    } else {
        echo "error: " . $conn->error;
    }

    $conn->close();
    exit();
}

// 🔹 If the request is a GET (Fetch latest data)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM dashboard ORDER BY date DESC"; // Fetch latest data
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['asin']}</td>
                    <td>{$row['product_title']}</td>
                    <td>{$row['brand']}</td>
                    <td>{$row['store_code']}</td>
                    <td>₹{$row['ordered_revenue']}</td>
                    <td>{$row['ordered_units']}</td>
                    <td>₹{$row['shipped_revenue']}</td>
                    <td>{$row['shipped_units']}</td>
                    <td>{$row['customer_returns']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='10'>No data found</td></tr>";
    }

    $conn->close();
    exit();
}
?>
