<?php
require_once __DIR__ . '/config.php';

requireLogin();

$pageTitle = $pageTitle ?? "Dashboard";

$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?> | GoComplain</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<div class="app-shell">

    <aside class="sidebar">

        <a href="dashboard.php" class="brand">
            <span class="brand-mark">G</span>
            GoComplain
        </a>

        <p class="side-label">MAIN MENU</p>

        <nav>

            <a href="dashboard.php">▦ Dashboard</a>

            <?php if (isAdmin()): ?>

                <a href="manage_complaints.php">
                    ▤ Manage Complaints
                </a>

                <a href="users.php">
                    ♙ User Management
                </a>

                <a href="reports.php">
                    ▥ Reports & Analytics
                </a>

            <?php else: ?>

                <a href="submit_complaint.php">
                    ＋ Submit Complaint
                </a>

                <a href="my_complaints.php">
                    ▤ My Complaints
                </a>

                <a href="track_complaints.php">
                    ⌕ Complaints Tracking
                </a>

            <?php endif; ?>

        </nav>

        <div class="side-bottom">

            <div class="profile-mini">

                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
                </div>

                <div>
                    <b><?= e($_SESSION['full_name']) ?></b>

                    <small>
                        <?= ucfirst(e($_SESSION['role'])) ?>
                    </small>
                </div>

            </div>

            <a href="logout.php" class="logout">
                ↪ Logout
            </a>

        </div>

    </aside>

    <main class="main-area">

        <header class="topbar">

            <div>
                <span class="eyebrow">
                    GOCOMPLAIN SERVICE PORTAL
                </span>

                <h1><?= e($pageTitle) ?></h1>
            </div>

            <div class="top-user">
                <span class="avatar">
                    <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
                </span>

                <div>
                    <b><?= e($_SESSION['full_name']) ?></b>

                    <small>
                        <?= ucfirst(e($_SESSION['role'])) ?>
                    </small>
                </div>
            </div>

        </header>

        <?php if ($flashMessage): ?>

            <div class="alert <?= e($flashMessage['type']) ?>">
                <?= e($flashMessage['message']) ?>
            </div>

        <?php endif; ?>