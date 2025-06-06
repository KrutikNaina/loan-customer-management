<?php
/* ──────────────────────────────────────────────────────────
   DB connection + admin-only gate
────────────────────────────────────────────────────────── */
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "admin_panel";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

/* ──────────────────────────────────────────────────────────
   Fetch rows  (only Pending)
────────────────────────────────────────────────────────── */
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search_query !== '') {                     // search mode
    $escaped = $conn->real_escape_string($search_query);
    $sql = "SELECT * FROM loan_customers
            WHERE process = 'Pending' AND (
                  customer_name LIKE '%$escaped%'  OR mobile_no  LIKE '%$escaped%'
               OR reference     LIKE '%$escaped%'  OR handling   LIKE '%$escaped%'
               OR types         LIKE '%$escaped%'  OR product    LIKE '%$escaped%'
               OR bank          LIKE '%$escaped%'  OR remarks    LIKE '%$escaped%' )
            ORDER BY id DESC";
} else {                                        // plain list
    $sql = "SELECT * FROM loan_customers WHERE process = 'Pending' ORDER BY id DESC";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Panel – Pending Loans</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
.sidebar{min-height:100vh;background:#343a40;color:#fff}
.sidebar .nav-link{color:rgba(255,255,255,.75)}
.sidebar .nav-link:hover{color:#fff}
.sidebar .nav-link.active{color:#fff;background:#007bff}
.main-content{padding:20px}
.table-container{background:#fff;padding:20px;border-radius:5px;
                 box-shadow:0 0 10px rgba(0,0,0,.1)}
.status-badge{padding:5px 10px;border-radius:20px;font-size:.8rem;font-weight:500}
.status-pending{background:#fff3cd;color:#856404}
.btn-export{background:#28a745;color:#fff;margin-right:10px}
.btn-export:hover{background:#218838;color:#fff}
#loanCustomersTable td,#loanCustomersTable th{font-size:14px;white-space:nowrap}
</style>
</head>

<body>
<div class="container-fluid">
 <div class="row">
  <!-- Sidebar -->
  <nav class="col-md-2 d-none d-md-block sidebar p-0">
      <div class="p-3"><h4>Admin Panel</h4></div>
      <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="add-customer.html">Add Customer</a></li>
          <li class="nav-item"><a class="nav-link" href="add-loan-customer.html">Add Loan Customer</a></li>
          <li class="nav-item"><a class="nav-link" href="loan-customers.php">Approved Loans</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Pending Loans</a></li>
          <li class="nav-item"><a class="nav-link" href="add-employee.php">Add Employee</a></li>
          <li class="nav-item"><a class="nav-link" href="list_employees.php">List Employees</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
      </ul>
  </nav>

  <!-- Main -->
  <div class="col-md-10 main-content">
    <h2>Pending Loan Customers</h2>
    <div class="table-container">

      <!-- Top bar -->
      <div class="d-flex justify-content-between mb-3">
          <div class="input-group" style="width:400px">
              <input id="searchInput" type="text" class="form-control" placeholder="Search..."
                     value="<?=htmlspecialchars($search_query)?>">
              <button id="searchBtn" class="btn btn-outline-secondary" type="button">Search</button>
          </div>
          <div>
              <button id="exportExcel" class="btn btn-export">Export to Excel</button>
              <a href="add-loan-customer.html" class="btn btn-success">Add New</a>
          </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table id="loanCustomersTable" class="table table-striped table-hover table-bordered w-100">
            <thead>
                <tr>
                    <th>No.</th><th>Customer Name</th><th>Mo. No</th><th>Reference</th><th>Handling</th>
                    <th>Types</th><th>Product</th><th>Bank</th><th>Amount</th><th>Date</th>
                    <th>Process</th><th>Remarks</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php if ($result && $result->num_rows): 
    $i = 1;
    while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td><?= htmlspecialchars($row['mobile_no']) ?></td>
        <td><?= htmlspecialchars($row['reference']) ?></td>
        <td><?= htmlspecialchars($row['handling']) ?></td>
        <td><?= htmlspecialchars($row['types']) ?></td>
        <td><?= htmlspecialchars($row['product']) ?></td>
        <td><?= htmlspecialchars($row['bank']) ?></td>
        <td>₹<?= number_format($row['amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td>
            <span class="status-badge status-<?= strtolower($row['process']) ?>">
                <?= htmlspecialchars($row['process']) ?>
            </span>
        </td>
        <td><?= htmlspecialchars($row['remarks']) ?></td>
        <td>
            <?php if (strtolower($row['process']) == 'rejected'): ?>
                <a href="view-loan-customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">View Details</a>
            <?php else: ?>
                <a href="edit-loan-customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">Delete</button>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="delete-loan-customer.php">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?= $row['id'] ?>">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete <strong><?= htmlspecialchars($row['customer_name']) ?></strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="13" class="text-center">No data found</td></tr>
<?php endif; ?>
</tbody>

        </table>
      </div>
    </div><!-- /.table-container -->
  </div><!-- /.main-content -->
 </div><!-- /.row -->
</div><!-- /.container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Excel export
document.getElementById('exportExcel').addEventListener('click', () => {
    const table = document.getElementById('loanCustomersTable');
    const wb = XLSX.utils.book_new();
    const clone = table.cloneNode(true);
    clone.querySelectorAll('td:last-child,th:last-child').forEach(c=>c.remove());
    XLSX.utils.book_append_sheet(wb,XLSX.utils.table_to_sheet(clone),"Pending Loans");
    XLSX.writeFile(wb,'Pending_Loans_'+new Date().toISOString().slice(0,10)+'.xlsx');
});

// search
document.getElementById('searchBtn').addEventListener('click', ()=>{
    const q=document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#loanCustomersTable tbody tr').forEach(r=>{
        r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
    });
});
</script>
</body>
</html>
