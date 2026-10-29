<?php
require __DIR__ . '/includes/init.php';
wajib_login();

$cari = bersih($_GET['q'] ?? '');
$daftar = barang_daftar($db, $cari);

$judul = 'Data Barang';
$menu = 'barang';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Data Barang</h1>
    <a href="barang_form.php" class="btn btn-primary">Tambah Barang</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-12 col-sm">
        <label for="q" class="visually-hidden">Cari barang</label>
        <input type="search" id="q" name="q" class="form-control" maxlength="100"
               placeholder="Cari kode, nama, atau kategori" value="<?= e($cari) ?>">
    </div>
    <div class="col-auto"><button type="submit" class="btn btn-outline-primary">Cari</button></div>
    <?php if ($cari !== ''): ?>
        <div class="col-auto"><a href="barang.php" class="btn btn-outline-secondary">Reset</a></div>
    <?php endif; ?>
</form>

<div class="kartu">
    <?php if (!$daftar): ?>
        <p class="text-muted mb-0">
            <?= $cari !== '' ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Belum ada data barang.' ?>
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabel table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th>
                    <th class="kolom-angka">Harga</th><th class="kolom-angka">Stok</th><th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($daftar as $i => $b): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($b['kode']) ?></td>
                        <td><?= e($b['nama']) ?></td>
                        <td><?= e($b['kategori']) ?></td>
                        <td class="kolom-angka"><?= rupiah((int) $b['harga']) ?></td>
                        <td class="kolom-angka"><?= angka((int) $b['stok']) ?></td>
                        <td class="text-nowrap">
                            <a href="barang_form.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-hapus
                                    data-id="<?= (int) $b['id'] ?>" data-nama="<?= e($b['nama']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalHapus">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="small text-muted mt-3 mb-0"><?= count($daftar) ?> barang ditampilkan.</p>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulHapus" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="barang_hapus.php" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="hapusId" value="">
            <div class="modal-header">
                <h2 class="modal-title h5" id="judulHapus">Konfirmasi hapus</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Hapus barang <strong id="hapusNama"></strong>? Data yang dihapus tidak dapat dikembalikan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
