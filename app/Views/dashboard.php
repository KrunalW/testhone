<?php
// defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="container">
    <h2>Welcome to the Dashboard</h2>
    <p>Hello, <?php echo auth()->user()->username ?? 'Guest'; ?>! This is your dashboard.</p>
</div>