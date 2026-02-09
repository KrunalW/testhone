<?php
// defined('BASEPATH') or exit('No direct script access allowed ');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CI4 with Shield</title>
    <!-- Bootstrap CSS for basic styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        .wrapper {
            display: flex;
            flex: 1;
        }

        .sidebar {
            width: 250px;
            background: #b1b1b1;
            padding: 20px;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .footer {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
        }

        .bg-light {
            background-color: rgb(150 196 243) !important;
        }

        .nav-link {
            color: #000000;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php echo view('partials/header'); ?>

    <div class="wrapper">
        <!-- Sidebar -->
        <?php echo view('partials/sidebar'); ?>

        <!-- Main Content -->
        <div class="content zzzzzzzzzzz">
            <?php echo view('dashboard'); ?>
        </div>
    </div>

    <!-- Footer -->
    <?php echo view('partials/footer'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>