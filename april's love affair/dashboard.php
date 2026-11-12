<?php
require_once 'config.php';
requireLogin();

$pageTitle = "Dashboard";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="dashboard"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="<?= isAdmin() ? 'true' : 'false' ?>">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="welcome-row">
        <div>
            <h2>Hello, <?= e($_SESSION['full_name']) ?>!</h2>
            <p class="muted">
                Welcome to your complaint management dashboard.
            </p>
        </div>

        <?php if (!isAdmin()): ?>
            <a href="submit_complaint.php" class="btn primary">
                + Submit Complaint
            </a>
        <?php endif; ?>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <p><?= isAdmin() ? 'Total Complaints' : 'My Complaints' ?></p>
            <strong id="complaint-total">0</strong>
        </div>
        <div class="stat-card">
            <p>Pending Complaints</p>
            <strong id="complaint-pending">0</strong>
        </div>
        <div class="stat-card">
            <p>In Progress</p>
            <strong id="complaint-progress">0</strong>
        </div>
        <div class="stat-card">
            <p>Resolved</p>
            <strong id="complaint-resolved">0</strong>
        </div>
    </div>

    <div class="panel">
        <h3>Recent Complaints</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <?php if (isAdmin()): ?>
                            <th>Customer</th>
                        <?php endif; ?>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="dashboard-complaints"></tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
