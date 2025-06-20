<?php
include 'db.php'; // ✅ Establish DB connection
include 'session.php'; // 🔒 Lock page before anything else

$id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch employee data to prefill form
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, username FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Employee not found
    header("Location: list_employees.php");
    exit();
}

$employee = $result->fetch_assoc();

// Handle form submit
if (isset($_POST['update_employee'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);

    // Optional: update password if filled
    $password = $_POST['password'];
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, username=?, password=? WHERE id=?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $username, $hashed_password, $id);
    } else {
        // Password not changed
        $stmt = $conn->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, username=? WHERE id=?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $username, $id);
    }

    if ($stmt->execute()) {
        header("Location: list_employees.php?msg=updated");
        exit();
    } else {
        $error = "Error updating employee: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Important for responsiveness -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
       <!-- Sidebar -->
       <nav class="col-md-2 d-none d-md-block sidebar bg-dark text-white p-0">
            <div class="p-3">
                <h4>Admin Panel</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add-customer.html">Add Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add-loan-customer.php">Add Loan Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loan-customers.php">Loan Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add-employee.php">Add Employee Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="list_employees.php">List Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            <div class="container" style="max-width: 800px;">
                <h2>Edit Employee</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($employee['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($employee['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($employee['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($employee['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password (leave blank to keep unchanged)</label>
                            <input type="password" name="password" class="form-control" placeholder="New password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-start">
                        <button type="submit" name="update_employee" class="btn btn-primary me-2">Update Employee</button>
                        <a href="list_employees.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>
