<?php
$base = '/hassan';

$pageTitle = $pageTitle ?? 'Dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            overflow-y: auto;
        }

        .sidebar .logo {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            color: #fff;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            height: 100vh;
            overflow-y: auto;
        }

        .topbar {
            height: 60px;
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .dropdown-toggle::after {
            margin-left: 0.5em;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">MyDashboard</div>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <!-- Admin Sidebar -->
            <a href="<?= $base ?>/admin/dashboard">🏠 Dashboard</a>

            <div class="accordion" id="menuAccordion">
                <div class="accordion-item bg-dark border-0">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed bg-dark text-white" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            🗺️ Destination Pages
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                        <div class="accordion-body bg-dark p-0">
                            <a href="<?= $base ?>/admin/destinations/create" class="ps-4 d-block">Create</a>
                            <a href="<?= $base ?>/admin/destinations/manage" class="ps-4 d-block">Manage</a>
                        </div>
                    </div>
                </div>

                <a href="<?= $base ?>/admin/bookings/manage">📅 Bookings</a>
                <a href="<?= $base ?>/admin/messages">💬 Messages</a>
                <a href="<?= $base ?>/admin/users/manage">👥 Users</a>

                <div class="accordion-item bg-dark border-0">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-dark text-white" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                            ⚙️ Settings
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                        <div class="accordion-body bg-dark p-0">
                            <a href="<?= $base ?>/admin/setting/profile" class="ps-4 d-block">Profile</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- User Sidebar -->
            <a href="<?= $base ?>/user/dashboard">🏠 Dashboard</a>
            <a href="<?= $base ?>/user/bookings/manage">📅 My Bookings</a>
            <a href="<?= $base ?>/user/setting/profile">📅 Profile</a>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main class="main">
        <!-- Topbar -->
        <div class="topbar">
            <div><strong><?= $pageTitle ?></strong></div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    👤 <?= $_SESSION['user']['name'] ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= $_SESSION['user']['role'] === 'admin' ? $base . '/admin/setting/profile' : $base . '/user/setting/profile' ?>">Settings</a></li>
                    <li><a class="dropdown-item" href="<?php echo $base; ?>/logout">Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        <div class="mt-4">
            <?php require_once $contentFile; ?>
        </div>
    </main>

</body>

</html>