<?php
// [SINTAKS PHP]: include | Memanggil koneksi database agar query dapat dieksekusi oleh file ini
include '../../config/koneksi.php';

// [SINTAKS PHP]: $_GET | Menangkap parameter URL untuk memproses logic database spesifik
$id = $_GET['id'];

// [SINTAKS PHP]: String Query SQL | Mengambil relasi data transaksi, kendaraan, tarif, dan area
$query_sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, tr.tarif_per_jam, a.nama_area 
              FROM tb_transaksi t 
              JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
              JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
              JOIN tb_area_parkir a ON t.id_area = a.id_area
              WHERE t.id_parkir = '$id'";

// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
$query = mysqli_query($koneksi, $query_sql);

// [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / mengambil sebaris record array berdasarkan key kolom asosiatif
$d = mysqli_fetch_assoc($query);
?>
<!-- [SINTAKS HTML]: <!DOCTYPE html> | Mendeklarasikan tipe dokumen sebagai HTML5 -->
<!DOCTYPE html>
<!-- [SINTAKS HTML]: <html> | Elemen akar HTML -->
<html>
<head>
    <!-- [SINTAKS HTML]: <title> | Menentukan judul dokumen yang tampil di tab browser -->
    <title>Struk Pembayaran Parkir</title>
    <!-- [SINTAKS CSS]: <style> | Menampung kode CSS Internal untuk memberikan style struk -->
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; color: #333; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .content { margin-top: 15px; font-size: 14px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px; font-weight: bold; font-size: 18px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; border-top: 1px dashed #000; padding-top: 10px; }
        /* [SINTAKS CSS]: @media print | Membuat style spesifik ketika jendela cetak terbuka */
        @media print { .no-print { display: none; } }
    </style>
</head>
<!-- [SINTAKS JAVASCRIPT]: onload="window.print()" | Event trigger yang otomatis meluncurkan opsi print jendela browser -->
<body onload="window.print()">
    <!-- [SINTAKS HTML]: <div> class="header" | Kontainer pembungkus bagian kop struk -->
    <div class="header">
        <h2 style="margin:0">PARLINE</h2>
        <p style="margin:5px 0">Hogwarts Parking System</p>
    </div>

    <!-- [SINTAKS HTML]: <div> class="content" | Kontainer rincian dari nota/struk parkir -->
    <div class="content">
        <!-- [SINTAKS PHP]: echo | Mencetak nilai variabel langsung ke dalam HTML -->
        <div class="row"><span>ID Parkir:</span> <span>#<?php echo $d['id_parkir']; ?></span></div>
        <div class="row"><span>No. Plat:</span> <strong><?php echo $d['plat_nomor']; ?></strong></div>
        <div class="row"><span>Jenis:</span> <span><?php echo $d['jenis_kendaraan']; ?></span></div>
        <div class="row"><span>Area:</span> <span><?php echo $d['nama_area']; ?></span></div>
        <hr style="border: 0; border-top: 1px solid #eee;">
        <!-- [SINTAKS PHP]: date() format + strtotime() | Memodelkan jam masuk/keluar ke bentuk user-friendly -->
        <div class="row"><span>Waktu Masuk:</span> <span><?php echo date('H:i', strtotime($d['waktu_masuk'])); ?></span></div>
        <div class="row"><span>Waktu Keluar:</span> <span><?php echo date('H:i', strtotime($d['waktu_keluar'])); ?></span></div>
        
        <?php 
            // [SINTAKS PHP]: new DateTime() | Membuat objek DateTime untuk mempermudah perhitungan selisih waktu
            $awal = new DateTime($d['waktu_masuk']);
            $akhir = new DateTime($d['waktu_keluar']);
            // [SINTAKS PHP]: diff() | Menghitung selisih matematis antara titik awal vs titik akhir
            $diff = $awal->diff($akhir);
            // [SINTAKS PHP]: Operasi aritmatika | Mendapatkan total jam parkir, inklusif 1 jam pertama
            $durasi = ($diff->days * 24) + $diff->h + ($diff->i > 0 ? 1 : 0);
        ?>
        
        <div class="row"><span>Durasi:</span> <span><?php echo $durasi; ?> Jam</span></div>
        <!-- [SINTAKS PHP]: number_format() | Memberikan separator titik ribuan pada mata uang agar mudah dibaca -->
        <div class="row"><span>Tarif/Jam:</span> <span>Rp <?php echo number_format($d['tarif_per_jam']); ?></span></div>
        
        <div class="row total">
            <span>TOTAL BAYAR:</span>
            <span>Rp <?php echo number_format($d['biaya_total']); ?></span>
        </div>
    </div>

    <!-- [SINTAKS HTML]: <div> class="footer" | Keterangan lunas di bagian bonggol bawah struk -->
    <div class="footer">
        <p>LUNAS<br>Terima kasih atas kunjungannya!</p>
    </div>

    <!-- [SINTAKS HTML]: <div> class="no-print" | Tombol balik ke kasir/petugas page yang disembunyikan saat dicetak -->
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <!-- [SINTAKS HTML]: <a> | Hyperlink / anchor ke modul transaksi keluar -->
        <a href="transaksi_keluar.php" style="text-decoration:none; background:#3b82f6; color:white; padding:8px 15px; border-radius:5px;">Kembali</a>
    </div>
</body>
</html>
