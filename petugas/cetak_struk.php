<?php
include '../config/koneksi.php';

// Ambil ID dan pastikan datanya ada
$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

$query = "SELECT * FROM tb_transaksi WHERE id_transaksi = '$id'";
$data = mysqli_query($koneksi, $query);
$r = mysqli_fetch_assoc($data);

// Jika ID tidak ditemukan, tampilkan pesan error, jangan biarkan layar putih
if (!$r) {
    die("Error: Data transaksi dengan ID $id tidak ditemukan di sistem.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Karcis - Hogwarts</title>
    <style>
        body { font-family: monospace; width: 280px; padding: 10px; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .plat { font-size: 24px; font-weight: bold; text-align: center; border: 2px solid #000; margin: 10px 0; padding: 5px; }
        table { width: 100%; font-size: 14px; }
        .footer { text-align: center; margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px; font-size: 11px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <h2 style="margin:0;">HOGWARTS</h2>
        <small>Magic Parking System</small>
    </div>

    <div class="plat"><?= $r['plat_nomor'] ?></div>

    <table>
        <tr><td>Jenis</td><td>: <?= $r['jenis_kendaraan'] ?></td></tr>
        <tr><td>Masuk</td><td>: <?= date('d/m/Y H:i', strtotime($r['waktu_masuk'])) ?></td></tr>
        <tr><td>Petugas</td><td>: <?= $r['petugas'] ?></td></tr>
    </table>

    <div class="footer">
        Jangan sampai hilang!<br>Denda: Rp 50.000
        <div class="no-print" style="margin-top:20px;">
            <button onclick="location.href='transaksi_masuk.php'">KEMBALI</button>
        </div>
    </div>
</body>
</html>