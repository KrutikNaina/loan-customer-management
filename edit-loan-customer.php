<?php
// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_panel";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update data
    $customer_name = $_POST['customer_name'];
    $mobile_no = $_POST['mobile_no'];
    $reference = $_POST['reference'];
    $handling = $_POST['handling'];
    $types = $_POST['types'];
    $product = $_POST['product'];
    $bank = $_POST['bank'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $process = $_POST['process'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("UPDATE loan_customers SET customer_name=?, mobile_no=?, reference=?, handling=?, types=?, product=?, bank=?, amount=?, date=?, process=?, remarks=? WHERE id=?");
    $stmt->bind_param("sssssssssssi", $customer_name, $mobile_no, $reference, $handling, $types, $product, $bank, $amount, $date, $process, $remarks, $id);
    $stmt->execute();

    header("Location: loan-customers.php");
    exit();
} else {
    // Fetch current data
    $stmt = $conn->prepare("SELECT * FROM loan_customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Loan Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-3">
            <h4>Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="add-customer.html">Add Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="add-loan-customer.html">Add Loan Customer</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Loan Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="login.html">Logout</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Edit Loan Customer</h2>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" value="<?php echo $data['customer_name']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Mobile No</label>
                        <input type="text" name="mobile_no" class="form-control" value="<?php echo $data['mobile_no']; ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Reference</label>
                        <input type="text" name="reference" class="form-control" value="<?php echo $data['reference']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Handling</label>
                        <input type="text" name="handling" class="form-control" value="<?php echo $data['handling']; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Types</label>
                        <input type="text" name="types" class="form-control" value="<?php echo $data['types']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Product</label>
                        <input type="text" name="product" class="form-control" value="<?php echo $data['product']; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Bank</label>
                        <input type="text" name="bank" class="form-control" value="<?php echo $data['bank']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" value="<?php echo $data['amount']; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo $data['date']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Process</label>
                        <select name="process" class="form-control">
                            <?php
                            $statuses = ['Pending', 'Approved', 'Rejected', 'Disbursed', 'Completed'];
                            foreach ($statuses as $status) {
                                $selected = ($data['process'] == $status) ? "selected" : "";
                                echo "<option value='$status' $selected>$status</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control"><?php echo $data['remarks']; ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Update</button>
                <a href="loan-customers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
                                