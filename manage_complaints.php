<?php
require_once 'config.php';
requireAdmin();

$pageTitle = "Manage Complaints";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="manage-complaints"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="true">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="panel">
        <h3>All Customer Complaints</h3>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Customer</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="managed-complaints"></tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
