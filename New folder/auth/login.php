    <?php
    session_start();
    include '../includes/db.php'; // Your database connection

    // Static admin credentials
    $adminUsername = "admin";
    $adminPassword = "admin@123";

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Static Admin Check
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['username'] = 'admin';
            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }

        // Employee Check
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM employees WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();

        if ($employee && password_verify($password, $employee['password'])) {
            $_SESSION['employee_id'] = $employee['id'];
            $_SESSION['employee_name'] = $employee['first_name'] . ' ' . $employee['last_name'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'employee';
            header("Location: profile.php");
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    }
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container" style="max-width: 400px; margin-top: 100px;">
        <h2 class="text-center">Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
    </div>
    </body>
    </html>
