<?php
// Database connection
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = "";     // default for XAMPP
$database = "admin_panel"; // your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and fetch POST data
$customerName = $_POST['customerName'] ?? '';
$mobileNo = $_POST['mobileNo'] ?? '';
$reference = $_POST['reference'] ?? '';
$handling = $_POST['handling'] ?? '';
$types = $_POST['types'] ?? '';
$product = $_POST['product'] ?? '';
$bank = $_POST['bank'] ?? '';
$amount = $_POST['amount'] ?? '';
$date = $_POST['date'] ?? '';
$process = $_POST['process'] ?? '';
$remarks = $_POST['remarks'] ?? '';

if (!empty($customerName) && !empty($mobileNo) && !empty($amount) && !empty($date)) {
    $stmt = $conn->prepare("INSERT INTO loan_customers (customer_name, mobile_no, reference, handling, types, product, bank, amount, date, process, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssisss", $customerName, $mobileNo, $reference, $handling, $types, $product, $bank, $amount, $date, $process, $remarks);

    if ($stmt->execute()) {
        echo "<script>alert('Loan Customer added successfully!'); window.location.href='add-loan-customer.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Required fields are missing (Customer Name, Mobile No, Amount, Date).";
}

$conn->close();
?>
