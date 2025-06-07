<?php
session_start();
include 'db.php';

$msg = '';

// Handle possible messages (add, update, delete)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $msg = "Employee added successfully!";
    elseif ($_GET['msg'] == 'updated') $msg = "Employee updated successfully!";
    elseif ($_GET['msg'] == 'deleted') $msg = "Employee deleted successfully!";
}

// Fetch all employees
$result = $conn->query("SELECT id, first_name, last_name, email, phone, username FROM employees ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this employee?')) {
                window.location.href = 'delete_employee.php?id=' + id;
            }
        }
    </script>
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
                    <a class="nav-link active" href="list_employees.php">List Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4 pt-4">
            <h2>Employees</h2>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <a href="add-employee.php" class="btn btn-primary mb-3">Add New Employee</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td>
                                    <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No employees found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </main>
    </div>
</div>
</body>
</html>
