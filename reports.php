<?php
require_once 'config.php';
requireAdmin();

$pageTitle = "Reports & Analytics";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="reports"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="true">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="stats-grid">
        <div class="stat-card">
            <p>Total Complaints</p>
            <strong id="report-total">0</strong>
        </div>
        <div class="stat-card">
            <p>New</p>
            <strong id="report-new">0</strong>
        </div>
        <div class="stat-card">
            <p>In Progress</p>
            <strong id="report-progress">0</strong>
        </div>
        <div class="stat-card">
            <p>Resolved</p>
            <strong id="report-resolved">0</strong>
        </div>
    </div>

    <div class="two-panels">
        <div class="panel">
            <h3>Complaints by Status</h3>
            <div id="report-statuses"></div>
        </div>
        <div class="panel">
            <h3>Complaints by Category</h3>
            <div id="report-categories"></div>
        </div>
    </div>

    <div class="panel">
        <h3>Monthly Complaints</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Complaints</th>
                </tr>
            </thead>
            <tbody id="report-monthly"></tbody>
        </table>
    </div>
</div>

<?php require 'footer.php'; ?>
