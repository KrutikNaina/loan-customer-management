<?php include 'session.php'; // 🔒 Lock page before anything else ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
        }

        .sidebar .nav-link:hover {
            color: white;
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }

        .main-content {
            padding: 20px;
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
                        <a class="nav-link active" href="add-customer.php">Add Customer</a>
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

            <div class="col-md-10 main-content">
                <h2>Add New Customer</h2>
                <form action="add_customer.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" name="customerName"
                                placeholder="Enter customer name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact" name="contact"
                                placeholder="Enter contact number" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="referenceName" class="form-label">Reference Name</label>
                            <input type="text" class="form-control" id="referenceName" name="referenceName"
                                placeholder="Enter reference name">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3"
                            placeholder="Enter any message"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>