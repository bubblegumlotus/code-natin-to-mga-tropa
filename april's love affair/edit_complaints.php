<?php
require_once 'config.php';
requireAdmin();

$pageTitle = "Edit Complaint";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="edit-complaint"
     data-complaint-id="<?= e($_GET['id'] ?? '') ?>"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="true">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="content-narrow panel">
        <h3>Update Complaint</h3>
        <div id="edit-complaint-details"></div>

        <form id="edit-complaint-form">
            <label for="complaint-status">Complaint Status</label>
            <select id="complaint-status" name="status" required>
                <option>New</option>
                <option>In Progress</option>
                <option>Resolved</option>
                <option>Rejected</option>
            </select>

            <label for="complaint-priority">Priority</label>
            <select id="complaint-priority" name="priority" required>
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
                <option>Urgent</option>
            </select>

            <label for="admin-remarks">Admin Remarks</label>
            <textarea id="admin-remarks" name="admin_remarks" rows="5"></textarea>

            <br><br>
            <button class="btn primary" type="submit">Save Changes</button>
            <a href="manage_complaints.php" class="btn secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
