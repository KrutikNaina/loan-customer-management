<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_panel";

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Now you can use $conn->query() safely


$conn = new mysqli("localhost", "root", "", "admin_panel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Total Customers
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM loan_customers")->fetch_assoc()['total'];

// Approved Loans
$approvedLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Approved'")->fetch_assoc()['total'];

// Pending Loans
$pendingLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Pending'")->fetch_assoc()['total'];

// Rejected Loans
$rejectedLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Rejected'")->fetch_assoc()['total'];

// Recent Customers
$recentCustomers = $conn->query("SELECT customer_name, mobile_no, process FROM loan_customers ORDER BY id DESC LIMIT 4");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
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
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
                    <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
                    <a class="nav-link" href="add-employee.php">Add Employee Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="list_employees.php">List Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>
        </nav>
        <div class="col-md-10 main-content">
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Customers</h5>
                            <p class="card-text display-4"><?= $totalCustomers ?></p>
                            <a href="loan-customers.php" class="text-white">View details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Approved Loans</h5>
                            <p class="card-text display-4"><?= $approvedLoans ?></p>
                            <a href="approve-loan.php" class="text-white ">View details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Loans</h5>
                            <p class="card-text display-4"><?= $pendingLoans ?></p>
                            <a href="pending-loans.php" class="text-white">View details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Rejected Loans</h5>
                            <p class="card-text display-4"><?= $rejectedLoans ?></p>
                            <a href="rejected-loans.php" class="text-white">View details</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Recent Customers
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $recentCustomers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['customer_name']; ?></td>
                                    <td><?php echo $row['mobile_no']; ?></td>
                                <td>
                                    <?php
                                        $status = $row['process'];
                                        $badgeClass = match($status) {
                                        'Approved'   => 'bg-success',   // green
                                        'Disbursed'  => 'bg-primary',   // blue
                                        'Pending'    => 'bg-warning',   // yellow
                                        'Rejected'   => 'bg-danger',    // red
                                        default      => 'bg-secondary'  // gray
                                        };
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Loan Status
                        </div>
                        <div class="card-body">
                            <canvas id="loanChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('loanChart').getContext('2d');
    const loanChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [<?= $approvedLoans ?>, <?= $pendingLoans ?>, <?= $rejectedLoans ?>],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
</body>
</html>
