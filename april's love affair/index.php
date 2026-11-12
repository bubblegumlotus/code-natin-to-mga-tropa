<?php
require_once 'config.php';

if (loggedIn()) {
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCsrf();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $user &&
        $password === $user['password'] &&
        $user['status'] === 'active'
    ) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        redirect("dashboard.php");

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GoComplain - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-page">

<div class="auth-wrap">

    <div class="auth-intro">
        <h1>GoComplain</h1>
        <h2>We're here to make things better.</h2>
        <p>
            Submit complaints, monitor progress,
            and receive updates about your concerns.
        </p>
    </div>

    <div class="auth-panel">

        <div class="auth-card">

            <h2>Sign In</h2>
            <p class="muted">Welcome back!</p>

            <?php if ($error): ?>
                <div class="alert error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="csrf_token"
                       value="<?= e(csrfToken()) ?>">

                <label>Email Address</label>
                <input type="email" name="email"
                       placeholder="Enter your email" required>

                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Enter your password" required>

                <button type="submit" class="btn primary full">
                    Sign In
                </button>

            </form>

            <p class="auth-switch">
                Don't have an account?
                <a href="register.php">Register here</a>
            </p>

        </div>

    </div>

</div>

</body>
</html>