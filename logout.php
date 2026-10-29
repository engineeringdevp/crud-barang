<?php
require __DIR__ . '/includes/init.php';
wajib_post();

if (csrf_valid()) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    session_start();
    flash_set('sukses', 'Anda sudah keluar.');
    redirect('login.php');
}
redirect('dashboard.php');
