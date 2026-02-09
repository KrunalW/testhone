<?php
// defined('BASEPATH') or exit('No direct script access allowed');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo base_url('/dashboard'); ?>">CI4 Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('/profile'); ?>">
                        <?php echo auth()->user()->username ?? 'Guest'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('logout'); ?>">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>