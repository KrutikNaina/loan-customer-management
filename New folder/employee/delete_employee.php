<?php
session_start();
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: list_employees.php");
    exit();
}

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
