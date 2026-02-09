<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Mock Test Platform</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            height: calc(100vh - 56px);
            width: var(--sidebar-width);
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - 56px);
            padding: 1rem;
        }

        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-mortarboard-fill"></i> Mock Test Platform
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= esc(auth()->user()->username) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link <?= url_is('/dashboard') ? 'active' : '' ?>" href="/dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link <?= url_is('profile') ? 'active' : '' ?>" href="/profile">
                <i class="bi bi-person"></i> Profile
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-top: 56px;">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>