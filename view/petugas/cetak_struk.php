<?php
// [SINTAKS PHP]: include | Memanggil file konfigurasi koneksi ke server MySQL Database
include '../../config/koneksi.php';

$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

// QUERY BARU: Menggabungkan transaksi, kendaraan, dan user
$query = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap as petugas 
          FROM tb_transaksi t 
          JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          JOIN tb_user u ON t.id_user = u.id_user
          WHERE t.id_parkir = '$id'"; 

$data = mysqli_query($koneksi, $query);
$r = mysqli_fetch_assoc($data);

if (!$r) {
    die("Error: Data parkir dengan ID $id tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Masuk Parkir</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; color: #000; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 15px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 24px; font-weight: bolder; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; font-size: 13px; font-weight: bold; }
        .content { font-size: 14px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: bold; }
        .barcode { text-align: center; margin: 25px 0; font-size: 28px; font-weight: bold; letter-spacing: 3px; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 15px 0; }
        .footer { text-align: center; margin-top: 25px; font-size: 11px; border-top: 2px dashed #000; padding-top: 15px; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>PARLINE</h2>
        <p>Hogwarts Parking System</p>
    </div>

    <div class="content">
        <div class="row"><span>KODE TIKET:</span> <span>#<?php echo str_pad($r['id_parkir'], 5, '0', STR_PAD_LEFT); ?></span></div>
        <div class="row"><span>NO. PLAT:</span> <span style="font-size: 16px;"><?php echo strtoupper($r['plat_nomor']); ?></span></div>
        <div class="row"><span>JENIS:</span> <span><?php echo strtoupper($r['jenis_kendaraan']); ?></span></div>
        <div class="row"><span>PETUGAS:</span> <span><?php echo strtoupper(substr($r['petugas'], 0, 15)); ?></span></div>
        <div class="row" style="margin-top: 15px;"><span>WAKTU MASUK:</span></div>
        <div class="row" style="justify-content: center; font-size: 16px; margin-top: 5px;">
            <span><?php echo date('d M Y - H:i', strtotime($r['waktu_masuk'])); ?> WIB</span>
        </div>
        
        <div class="barcode">
            *<?php echo $r['id_parkir'] . rand(100,999); ?>*
        </div>
        
        <div style="text-align: center; font-size: 12px; font-weight: bold;">
            Tanda masuk ini adalah<br>bukti sah parkir kendaraan.<br>Jangan sampai hilang.
        </div>
    </div>

    <div class="footer">
        <p>SIMPAN KARCIS DENGAN AMAN.<br>KEHILANGAN KARCIS DIKENAKAN DENDA.</p>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <a href="transaksi_masuk.php" style="text-decoration:none; background:#2563eb; color:white; padding:12px 25px; border-radius:10px; font-family: sans-serif; font-weight: bold;">Kembali Ke Beranda</a>
    </div>
</body>
</html>
