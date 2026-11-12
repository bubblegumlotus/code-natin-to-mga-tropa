<?php
require_once 'config.php';
requireLogin();

if (isAdmin()) {
    redirect("manage_complaints.php");
}

$pageTitle = "Complaints Tracking";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="track-complaint"
     data-complaint-id="<?= e($_GET['id'] ?? '') ?>"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-is-admin="false">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="content-narrow panel">
        <h3>Track Your Complaint</h3>

        <form id="track-complaint-form" class="inline-form">
            <input type="text" name="complaint_code"
                   placeholder="Enter Complaint ID" required>
            <button class="btn primary" type="submit">Track</button>
        </form>

        <div id="tracking-result"></div>
    </div>
</div>

<?php require 'footer.php'; ?>
