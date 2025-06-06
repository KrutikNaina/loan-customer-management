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

// Total Customers
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM loan_customers")->fetch_assoc()['total'];

// Approved Loans
$approvedLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Approved'")->fetch_assoc()['total'];

// Pending Loans
$pendingLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Pending'")->fetch_assoc()['total'];

// Rejected Loans
$rejectedLoans = $conn->query("SELECT COUNT(*) AS total FROM loan_customers WHERE process = 'Rejected'")->fetch_assoc()['total'];

// Recent Customers
$recentCustomers = $conn->query("SELECT customer_name, mobile_no, process FROM loan_customers ORDER BY id DESC LIMIT 6");

// Monthly loans (grouped by month)
$monthlyLoanCounts = array_fill(0, 12, 0);
$result = $conn->query("SELECT MONTH(created_at) as month, COUNT(*) as count FROM loan_customers GROUP BY month");
while ($row = $result->fetch_assoc()) {
    $monthlyLoanCounts[$row['month'] - 1] = (int)$row['count'];
}

$topEmployees = [];
$employeeCounts = [];

$result = $conn->query("
    SELECT handling AS employee_name, COUNT(*) AS count
    FROM loan_customers
    GROUP BY handling
    ORDER BY count DESC
    LIMIT 5
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $topEmployees[] = $row['employee_name'];
        $employeeCounts[] = (int)$row['count'];
    }
} else {
    $topEmployees = ['No Data'];
    $employeeCounts = [0];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-body.p-0 {
            max-height: 300px;  /* fixed height, adjust as needed */
            overflow-y: auto;   /* vertical scrollbar if content overflows */
        }

        /* Fix chart canvas height to avoid shifting */
        .card-body canvas {
            height: 300px !important;
        }
        @media (max-width: 767px) {
            /* Add spacing between stacked cards on small screens */
            .card {
                margin-bottom: 1.5rem;
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
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="loan/add-customer.html">Add Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="loan/add-loan-customer.html">Add Loan Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="loan/loan-customers.php">Loan Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="employee/add-employee.php">Add Employee Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="employee/list_employees.php">List Employee</a></li>
                <li class="nav-item"><a class="nav-link" href="auth/login.php">Logout</a></li>
            </ul>
        </nav>

        <main class="col-md-10 main-content">
            <h2>Dashboard</h2>
            <div class="row">
                <!-- Summary cards -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Loans</h5>
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
                            <a href="approve-loan.php" class="text-white">View details</a>
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

            <!-- Recent Customers + Loan Status Chart -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Recent Customers</div>
                        <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr><th>Name</th><th>Contact</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $recentCustomers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                                        <td>
                                            <?php
                                            $status = $row['process'];
                                            $badgeClass = match($status) {
                                                'Approved'   => 'bg-success',
                                                'Disbursed'  => 'bg-primary',
                                                'Pending'    => 'bg-warning',
                                                'Rejected'   => 'bg-danger',
                                                default      => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
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
                        <div class="card-header">Loan Status</div>
                        <div class="card-body">
                            <canvas id="loanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Loan Graph + Top Employees -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Monthly Loan Graph</div>
                        <div class="card-body">
                            <canvas id="monthlyLoanChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Top Employees by Loans Handled</div>
                        <div class="card-body">
                            <canvas id="topEmployeesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Loan Status Doughnut Chart
    const loanCtx = document.getElementById('loanChart').getContext('2d');
    new Chart(loanCtx, {
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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Loan Bar Chart
    const monthlyCtx = document.getElementById('monthlyLoanChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['January','February','March','April','May','June','July','August','September','October','November','December'],
            datasets: [{
                label: 'Loans Issued',
                data: <?= json_encode($monthlyLoanCounts) ?>,
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });

    // Top Employees Horizontal Bar Chart
    const employeeCtx = document.getElementById('topEmployeesChart').getContext('2d');
    new Chart(employeeCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($topEmployees) ?>,
            datasets: [{
                label: 'Loans Handled',
                data: <?= json_encode($employeeCounts) ?>,
                backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#6f42c1', '#fd7e14']
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
