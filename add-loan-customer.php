<?php
include 'session.php'; // 🔒 Lock page before anything else
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Loan Customer</title>
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 20px;
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
                <li class="nav-item"><a class="nav-link" href="add-customer.php">Add Customer</a></li>
                <li class="nav-item"><a class="nav-link active" href="add-loan-customer.php">Add Loan Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="loan-customers.php">Loan Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="add-employee.php">Add Employee Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="list_employees.php">List Employee</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </nav>

            <!-- Main Content Area -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Add Loan Customer</h2>
                    <div>
                        <a href="loan-customers.php" class="btn btn-outline-secondary me-2">View All Customers</a>
                    </div>
                </div>

                <div class="form-container">
                    <div class="form-header">
                        <h3>Loan Customer Information</h3>
                        <p class="text-muted">Please fill all the required fields</p>
                    </div>

                    <form method="POST" action="add_loan_customer.php">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="number" class="form-label">NO.</label>
                                    <input type="text" class="form-control" id="number" name="number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customerName" class="form-label">CUSTOMER NAME <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customerName" name="customerName"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mobileNo" class="form-label">MO. NO <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="mobileNo" name="mobileNo" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reference" class="form-label">REFERENCE</label>
                                    <input type="text" class="form-control" id="reference" name="reference">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="handling" class="form-label">HANDLING</label>
                                    <select class="form-select" id="handling" name="handling">
                                        <option value="">Select handling</option>
                                        <option value="agent1">Agent 1</option>
                                        <option value="agent2">Agent 2</option>
                                        <option value="agent3">Agent 3</option>
                                        <option value="direct">Direct</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="types" class="form-label">TYPES</label>
                                    <select class="form-select" id="types" name="types">
                                        <option value="">Select type</option>
                                        <option value="personal">Personal</option>
                                        <option value="business">Business</option>
                                        <option value="education">Education</option>
                                        <option value="home">Home</option>
                                        <option value="auto">Auto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product" class="form-label">PRODUCT</label>
                                    <select class="form-select" id="product" name="product">
                                        <option value="">Select product</option>
                                        <option value="personal_loan">Personal Loan</option>
                                        <option value="business_loan">Business Loan</option>
                                        <option value="home_loan">Home Loan</option>
                                        <option value="car_loan">Car Loan</option>
                                        <option value="education_loan">Education Loan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bank" class="form-label">BANK</label>
                                    <select class="form-select" id="bank" name="bank">
                                        <option value="">Select bank</option>
                                        <option value="abc">ABC Bank</option>
                                        <option value="xyz">XYZ Bank</option>
                                        <option value="def">DEF Bank</option>
                                        <option value="ghi">GHI Bank</option>
                                        <option value="jkl">JKL Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount" class="form-label">AMOUNT <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="amount" name="amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date" class="form-label">DATE <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="process" class="form-label">PROCESS</label>
                                    <select class="form-select" id="process" name="process">
                                        <option value="">Select status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="disbursed">Disbursed</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="remarks" class="form-label">REMARKS</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set today's date as default
        document.getElementById('date').valueAsDate = new Date();

        // Auto-increment number (simplified example)
        document.getElementById('number').value = "LC" + Math.floor(1000 + Math.random() * 9000);
    </script>
</body>

</html>