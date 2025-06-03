<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change if your MySQL username is different
$password = "";     // Change if you have a MySQL password
$database = "admin_panel";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize POST data
    $customerName   = $_POST['customerName'] ?? '';
    $contact        = $_POST['contact'] ?? '';
    $referenceName  = $_POST['referenceName'] ?? '';
    $status         = $_POST['status'] ?? 'pending';
    $message        = $_POST['message'] ?? '';

    // Simple validation
    if (!empty($customerName) && !empty($contact)) {
        // Prepare SQL and bind parameters
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, contact, reference_name, status, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $customerName, $contact, $referenceName, $status, $message);

            if ($stmt->execute()) {
                echo "<script>alert('Customer added successfully!'); window.location.href='add-customer.html';</script>";
            } else {
                echo "Database error: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        } else {
            echo "Prepare failed: " . htmlspecialchars($conn->error);
        }
    } else {
        echo "<script>alert('Customer Name and Contact are required.'); window.history.back();</script>";
    }
} else {
    // Invalid access method
    echo "<script>alert('Invalid request method.'); window.location.href='add-customer.html';</script>";
}

$conn->close();
?>
