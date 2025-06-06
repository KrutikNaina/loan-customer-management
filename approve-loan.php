<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_panel";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_query)) {
    $escaped = $conn->real_escape_string($search_query);
    $sql = "SELECT * FROM loan_customers 
            WHERE process = 'Approved' AND (
                customer_name LIKE '%$escaped%' 
                OR mobile_no LIKE '%$escaped%' 
                OR reference LIKE '%$escaped%'
                OR handling LIKE '%$escaped%'
                OR types LIKE '%$escaped%'
                OR product LIKE '%$escaped%'
                OR bank LIKE '%$escaped%'
                OR remarks LIKE '%$escaped%'
            )
            ORDER BY id DESC";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT * FROM loan_customers WHERE process = 'Approved' ORDER BY id DESC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Approved Loans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); }
        .sidebar .nav-link:hover { color: white; }
        .sidebar .nav-link.active { color: white; background-color: #007bff; }
        .main-content { padding: 20px; }
        .table-container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; text-transform: capitalize; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .btn-export { background-color: #28a745; color: white; margin-right: 10px; }
        .btn-export:hover { background-color: #218838; color: white; }
        #loanCustomersTable td, #loanCustomersTable th {
            font-size: 14px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark text-white p-0">
            <div class="p-3"><h4>Admin Panel</h4></div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="add-customer.html">Add Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="add-loan-customer.html">Add Loan Customer</a></li>
                <li class="nav-item"><a class="nav-link active" href="loan-customers.php">Loan Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="add-employee.php">Add Employee Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="list_employees.php">List Employee</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
            </ul>
        </nav>
        <div class="col-md-10 main-content">
            <h2>Approved Loan Customers</h2>
            <div class="table-container">
                <div class="d-flex justify-content-between mb-3">
                    <div class="input-group" style="width: 400px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search..." value="<?= htmlspecialchars($search_query ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="button">Search</button>
                    </div>
                    <div>
                        <button id="exportExcel" class="btn btn-export">Export to Excel</button>
                        <a href="add-loan-customer.html" class="btn btn-success">Add New</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="loanCustomersTable" class="table table-striped table-hover table-bordered w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Customer Name</th>
                                <th>Mo. No</th>
                                <th>Reference</th>
                                <th>Handling</th>
                                <th>Types</th>
                                <th>Product</th>
                                <th>Bank</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Process</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $count = 1;
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $count++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['mobile_no']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reference']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['handling']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['types']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['product']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                    echo "<td>₹" . number_format($row['amount'], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                    echo "<td><span class='status-badge status-approved'>" . htmlspecialchars($row['process']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                                    echo "<td>
                                    <a href='edit-loan-customer.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edit</a>

                                    <button type='button' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal" . $row['id'] . "'>Delete</button>
                                
                                    <!-- Delete Confirmation Modal -->
                                    <div class='modal fade' id='deleteModal" . $row['id'] . "' tabindex='-1' aria-labelledby='deleteModalLabel" . $row['id'] . "' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <form method='POST' action='delete-loan-customer.php'>
                                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='deleteModalLabel" . $row['id'] . "'>Confirm Delete</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        Are you sure you want to delete <strong>" . htmlspecialchars($row['customer_name']) . "</strong>?
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                        <button type='submit' class='btn btn-danger'>Yes, Delete</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>";
                                }
                            } else {
                                echo "<tr><td colspan='13' class='text-center'>No approved loans found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('exportExcel').addEventListener('click', function() {
        const table = document.getElementById('loanCustomersTable');
        const wb = XLSX.utils.book_new();
        const tableClone = table.cloneNode(true);
        const actionCells = tableClone.querySelectorAll('td:last-child, th:last-child');
        actionCells.forEach(cell => cell.remove());
        const ws = XLSX.utils.table_to_sheet(tableClone);
        XLSX.utils.book_append_sheet(wb, ws, "Approved Loans");
        const fileName = 'Approved_Loans_' + new Date().toISOString().slice(0, 10) + '.xlsx';
        XLSX.writeFile(wb, fileName);
    });

    document.querySelector('.btn-outline-secondary').addEventListener('click', function() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#loanCustomersTable tbody tr');
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchText) ? '' : 'none';
        });
    });
</script>
</body>
</html>
