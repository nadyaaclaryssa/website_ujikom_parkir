<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/owner/export_excel.php
// -> Tujuan Spesifik: Modul Ekspor data laporan transaksi parkir ke format file Microsoft Excel (.xls).
//    File ini akan menghasilkan download file Excel yang rapi dan terformat profesional
//    berisi seluruh data transaksi kendaraan yang sudah Check-Out (status = 'keluar').
// ======================================

// [SINTAKS PHP]: session_start() | Memulai sesi autentikasi untuk memastikan hanya Owner yang bisa mengekspor
session_start();

// [SINTAKS PHP]: Validasi Role | Hanya role 'owner' yang diizinkan mengakses fitur ekspor ini
if($_SESSION['role'] != "owner") { 
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: include koneksi | Menyambungkan ke database MySQL
include '../../config/koneksi.php';

// [SINTAKS PHP]: Ambil parameter filter tanggal dari GET (jika ada)
$tanggal_awal  = isset($_GET['dari']) ? $_GET['dari'] : '';
$tanggal_akhir = isset($_GET['sampai']) ? $_GET['sampai'] : '';

// [SINTAKS PHP]: Bangun Query SQL dengan kondisi filter tanggal opsional
$sql = "SELECT t.id_parkir, k.plat_nomor, k.jenis_kendaraan, k.warna, 
               a.nama_area, t.waktu_masuk, t.waktu_keluar, t.durasi_jam, t.biaya_total, 
               u.nama_lengkap as petugas
        FROM tb_transaksi t 
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
        JOIN tb_area_parkir a ON t.id_area = a.id_area
        LEFT JOIN tb_user u ON t.id_user = u.id_user  
        WHERE t.status='keluar'";

// [SINTAKS PHP]: Jika user memfilter rentang tanggal, tambahkan kondisi WHERE
if(!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $tanggal_awal  = mysqli_real_escape_string($koneksi, $tanggal_awal);
    $tanggal_akhir = mysqli_real_escape_string($koneksi, $tanggal_akhir);
    $sql .= " AND DATE(t.waktu_keluar) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

$sql .= " ORDER BY t.waktu_keluar DESC";
$query = mysqli_query($koneksi, $sql);

// [SINTAKS PHP]: Hitung grand total pendapatan dari data yang difilter
$total_pendapatan = 0;

// [SINTAKS PHP]: Nama file Excel dinamis berdasarkan tanggal export
$nama_file = "Laporan_Parkir_PARLINE_" . date('Y-m-d_His') . ".xls";

// [SINTAKS PHP]: Header HTTP | Mengatur response browser agar menganggap output bukan halaman web,
// melainkan file Excel yang harus di-download (bukan ditampilkan)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:x="urn:schemas-microsoft-com:office:spreadsheet" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Laporan Parkir</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        /* [SINTAKS CSS]: Styling tabel agar tampil rapi di dalam file Excel */
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333333; padding: 8px 12px; text-align: left; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        th { background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; }
        .header-title { font-size: 16pt; font-weight: bold; font-family: Calibri, Arial, sans-serif; }
        .header-sub { font-size: 10pt; color: #555555; font-family: Calibri, Arial, sans-serif; }
        .money { text-align: right; font-weight: bold; }
        .center { text-align: center; }
        .total-row td { background-color: #dcfce7; font-weight: bold; font-size: 12pt; }
        .stripe { background-color: #f8fafc; }
    </style>
</head>
<body>

<!-- Baris Judul Dokumen Excel -->
<table>
    <tr>
        <td colspan="10" class="header-title" style="text-align:center; border:none;">
            LAPORAN TRANSAKSI PARKIR - PARLINE
        </td>
    </tr>
    <tr>
        <td colspan="10" class="header-sub" style="text-align:center; border:none;">
            Sistem Manajemen Parkir Cerdas & Terintegrasi
        </td>
    </tr>
    <tr>
        <td colspan="10" class="header-sub" style="text-align:center; border:none;">
            Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB
            <?php if(!empty($tanggal_awal) && !empty($tanggal_akhir)): ?>
                | Filter: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> s/d <?= date('d/m/Y', strtotime($tanggal_akhir)) ?>
            <?php endif; ?>
        </td>
    </tr>
    <!-- Baris kosong sebagai pemisah -->
    <tr><td colspan="10" style="border:none;">&nbsp;</td></tr>
</table>

<!-- Tabel Data Utama -->
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ID Parkir</th>
            <th>Plat Nomor</th>
            <th>Jenis Kendaraan</th>
            <th>Warna</th>
            <th>Area Parkir</th>
            <th>Waktu Masuk</th>
            <th>Waktu Keluar</th>
            <th>Durasi (Jam)</th>
            <th>Total Bayar (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($data = mysqli_fetch_assoc($query)) { 
            $total_pendapatan += $data['biaya_total'];
            $stripe_class = ($no % 2 == 0) ? 'stripe' : '';
        ?>
        <tr class="<?= $stripe_class ?>">
            <td class="center"><?= $no++ ?></td>
            <td class="center">PKR-<?= str_pad($data['id_parkir'], 4, '0', STR_PAD_LEFT) ?></td>
            <td style="font-weight:bold;"><?= strtoupper($data['plat_nomor']) ?></td>
            <td><?= ucfirst($data['jenis_kendaraan']) ?></td>
            <td><?= ucfirst($data['warna'] ?? '-') ?></td>
            <td><?= $data['nama_area'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($data['waktu_masuk'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($data['waktu_keluar'])) ?></td>
            <td class="center"><?= $data['durasi_jam'] ?? '-' ?></td>
            <td class="money">Rp <?= number_format($data['biaya_total'], 0, ',', '.') ?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <!-- Baris Grand Total -->
        <tr class="total-row">
            <td colspan="9" style="text-align:right; padding-right:15px;">GRAND TOTAL PENDAPATAN:</td>
            <td class="money" style="font-size:13pt;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td colspan="10" style="text-align:center; font-size:9pt; color:#888; border:none; padding-top:15px;">
                Dokumen ini digenerate secara otomatis oleh Sistem PARLINE &mdash; <?= date('Y') ?>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>
