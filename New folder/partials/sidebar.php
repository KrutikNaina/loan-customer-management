<?php
$currentPage = basename($_SERVER['PHP_SELF']); // e.g., dashboard.php
?>

<nav class="col-md-2 d-none d-md-block sidebar bg-dark text-white p-0">
  <div class="p-3">
    <h4>Admin Panel</h4>
  </div>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'add-customer.php') ? 'active' : '' ?>" href=".loan/add-customer.php">Add Customer</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'add-loan-customer.html') ? 'active' : '' ?>" href="loan/add-loan-customer.html">Add Loan Customer</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'loan-customers.php') ? 'active' : '' ?>" href="loan/loan-customers.php">Loan Customers</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'add-employee.php') ? 'active' : '' ?>" href="employee/add-employee.php">Add Employee Customers</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'list_employees.php') ? 'active' : '' ?>" href="employee/list_employees.php">List Employee</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($currentPage === 'login.php') ? 'active' : '' ?>" href="login.php">Logout</a>
    </li>
  </ul>
</nav>
