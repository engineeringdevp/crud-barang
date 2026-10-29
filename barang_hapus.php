<?php
require __DIR__ . '/includes/init.php';
wajib_login();
wajib_post();

if (!csrf_valid()) {
    flash_set('gagal', 'Sesi formulir kedaluwarsa. Silakan coba lagi.');
    redirect('barang.php');
}

$id = id_valid($_POST['id'] ?? null);
if (!$id) {
    flash_set('gagal', 'ID barang tidak valid.');
    redirect('barang.php');
}

try {
    if (barang_hapus($db, $id)) {
        flash_set('sukses', 'Barang berhasil dihapus.');
    } else {
        flash_set('gagal', 'Barang tidak ditemukan.');
    }
} catch (mysqli_sql_exception $ex) {
    flash_set('gagal', 'Barang gagal dihapus. Silakan coba lagi.');
}
redirect('barang.php');
