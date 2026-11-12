<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("users.php");
}

verifyCsrf();

if (isset($_POST['delete'])) {

    $id = (int)$_POST['id'];

    if ($id == $_SESSION['user_id']) {
        flash("You cannot delete your own account.", "error");
        redirect("users.php");
    }

    $stmt = $pdo->prepare(
        "DELETE FROM users WHERE id=?"
    );

    $stmt->execute([$id]);

    flash("User successfully deleted.");
}

redirect("users.php");
?>