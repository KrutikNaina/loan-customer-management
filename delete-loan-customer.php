<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $conn = new mysqli("localhost", "root", "", "admin_panel");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM loan_customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: loan-customers.php");
    exit();
}
?>
