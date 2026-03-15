<?php
// [SINTAKS PHP]: include | Memanggil file konfigurasi koneksi ke server MySQL Database
include '../../config/koneksi.php';

// [SINTAKS PHP]: isset($_GET[]) | Mengecek apakah parameter URL 'id' tersedia. Jika ada, amankan dengan mysqli_real_escape_string
$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

// [SINTAKS PHP]: String Query SQL | Mengambil relasi data transaksi, kendaraan, dan operator/petugas
$query = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap as petugas 
          FROM tb_transaksi t 
          JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
          JOIN tb_user u ON t.id_user = u.id_user
          WHERE t.id_parkir = '$id'"; 

// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi sintaks SQL ke dalam koneksi database aktif
$data = mysqli_query($koneksi, $query);
// [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / mengambil record data menjadi array asosiatif
$r = mysqli_fetch_assoc($data);

// [SINTAKS PHP]: if statement | Mengecek jika data tidak ada, eksekusi die() untuk mematikan proses render PHP
if (!$r) {
    die("Error: Data parkir dengan ID $id tidak ditemukan.");
}
?>
<!-- [SINTAKS HTML]: <!DOCTYPE html> | Mendeklarasikan tipe dokumen sebagai HTML5 -->
<!DOCTYPE html>
<!-- [SINTAKS HTML]: <html lang="id"> | Elemen akar HTML dengan atribut bahasa Indonesia -->
<html lang="id">
<head>
    <!-- [SINTAKS HTML]: <meta charset="UTF-8"> | Mendefinisikan karakter encoding UTF-8 -->
    <meta charset="UTF-8">
    <!-- [SINTAKS HTML]: <title> | Menentukan judul dokumen yang tampil di tab browser -->
    <title>Tanda Masuk Parkir</title>
    <!-- [SINTAKS CSS]: <style> | Menampung kode CSS Internal untuk memberikan style struk -->
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; color: #000; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 15px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 24px; font-weight: bolder; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; font-size: 13px; font-weight: bold; }
        .content { font-size: 14px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: bold; }
        .barcode { text-align: center; margin: 25px 0; font-size: 28px; font-weight: bold; letter-spacing: 3px; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 15px 0; }
        .footer { text-align: center; margin-top: 25px; font-size: 11px; border-top: 2px dashed #000; padding-top: 15px; font-weight: bold; }
        /* [SINTAKS CSS]: @media print | Membuat style spesifik ketika jendela cetak (Ctrl+P) terbuka (Biar tombol kembali hilang otomatis) */
        @media print { .no-print { display: none; } }
    </style>
</head>
<!-- [SINTAKS JAVASCRIPT]: onload="window.print()" | Event trigger yang langsung memanggil fitur Print Browser begitu loading halaman selesai -->
<body onload="window.print()">
    <!-- [SINTAKS HTML]: <div> class="header" | Kontainer pembungkus bagian kop struk -->
    <div class="header">
        <h2>PARLINE</h2>
        <p>Hogwarts Parking System</p>
    </div>

    <!-- [SINTAKS HTML]: <div> class="content" | Kontainer utama isi rincian kedatangan -->
    <div class="content">
        <!-- [SINTAKS PHP]: str_pad() | Menambah bantalan angka "0" di awal ID agar jadi #000xx -->
        <div class="row"><span>KODE TIKET:</span> <span>#<?php echo str_pad($r['id_parkir'], 5, '0', STR_PAD_LEFT); ?></span></div>
        <!-- [SINTAKS PHP]: strtoupper() | Fungsi build-in untuk mengonversi huruf jadi kapital besar semua -->
        <div class="row"><span>NO. PLAT:</span> <span style="font-size: 16px;"><?php echo strtoupper($r['plat_nomor']); ?></span></div>
        <div class="row"><span>JENIS:</span> <span><?php echo strtoupper($r['jenis_kendaraan']); ?></span></div>
        <!-- [SINTAKS PHP]: substr() | Fungsi build-in untuk memotong string apabila string terlalu panjang -->
        <div class="row"><span>PETUGAS:</span> <span><?php echo strtoupper(substr($r['petugas'], 0, 15)); ?></span></div>
        <div class="row" style="margin-top: 15px;"><span>WAKTU MASUK:</span></div>
        <div class="row" style="justify-content: center; font-size: 16px; margin-top: 5px;">
            <!-- [SINTAKS PHP]: date() format + strtotime() | Merombak waktu database mentah menjadi representasi tanggal/jam -->
            <span><?php echo date('d M Y - H:i', strtotime($r['waktu_masuk'])); ?> WIB</span>
        </div>
        
        <!-- [SINTAKS PHP]: rand() | Menghasilkan angka acak pseudo sebagai simulasi barcode struk / pengaman tambahan -->
        <div class="barcode">
            *<?php echo $r['id_parkir'] . rand(100,999); ?>*
        </div>
        
        <div style="text-align: center; font-size: 12px; font-weight: bold;">
            Tanda masuk ini adalah<br>bukti sah parkir kendaraan.<br>Jangan sampai hilang.
        </div>
    </div>

    <!-- [SINTAKS HTML]: <div> class="footer" | Kontainer bawa kaki struk imbauan aman -->
    <div class="footer">
        <p>SIMPAN KARCIS DENGAN AMAN.<br>KEHILANGAN KARCIS DIKENAKAN DENDA.</p>
    </div>

    <!-- [SINTAKS HTML]: <div> class="no-print" | Kontainer wrapper untuk tombol bali biar pas ngeprint tidak ikut tecetak -->
    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <a href="transaksi_masuk.php" style="text-decoration:none; background:#2563eb; color:white; padding:12px 25px; border-radius:10px; font-family: sans-serif; font-weight: bold;">Kembali Ke Beranda</a>
    </div>
</body>
</html>
