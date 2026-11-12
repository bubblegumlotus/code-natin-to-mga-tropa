<?php
require_once 'config.php';
requireLogin();

if (isAdmin()) {
    redirect("manage_complaints.php");
}

$pageTitle = "My Complaints";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="my-complaints"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="false">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="panel">
        <div class="panel-heading">
            <h3>My Submitted Complaints</h3>
            <a href="submit_complaint.php" class="btn primary">
                + New Complaint
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="my-complaints"></tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
