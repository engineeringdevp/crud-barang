<?php
require __DIR__ . '/includes/init.php';
wajib_login();
require __DIR__ . '/lib/fpdf/fpdf.php';

class LaporanPDF extends FPDF
{
    public function Footer(): void
    {
        $this->SetY(-12);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 6, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'C');
    }
}

// FPDF memakai encoding Latin-1, jadi teks UTF-8 dikonversi dulu.
function pdf_teks(string $teks): string
{
    return mb_convert_encoding($teks, 'ISO-8859-1', 'UTF-8');
}

// Potong teks agar muat di dalam sel.
function pdf_muat(FPDF $pdf, string $teks, float $lebar): string
{
    $teks = pdf_teks($teks);
    if ($pdf->GetStringWidth($teks) <= $lebar - 2) {
        return $teks;
    }
    while ($teks !== '' && $pdf->GetStringWidth($teks . '...') > $lebar - 2) {
        $teks = substr($teks, 0, -1);
    }
    return $teks . '...';
}

$daftar = barang_daftar($db);
$ringkasan = hitung_ringkasan($db);

$kolom = [['No', 10, 'C'], ['Kode', 22, 'L'], ['Nama', 46, 'L'], ['Kategori', 30, 'L'],
          ['Harga', 28, 'R'], ['Stok', 18, 'R'], ['Subtotal', 36, 'R']];

$pdf = new LaporanPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetTitle('Laporan Data Barang');
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'Laporan Data Barang', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Dicetak: ' . date('d-m-Y H:i'), 0, 1);
$pdf->Ln(2);
$pdf->Cell(0, 5, 'Jumlah barang: ' . angka($ringkasan['jumlah'])
    . '   |   Total stok: ' . angka($ringkasan['total_stok'])
    . '   |   Total nilai: ' . rupiah($ringkasan['total_nilai']), 0, 1);
$pdf->Ln(3);

$kepala = function () use ($pdf, $kolom): void {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(31, 78, 121);
    $pdf->SetTextColor(255, 255, 255);
    foreach ($kolom as [$label, $lebar, $rata]) {
        $pdf->Cell($lebar, 7, $label, 1, 0, 'C', true);
    }
    $pdf->Ln();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);
};
$kepala();

if (!$daftar) {
    $pdf->Cell(array_sum(array_column($kolom, 1)), 8, 'Belum ada data barang.', 1, 1, 'C');
}

foreach ($daftar as $i => $b) {
    if ($pdf->GetY() > 272) {
        $pdf->AddPage();
        $kepala();
    }
    $harga = (int) $b['harga'];
    $stok = (int) $b['stok'];
    $isi = [
        (string) ($i + 1), $b['kode'], $b['nama'], $b['kategori'],
        angka($harga), angka($stok), angka(hitung_subtotal($harga, $stok)),
    ];
    foreach ($kolom as $k => [$label, $lebar, $rata]) {
        $pdf->Cell($lebar, 6.5, pdf_muat($pdf, $isi[$k], $lebar), 1, 0, $rata);
    }
    $pdf->Ln();
}

if ($daftar) {
    if ($pdf->GetY() > 272) {
        $pdf->AddPage();
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10 + 22 + 46 + 30 + 28, 7, 'Total', 1, 0, 'R');
    $pdf->Cell(18, 7, angka($ringkasan['total_stok']), 1, 0, 'R');
    $pdf->Cell(36, 7, angka($ringkasan['total_nilai']), 1, 1, 'R');
}

$pdf->Output('I', 'laporan-barang.pdf');
