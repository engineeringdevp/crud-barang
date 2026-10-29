<?php
require __DIR__ . '/includes/init.php';
wajib_login();

$ringkasan = hitung_ringkasan($db);
$terbaru = barang_terbaru($db, 5);

$judul = 'Dashboard';
$menu = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<h1 class="h4 mb-3">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="kartu">
            <div class="statistik-label">Jumlah barang</div>
            <div class="statistik-nilai"><?= angka($ringkasan['jumlah']) ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kartu">
            <div class="statistik-label">Total stok</div>
            <div class="statistik-nilai"><?= angka($ringkasan['total_stok']) ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kartu">
            <div class="statistik-label">Total nilai</div>
            <div class="statistik-nilai"><?= rupiah($ringkasan['total_nilai']) ?></div>
        </div>
    </div>
</div>

<div class="kartu">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Barang terbaru</h2>
        <a href="barang.php" class="small">Lihat semua</a>
    </div>
    <?php if (!$terbaru): ?>
        <p class="text-muted mb-0">Belum ada data barang. <a href="barang_form.php">Tambah barang pertama</a>.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabel align-middle mb-0">
                <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th class="kolom-angka">Stok</th></tr></thead>
                <tbody>
                <?php foreach ($terbaru as $b): ?>
                    <tr>
                        <td><?= e($b['kode']) ?></td>
                        <td><?= e($b['nama']) ?></td>
                        <td><?= e($b['kategori']) ?></td>
                        <td class="kolom-angka"><?= angka((int) $b['stok']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
