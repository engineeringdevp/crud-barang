<?php
require __DIR__ . '/includes/init.php';
redirect(sudah_login() ? 'dashboard.php' : 'login.php');
