<?php
require __DIR__ . '/includes/init.php';
wajib_tamu();

$username = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(bersih($_POST['username'] ?? ''));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (!csrf_valid()) {
        $pesan = 'Sesi formulir kedaluwarsa. Silakan coba lagi.';
    } elseif ($username === '' || $password === '') {
        $pesan = 'Username dan password wajib diisi.';
    } else {
        $user = user_cari($db, $username);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            redirect('dashboard.php');
        }
        $pesan = 'Username atau password salah.';
    }
}

$judul = 'Masuk';
$menu = 'login';
require __DIR__ . '/includes/header.php';
?>
<div class="form-sempit kartu">
    <h1 class="h4 mb-3">Masuk</h1>
    <?php if ($pesan !== ''): ?><div class="alert alert-danger"><?= e($pesan) ?></div><?php endif; ?>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" maxlength="30" autocomplete="username" class="form-control" value="<?= e($username) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>
    <p class="small text-center mt-3 mb-0">Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
