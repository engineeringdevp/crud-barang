<?php
require __DIR__ . '/includes/init.php';
wajib_login();

$daftar = barang_daftar($db);
$ringkasan = hitung_ringkasan($db);

$judul = 'Laporan';
$menu = 'laporan';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Laporan Data Barang</h1>
    <a href="laporan_pdf.php" target="_blank" rel="noopener" class="btn btn-primary">Cetak PDF</a>
</div>

<div class="kartu">
    <?php if (!$daftar): ?>
        <p class="text-muted mb-0">Belum ada data barang untuk dilaporkan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabel align-middle mb-0">
                <thead>
                <tr>
                    <th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th>
                    <th class="kolom-angka">Harga</th><th class="kolom-angka">Stok</th><th class="kolom-angka">Subtotal</th>
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
                        <td class="kolom-angka"><?= rupiah(hitung_subtotal((int) $b['harga'], (int) $b['stok'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr class="fw-bold">
                    <td colspan="5" class="kolom-angka">Total</td>
                    <td class="kolom-angka"><?= angka($ringkasan['total_stok']) ?></td>
                    <td class="kolom-angka"><?= rupiah($ringkasan['total_nilai']) ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
