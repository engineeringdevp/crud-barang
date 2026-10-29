<?php
require __DIR__ . '/includes/init.php';
wajib_tamu();

$data = ['nama' => '', 'username' => ''];
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $err['umum'] = 'Sesi formulir kedaluwarsa. Silakan coba lagi.';
    } else {
        [$data, $password, $err] = validasi_registrasi($_POST);

        if (!$err && user_cari($db, $data['username'])) {
            $err['username'] = 'Username sudah dipakai.';
        }

        if (!$err) {
            try {
                user_buat($db, $data['nama'], $data['username'], $password);
                flash_set('sukses', 'Registrasi berhasil. Silakan masuk.');
                redirect('login.php');
            } catch (mysqli_sql_exception $ex) {
                $err['username'] = $ex->getCode() === 1062
                    ? 'Username sudah dipakai.'
                    : 'Registrasi gagal. Silakan coba lagi.';
            }
        }
    }
}

$judul = 'Registrasi';
$menu = 'registrasi';
require __DIR__ . '/includes/header.php';
?>
<div class="form-sempit kartu">
    <h1 class="h4 mb-3">Buat Akun</h1>
    <?php if (isset($err['umum'])): ?><div class="alert alert-danger"><?= e($err['umum']) ?></div><?php endif; ?>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama lengkap</label>
            <input type="text" id="nama" name="nama" maxlength="100" class="form-control<?= kelas_err($err, 'nama') ?>" value="<?= e($data['nama']) ?>" required>
            <?= pesan_err($err, 'nama') ?>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" maxlength="30" autocomplete="username" class="form-control<?= kelas_err($err, 'username') ?>" value="<?= e($data['username']) ?>" required>
            <?= pesan_err($err, 'username') ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" class="form-control<?= kelas_err($err, 'password') ?>" required>
            <?= pesan_err($err, 'password') ?>
        </div>
        <div class="mb-3">
            <label for="konfirmasi" class="form-label">Ulangi password</label>
            <input type="password" id="konfirmasi" name="konfirmasi" autocomplete="new-password" class="form-control<?= kelas_err($err, 'konfirmasi') ?>" required>
            <?= pesan_err($err, 'konfirmasi') ?>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>
    <p class="small text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Masuk</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
