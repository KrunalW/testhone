<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Admin Panel</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            padding: 0;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-left-color: #0d6efd;
        }
        .sidebar .nav-link.active {
            background-color: #e7f1ff;
            color: #0d6efd;
            border-left-color: #0d6efd;
            font-weight: 500;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-graduation-cap"></i> Mock Test Platform - Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= auth()->user()->username ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <nav class="nav flex-column">
            <?php if (auth()->user()->can('subjects.manage')): ?>
                <a class="nav-link <?= url_is('admin/subjects*') ? 'active' : '' ?>" href="/admin/subjects">
                    <i class="fas fa-book"></i> Subjects
                </a>
            <?php endif; ?>

            <?php if (auth()->user()->can('questions.manage')): ?>
                <a class="nav-link <?= url_is('admin/questions*') ? 'active' : '' ?>" href="/admin/questions">
                    <i class="fas fa-question-circle"></i> Questions
                </a>
            <?php endif; ?>

            <?php if (auth()->user()->can('exams.create') || auth()->user()->can('exams.schedule')): ?>
                <a class="nav-link <?= url_is('admin/exams*') ? 'active' : '' ?>" href="/admin/exams">
                    <i class="fas fa-clipboard-list"></i> Exams
                </a>
            <?php endif; ?>

            <?php if (auth()->user()->can('admin.access')): ?>
                <hr class="my-2">
                <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
                <a class="nav-link <?= url_is('admin/reports*') ? 'active' : '' ?>" href="/admin/reports">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-top: 56px;">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
