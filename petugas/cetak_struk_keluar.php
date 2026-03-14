<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT t.*, tr.tarif_per_jam, a.nama_area 
                                 FROM tb_transaksi t 
                                 JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
                                 JOIN tb_area a ON t.id_area = a.id_area
                                 WHERE t.id_transaksi = '$id'");
$d = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran Parkir</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; color: #333; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .content { margin-top: 15px; font-size: 14px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px; font-weight: bold; font-size: 18px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; border-top: 1px dashed #000; padding-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0">PARLINE</h2>
        <p style="margin:5px 0">Hogwarts Parking System</p>
    </div>

    <div class="content">
        <div class="row"><span>ID Transaksi:</span> <span>#<?php echo $d['id_transaksi']; ?></span></div>
        <div class="row"><span>No. Plat:</span> <strong><?php echo $d['plat_nomor']; ?></strong></div>
        <div class="row"><span>Jenis:</span> <span><?php echo $d['jenis_kendaraan']; ?></span></div>
        <div class="row"><span>Area:</span> <span><?php echo $d['nama_area']; ?></span></div>
        <hr style="border: 0; border-top: 1px solid #eee;">
        <div class="row"><span>Waktu Masuk:</span> <span><?php echo date('H:i', strtotime($d['waktu_masuk'])); ?></span></div>
        <div class="row"><span>Waktu Keluar:</span> <span><?php echo date('H:i', strtotime($d['waktu_keluar'])); ?></span></div>
        
        <?php 
            $awal = new DateTime($d['waktu_masuk']);
            $akhir = new DateTime($d['waktu_keluar']);
            $diff = $awal->diff($akhir);
            $durasi = ($diff->days * 24) + $diff->h + ($diff->i > 0 ? 1 : 0);
        ?>
        
        <div class="row"><span>Durasi:</span> <span><?php echo $durasi; ?> Jam</span></div>
        <div class="row"><span>Tarif/Jam:</span> <span>Rp <?php echo number_format($d['tarif_per_jam']); ?></span></div>
        
        <div class="row total">
            <span>TOTAL BAYAR:</span>
            <span>Rp <?php echo number_format($d['biaya_total']); ?></span>
        </div>
    </div>

    <div class="footer">
        <p>LUNAS<br>Terima kasih atas kunjungannya!</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <a href="transaksi_keluar.php" style="text-decoration:none; background:#3b82f6; color:white; padding:8px 15px; border-radius:5px;">Kembali</a>
    </div>
</body>
</html>