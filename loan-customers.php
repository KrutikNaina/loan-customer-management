<?php
include 'session.php'; // 🔒 Lock page before anything else

$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_panel";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $stmt = $conn->prepare("DELETE FROM loan_customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: loan-customers.php");
    exit();
}

// Search logic
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_query)) {
    $escaped = $conn->real_escape_string($search_query);
    $sql = "SELECT * FROM loan_customers 
            WHERE customer_name LIKE '%$escaped%' 
               OR mobile_no LIKE '%$escaped%' 
               OR reference LIKE '%$escaped%'
               OR handling LIKE '%$escaped%'
               OR types LIKE '%$escaped%'
               OR product LIKE '%$escaped%'
               OR bank LIKE '%$escaped%'
               OR process LIKE '%$escaped%'
               OR remarks LIKE '%$escaped%'
            ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM loan_customers ORDER BY id DESC";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Loan Customers</title>
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
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .status-disbursed { background-color: #cce5ff; color: #004085; }
    .status-completed { background-color: #d1ecf1; color: #0c5460; }
    .btn-export { background-color: #28a745; color: white; margin-right: 10px; }
    .btn-export:hover { background-color: #218838; color: white; }
    #loanCustomersTable td, #loanCustomersTable th { font-size: 14px; white-space: nowrap; }
    .modal { z-index: 1050; }

    .table-container {
  max-height: 600px;
  overflow-y: auto;
  position: relative;
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="add-customer.html">Add Customer</a></li>
        <li class="nav-item"><a class="nav-link" href="add-loan-customer.php">Add Loan Customer</a></li>
        <li class="nav-item"><a class="nav-link active" href="loan-customers.php">Loan Customers</a></li>
        <li class="nav-item"><a class="nav-link" href="add-employee.php">Add Employee Customers</a></li>
        <li class="nav-item"><a class="nav-link" href="list_employees.php">List Employee</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <div class="col-md-10 main-content">
      <h2>Loan Customers</h2>
      <div class="table-container">
      <div class="container mb-4">
  <div class="row g-3 align-items-end justify-content-between">
    
    <!-- Search Input -->
    <div class="col-md-5 col-lg-4">
      <div class="input-group">
        <input type="text" class="form-control" name="search" placeholder="Search..." value="<?= htmlspecialchars($search_query ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </div>

    <!-- Filters & Actions -->
    <div class="col-md-7 col-lg-8 d-flex flex-wrap align-items-center justify-content-end gap-2">
      <div class="d-flex align-items-center">
        <label for="startDate" class="form-label me-2 mb-0">Start:</label>
        <input type="date" id="startDate" class="form-control me-3" style="min-width: 160px;">
      </div>

      <div class="d-flex align-items-center">
        <label for="endDate" class="form-label me-2 mb-0">End:</label>
        <input type="date" id="endDate" class="form-control me-3" style="min-width: 160px;">
      </div>
      <button id="exportExcel" class="btn btn-outline-primary">📁 Export</button>
      <a href="add-loan-customer.php" class="btn btn-success"> Add New</a>
    </div>

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
                while ($row = $result->fetch_assoc()) {
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
                  echo "<td><span class='status-badge status-" . strtolower($row['process']) . "'>" . htmlspecialchars($row['process']) . "</span></td>";
                  echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                  echo "<td>
                          <a href='edit-loan-customer.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edit</a>
                          <button type='button' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal" . $row['id'] . "'>Delete</button>
                          <div class='modal fade' id='deleteModal" . $row['id'] . "' tabindex='-1' aria-labelledby='deleteModalLabel" . $row['id'] . "' aria-hidden='true'>
                            <div class='modal-dialog'>
                              <form method='POST' action='delete-loan-customer.php'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <div class='modal-content'>
                                  <div class='modal-header'>
                                    <h5 class='modal-title'>Confirm Delete</h5>
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
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='13' class='text-center'>No data found</td></tr>";
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
  document.getElementById('exportExcel').addEventListener('click', function () {
    const table = document.getElementById('loanCustomersTable');
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    const wb = XLSX.utils.book_new();
    const ws_data = [];
    const headers = [];

    table.querySelectorAll('thead th').forEach((th, index, arr) => {
      if (index !== arr.length - 1) headers.push(th.innerText.trim());
    });
    ws_data.push(headers);

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      const dateCell = cells[9].innerText.trim();

      if (startDate && endDate) {
        const rowDate = new Date(dateCell);
        const start = new Date(startDate);
        const end = new Date(endDate);
        if (rowDate < start || rowDate > end) return;
      }

      const rowData = [];
      cells.forEach((cell, i) => {
        if (i !== cells.length - 1) rowData.push(cell.innerText.trim());
      });
      ws_data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(ws_data);
    XLSX.utils.book_append_sheet(wb, ws, "Filtered Loan Customers");
    const filename = 'Loan_Customers_Filtered_' + new Date().toISOString().slice(0, 10) + '.xlsx';
    XLSX.writeFile(wb, filename);
  });

  document.querySelector('.btn-outline-secondary').addEventListener('click', function () {
    const searchText = document.querySelector('.form-control').value.toLowerCase();
    const rows = document.querySelectorAll('#loanCustomersTable tbody tr');

    rows.forEach(row => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(searchText) ? '' : 'none';
    });
  });
</script>
</body>
</html>
