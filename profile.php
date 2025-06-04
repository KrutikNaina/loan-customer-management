<?php
session_start();
include 'db.php';

if (!isset($_SESSION['employee_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

// Fetch employee details including username
$stmt = $conn->prepare("SELECT username, email, phone FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$emp_result = $stmt->get_result();
$employee = $emp_result->fetch_assoc();

if (!$employee) {
    echo "Employee not found!";
    exit();
}

$employee_username = $employee['username'];

// Count loans handled by this employee (by username)
$stmt = $conn->prepare("SELECT COUNT(*) as loan_count FROM loan_customers WHERE handling = ?");
$stmt->bind_param("s", $employee_username);
$stmt->execute();
$loan_result = $stmt->get_result();
$loan_data = $loan_result->fetch_assoc();

// Fetch loans handled by this employee
$stmt = $conn->prepare("SELECT id, customer_name, amount, process FROM loan_customers WHERE handling = ?");
$stmt->bind_param("s", $employee_username);
$stmt->execute();
$loans_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Employee Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f1f4f9;
        }
        .profile-card {
            max-width: 700px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .profile-card h2 {
            color: #333;
        }
        .profile-info p {
            margin: 8px 0;
            font-size: 16px;
        }
        .btn-logout {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card">
    <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($employee['username']); ?></h2>

        
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($employee['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($employee['phone']); ?></p>
            <hr />
            <h5>Total Loans Handled: <span class="text-primary"><?php echo $loan_data['loan_count'] ?? 0; ?></span></h5>
        </div>

        <hr />
        <h4>Loans You Handle</h4>
        <?php if ($loans_result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($loan = $loans_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['id']); ?></td>
                            <td><?php echo htmlspecialchars($loan['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($loan['amount'], 2)); ?></td>
                            <td>
    <?php
        $status = $loan['process'];
        $badgeClass = match($status) {
            'Approved'   => 'bg-success',   // green
            'Disbursed'  => 'bg-primary',   // blue
            'Pending'    => 'bg-warning',   // yellow
            'Rejected'   => 'bg-danger',    // red
            default      => 'bg-secondary'  // gray
        };
    ?>
    <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
</td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No loans found.</p>
        <?php endif; ?>

        <div class="text-center btn-logout">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
