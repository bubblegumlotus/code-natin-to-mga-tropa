<?php
require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$error = "";

if ($id <= 0) {
    redirect("users.php");
}

$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE id=?"
);

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCsrf();

    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        empty($name) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($role, ['admin','user'], true) ||
        !in_array($status, ['active','inactive'], true)
    ) {

        $error = "Please enter valid information.";

    } elseif (
        $id == $_SESSION['user_id'] &&
        ($role != 'admin' || $status != 'active')
    ) {

        $error = "You cannot remove your own admin access.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email=? AND id<>?"
        );

        $check->execute([$email, $id]);

        if ($check->fetch()) {

            $error = "Email already exists.";

        } else {

            if ($password !== "") {
                $stmt = $pdo->prepare(
                    "UPDATE users
                     SET full_name=?, email=?, password=?, role=?, status=?
                     WHERE id=?"
                );

                $stmt->execute([
                    $name,
                    $email,
                    $password,
                    $role,
                    $status,
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE users
                     SET full_name=?, email=?, role=?, status=?
                     WHERE id=?"
                );

                $stmt->execute([
                    $name,
                    $email,
                    $role,
                    $status,
                    $id
                ]);
            }

            flash("User successfully updated.");
            redirect("users.php");
        }
    }
}

$pageTitle = "Edit User";
require 'header.php';
?>

<div class="content-narrow panel">

    <h3>Edit User</h3>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?= e(csrfToken()) ?>">

        <label>Full Name</label>

        <input type="text" name="full_name"
               value="<?= e($user['full_name'] ?? '') ?>"
               required>

        <label>Email</label>

        <input type="email" name="email"
               value="<?= e($user['email'] ?? '') ?>"
               required>

        <label>Password</label>

        <input type="password" name="password"
               placeholder="Leave blank to keep current password">

        <label>Role</label>

        <select name="role">

            <option value="user"
                <?= ($user['role'] ?? 'user') == 'user' ? 'selected' : '' ?>>
                User
            </option>

            <option value="admin"
                <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>
                Admin
            </option>

        </select>

        <label>Account Status</label>

        <select name="status">

            <option value="active"
                <?= ($user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>
                Active
            </option>

            <option value="inactive"
                <?= ($user['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>
                Inactive
            </option>

        </select>

        <br><br>

        <button class="btn primary">Save Changes</button>

        <a href="users.php" class="btn secondary">Cancel</a>

    </form>

</div>

<?php require 'footer.php'; ?>