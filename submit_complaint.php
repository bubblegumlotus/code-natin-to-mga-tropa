<?php
require_once 'config.php';
requireLogin();

if (isAdmin()) {
    redirect("manage_complaints.php");
}

$pageTitle = "Submit Complaint";
require 'header.php';
?>

<div id="complaints-app"
     data-complaints-page="submit-complaint"
     data-user-id="<?= e($_SESSION['user_id']) ?>"
     data-user-name="<?= e($_SESSION['full_name']) ?>"
     data-is-admin="false">
    <div id="complaints-error" class="alert error" role="alert" hidden></div>

    <div class="content-narrow panel">
        <h3>Submit a New Complaint</h3>

        <form id="submit-complaint-form" class="form-grid">
            <div>
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option>Billing</option>
                    <option>Water Quality</option>
                    <option>Service Delay</option>
                    <option>Leakage</option>
                    <option>Low Water Pressure</option>
                    <option>Other</option>
                </select>
            </div>

            <div>
                <label for="subject">Subject</label>
                <input id="subject" type="text" name="subject" required>
            </div>

            <div class="span-2">
                <label for="address">Address / Location</label>
                <input id="address" type="text" name="address" required>
            </div>

            <div class="span-2">
                <label for="description">Complaint Description</label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>

            <div class="span-2">
                <button class="btn primary" type="submit">Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
