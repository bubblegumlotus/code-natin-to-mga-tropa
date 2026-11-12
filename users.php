<?php
require_once 'config.php';
requireAdmin();

$stmt = $pdo->query(
    "SELECT * FROM users ORDER BY created_at DESC"
);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "User Management";
require 'header.php';
?>

<div class="panel">

    <div class="panel-heading">

        <h3>Registered Users</h3>

        <a href="edit_user.php" class="btn primary">
            + Add User
        </a>

    </div>

    <div class="table-wrap">

        <table>

            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $u): ?>

            <tr>

                <td><?= (int)$u['id'] ?></td>

                <td><?= e($u['full_name']) ?></td>

                <td><?= e($u['email']) ?></td>

                <td><?= e($u['role']) ?></td>

                <td><?= e($u['status']) ?></td>

                <td>

                    <a class="text-link"
                       href="edit_user.php?id=<?= (int)$u['id'] ?>">
                        Edit
                    </a>

                    <form method="POST"
                          action="user_action.php"
                          class="inline-delete"
                          onsubmit="return confirm('Delete this user?')">

                        <input type="hidden" name="csrf_token"
                               value="<?= e(csrfToken()) ?>">

                        <input type="hidden" name="id"
                               value="<?= (int)$u['id'] ?>">

                        <button name="delete"
                                class="link-danger"
                                <?= $u['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

            <?php endforeach; ?>

        </table>

    </div>

</div>

<?php require 'footer.php'; ?>