<?php
require_once 'config.php';

if (loggedIn()) {
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCsrf();

    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (
        empty($name) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        $error = "Please enter valid information. Password must be at least 8 characters.";

    } elseif ($password !== $confirm) {

        $error = "Passwords do not match.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->execute([$email]);

        if ($check->fetch()) {

            $error = "Email is already registered.";

        } else {

            // Plain-text password storage
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (full_name, email, password, role, status)
                VALUES (?, ?, ?, 'user', 'active')"
            );

            $stmt->execute([
                $name,
                $email,
                $password
            ]);

            flash("Registration successful! You can now log in.");

            redirect("index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - GoComplain</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-page">

<div class="auth-wrap">

    <div class="auth-intro">
        <h1>GoComplain</h1>
        <h2>Your voice helps us improve.</h2>
        <p>
            Create an account to submit complaints
            and track their progress.
        </p>
    </div>

    <div class="auth-panel">

        <div class="auth-card">

            <h2>Create Account</h2>
            <p class="muted">Register as a new user.</p>

            <?php if ($error): ?>
                <div class="alert error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="csrf_token"
                       value="<?= e(csrfToken()) ?>">

                <label>Full Name</label>
                <input type="text" name="full_name"
                       placeholder="Full name" required>

                <label>Email Address</label>
                <input type="email" name="email"
                       placeholder="Email address" required>

                <label>Password</label>
                <input type="password" name="password"
                       minlength="8" placeholder="Password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                       minlength="8" placeholder="Confirm password" required>

                <button type="submit" class="btn primary full">
                    Register
                </button>

            </form>

            <p class="auth-switch">
                Already have an account?
                <a href="index.php">Sign In</a>
            </p>

        </div>

    </div>

</div>

</body>
</html>