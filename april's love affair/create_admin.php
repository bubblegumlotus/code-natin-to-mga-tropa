<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCsrf();

    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (
        empty($name) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {

        $message = "Please enter valid information.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email=?"
        );

        $check->execute([$email]);

        if ($check->fetch()) {

            $message = "Email already exists.";

        } else {

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (full_name,email,password,role,status)
                VALUES (?,?,?,'admin','active')"
            );

            $stmt->execute([
                $name,
                $email,
                $password
            ]);

            $message = "Admin account created successfully. Delete this file now.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-page">

<div class="auth-card standalone">

    <h2>Create Administrator</h2>

    <?php if ($message): ?>
        <div class="alert">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?= e(csrfToken()) ?>">

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn primary full">
            Create Admin
        </button>

    </form>

</div>

</body>
</html>