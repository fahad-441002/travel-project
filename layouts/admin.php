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

        .wrapper {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #212529;
            color: white;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 10000;
        }

        .sidebar .logo {
            text-align: center;
            font-size: 1.7rem;
            font-weight: 600;
            margin: 20px 0;
            color: #ffffff;
        }

        .sidebar a,
        .accordion-button {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            font-size: 15px;
        }

        .sidebar a:hover,
        .accordion-button:hover {
            background-color: #343a40;
            color: #ffffff;
        }

        .accordion-button {
            background-color: transparent;
            border: none;
            box-shadow: none;
        }

        .accordion-button:not(.collapsed)::after {
            transform: rotate(90deg);
        }

        .accordion-button::after {
            margin-left: auto;
            transition: transform 0.2s;
            filter: invert(1);
        }

        .accordion-body a {
            padding-left: 40px;
            font-size: 14px;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            flex-grow: 1;
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
            z-index: 1001;
        }

        #sidebarToggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            #sidebarToggle {
                display: inline-block;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo">🌍 MyDashboard</div>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="<?= $base ?>/admin/dashboard">🏠 Dashboard</a>

                <div class="accordion accordion-flush" id="adminMenu">
                    <div class="accordion-item bg-dark border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#destinationMenu">
                                🗺️ Destination Pages
                            </button>
                        </h2>
                        <div id="destinationMenu" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
                            <div class="accordion-body p-0">
                                <a href="<?= $base ?>/admin/destinations/create">➕ Create</a>
                                <a href="<?= $base ?>/admin/destinations/manage">📋 Manage</a>
                                <a href="<?= $base ?>/admin/destinations/highlights">🎬 Highlights</a>
                            </div>
                        </div>
                    </div>

                    <a href="<?= $base ?>/admin/bookings/manage">📅 Bookings</a>
                    <a href="<?= $base ?>/admin/messages">💬 Messages</a>
                    <a href="<?= $base ?>/admin/users/manage">👥 Users</a>

                    <div class="accordion-item bg-dark border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#settingsMenu">
                                ⚙️ Settings
                            </button>
                        </h2>
                        <div id="settingsMenu" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
                            <div class="accordion-body p-0">
                                <a href="<?= $base ?>/admin/setting/profile">👤 Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <a href="<?= $base ?>/user/dashboard">🏠 Dashboard</a>
                <a href="<?= $base ?>/user/bookings/manage">📅 My Bookings</a>
                <a href="<?= $base ?>/user/setting/profile">👤 Profile</a>
            <?php endif; ?>
        </div>

        <!-- Main Content -->
        <main class="main w-100">
            <div class="topbar">
                <button id="sidebarToggle" class="btn text-dark">&#9776;</button>
                <div><strong><?= $pageTitle ?></strong></div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        👤 <?= $_SESSION['user']['name'] ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item"
                                href="<?= $_SESSION['user']['role'] === 'admin' ? $base . '/admin/setting/profile' : $base . '/user/setting/profile' ?>">Settings</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base ?>/logout">Logout</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-4">
                <?php
                if (isset($_GET['status'])) {
                    if ($_GET['status'] === 'added') {
                        echo "<div class='alert alert-success'>Highlight added successfully!</div>";
                    } elseif ($_GET['status'] === 'updated') {
                        echo "<div class='alert alert-success'>Highlight updated successfully!</div>";
                    } elseif ($_GET['status'] === 'deleted') {
                        echo "<div class='alert alert-success'>Highlight deleted successfully!</div>";
                    }
                }

                require_once $contentFile;
                ?>
            </div>
        </main>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>

</body>

</html>