<?php
session_start();
include '../includes/db.php'; // Ensure this connects and creates $conn

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

if (isset($_POST['add_employee'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $username   = $_POST['username'];
    $password   = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password

    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, phone, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $username, $password);

    if ($stmt->execute()) {
        $success = "Employee added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<!-- Ensure this is part of your PHP file -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }

        .main-content {
            padding: 30px;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .alert {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                padding: 10px 0;
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
                    <a class="nav-link" href="add-loan-customer.html">Add Loan Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loan-customers.php">Loan Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="add-employee.php">Add Employee Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="list_employees.php">List Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 col-sm-12 main-content">
            <div class="form-section">
                <h2 class="mb-4">Add New Employee</h2>

                <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
                <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start">
                        <button type="submit" name="add_employee" class="btn btn-primary me-2">Add Employee</button>
                        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

</body>
</html>
