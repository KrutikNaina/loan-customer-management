<?php
include 'session.php'; // 🔒 Lock page before anything else


$id = intval($_GET['id']);

// Delete employee
$stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list_employees.php?msg=deleted");
    exit();
} else {
    echo "Error deleting employee: " . $stmt->error;
}
