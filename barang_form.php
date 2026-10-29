<?php
require __DIR__ . '/includes/init.php';
wajib_login();

// Mode edit bila ada parameter id, mode tambah bila tidak ada.
$id = null;
$barang = null;
if (isset($_GET['id'])) {
    $id = id_valid($_GET['id']);
    $barang = $id ? barang_ambil($db, $id) : null;
    if (!$barang) {
        flash_set('gagal', 'Barang tidak ditemukan atau ID tidak valid.');
        redirect('barang.php');
    }
}

$data = $barang
    ? ['kode' => $barang['kode'], 'nama' => $barang['nama'], 'kategori' => $barang['kategori'],
       'harga' => (string) $barang['harga'], 'stok' => (string) $barang['stok']]
    : ['kode' => '', 'nama' => '', 'kategori' => '', 'harga' => '', 'stok' => ''];
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $err['umum'] = 'Sesi formulir kedaluwarsa. Silakan coba lagi.';
    } else {
        [$data, $err] = validasi_barang($_POST);

        if (!isset($err['kode']) && kode_ada($db, $data['kode'], $id)) {
            $err['kode'] = 'Kode sudah dipakai barang lain.';
        }

        if (!$err) {
            try {
                if ($id) {
                    barang_ubah($db, $id, $data);
                    flash_set('sukses', 'Barang berhasil diperbarui.');
                } else {
                    barang_tambah($db, $data);
                    flash_set('sukses', 'Barang berhasil ditambahkan.');
                }
                redirect('barang.php');
            } catch (mysqli_sql_exception $ex) {
                $err['umum'] = $ex->getCode() === 1062
                    ? 'Kode sudah dipakai barang lain.'
                    : 'Data gagal disimpan. Silakan coba lagi.';
            }
        }
    }
}

$judul = $id ? 'Edit Barang' : 'Tambah Barang';
$menu = $id ? 'barang' : 'tambah';
require __DIR__ . '/includes/header.php';
?>
<h1 class="h4 mb-3"><?= e($judul) ?></h1>

<div class="kartu form-sedang">
    <?php if (isset($err['umum'])): ?><div class="alert alert-danger"><?= e($err['umum']) ?></div><?php endif; ?>
    <form method="post" action="barang_form.php<?= $id ? '?id=' . $id : '' ?>" novalidate>
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-12 col-sm-4">
                <label for="kode" class="form-label">Kode</label>
                <input type="text" id="kode" name="kode" maxlength="20" class="form-control<?= kelas_err($err, 'kode') ?>" value="<?= e($data['kode']) ?>" required>
                <?= pesan_err($err, 'kode') ?>
            </div>
            <div class="col-12 col-sm-8">
                <label for="nama" class="form-label">Nama barang</label>
                <input type="text" id="nama" name="nama" maxlength="100" class="form-control<?= kelas_err($err, 'nama') ?>" value="<?= e($data['nama']) ?>" required>
                <?= pesan_err($err, 'nama') ?>
            </div>
            <div class="col-12">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" id="kategori" name="kategori" maxlength="50" class="form-control<?= kelas_err($err, 'kategori') ?>" value="<?= e($data['kategori']) ?>" required>
                <?= pesan_err($err, 'kategori') ?>
            </div>
            <div class="col-12 col-sm-6">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="text" id="harga" name="harga" inputmode="numeric" maxlength="9" class="form-control<?= kelas_err($err, 'harga') ?>" value="<?= e($data['harga']) ?>" required>
                <?= pesan_err($err, 'harga') ?>
            </div>
            <div class="col-12 col-sm-6">
                <label for="stok" class="form-label">Stok</label>
                <input type="text" id="stok" name="stok" inputmode="numeric" maxlength="7" class="form-control<?= kelas_err($err, 'stok') ?>" value="<?= e($data['stok']) ?>" required>
                <?= pesan_err($err, 'stok') ?>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
