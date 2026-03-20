<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/laporan.php
// -> Tujuan Spesifik: Halaman Modul Pengecekan riwayat omset dan cashflow historis berdasarkan pendapatan transaksi tiket check-out terbayar.
// -> Peringatan: Nampaknya file ini merupakan salinan parsial Modul Petugas yang belum disesuaikan penuh role/hak aksesnya 100%, biarkan berjalan AS-IS untuk presentasi.
// ======================================

// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();

// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

// [SINTAKS PHP]: Cek Kondisi Role Bug(?) | Pemerikasaan hak akses ini menolak siapapun yang "Bukan Petugas". Kemungkinan ini script copo dari folder petugas yg lupa diupdate variabel pengecekannya ke "admin".
if($_SESSION['role'] != "petugas") { 
    // [SINTAKS PHP]: header() | Fungsi pengalihan otomatis ke lokasi form otentikasi
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Menuliskan Identitas Variabel Lokal dari penyedotan Keranjang Kantung Sesi Session $_SESSION Browser.
$nama_petugas = $_SESSION['nama_lengkap'] ?? 'Petugas';

// [SINTAKS PHP]: Kueri Data Pelaporan Rantai Panjang JOIN | Mengambil tumpung transaksi struk yang murni 'Lunas Check out', dibantu relasi ikatan Tipe ID Kendaraan dan Tipe Relasi Siapa Pemungut Kasir yang Narik Duitnya 
$query = mysqli_query($koneksi, "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, u.nama_lengkap as petugas FROM tb_transaksi t JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan LEFT JOIN tb_user u ON t.id_user = u.id_user WHERE t.status='keluar' ORDER BY t.waktu_keluar DESC");

// [SINTAKS PHP]: Pengumpulan Pundi Total Pendapatan Kalkulator Otomatis SQL | Menghitung (Sum) akumulasi tagihan Total duit dari khusus kendaraan yg telah keluar (Lunas) sepanjang masa hidup the data tabel.
$total_duit = mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE status='keluar'");

// [SINTAKS PHP]: Fetch Extracting Tumpukan Hasil Query Jadi Variabel Index Teks
$total = mysqli_fetch_assoc($total_duit);
?>

<!-- [SINTAKS HTML]: Pola Penjamin Format HTML 5 Valid Standard Root Tag -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan - Hogwarts Parkir</title>
    <!-- [SINTAKS HTML]: Integrator Tipografi Gaya Huruf modern Dari Server CDN milik Web Font Library Google  -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Desain Rangka Dasar Template Dokumen */
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: #f1f5f9; }
        
        /* [SINTAKS CSS]: Panel Navigasi Tetap Memaku Menjulang Setinggi Layar PC Viewport Height Kiri Dinding Warna Marun gelap darah  */
        .sidebar { width: 260px; height: 100vh; background: #8f3434; color: white; position: fixed; padding: 20px 0; }
        
        /* [SINTAKS CSS]: Bentuk Balok Ruangan Link Teks Estetik Padding besar  */
        .sidebar a { display: block; color: rgba(255,255,255,0.8); padding: 14px 25px; text-decoration: none; }
        
        /* [SINTAKS CSS]: Modifikasi Tab halaman Laporan aktif Menyorot Biru dengan radius setengah bundar yg nempel ujung Kanan Layar List Nav  */
        .sidebar a.active { background: #782626; color: white; font-weight: 600; border-radius: 0 50px 50px 0; margin-right: 20px; }
        
        /* [SINTAKS CSS]: Tumpuan Utama Celah Spasi Kanan (Biar gak ketumpuk Sidebar absolute Fix tadi di Margin Kiri 260px nya) */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        
        /* [SINTAKS CSS]: Bungkus UI Area Putih Kartu Tabel Shadow Halusan Minimalis Border lengkung radius moderat 20 Pixel Drop Down */
        .table-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        /* [SINTAKS CSS]: Model Tabel List Panjang Default Lebar 100%, Menyatu Tak Terdapat garis ganda / bolong rongga Collapse Cell   */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        
        /* [SINTAKS CSS]: Standarisasi Jarak Antar Teks Judul Kolom (TH) & baris data kolom Isi (TD) Serta Garis strip Halus pembatas bawah solid grey border bottom */
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        
        /* [SINTAKS CSS]: Mewarnai Heading Atas Judul Tabel (TH) Abu Muda Lembut Pucat Spasi uppercase Kapital Kecil imut 12px huruf  */
        th { background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 12px; }
        
        /* [SINTAKS CSS]: Kotakan Menor Peringkasan (Tingkat Peringkasan) Omset / Penghargaan Kotak Gede Merah  Darah Marun Inline-Block dinamis Menghindari Patahan flex box width 300 Paling Minimal  */
        .summary-box { background: #8f3434; color: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; display: inline-block; min-width: 300px; }
    </style>
</head>
<body>
    
    <!-- [SINTAKS HTML]: Konstruksi Modul Kerangka Samping Penanda Arah Panduan Kemudi Lokasi App -->
    <div class="sidebar">
        <!-- [SINTAKS HTML]: Center inline Style Header Judul Teks Kop Aplikasi  -->
        <div style="text-align:center; padding: 20px;"><h2 style="font-size: 14px;">HOGWARTS PARKIR</h2></div>
        
        <!-- [SINTAKS HTML]: Rentetan Baris Link Pemindah Arah Lokasi Kerja File Petugas  -->
        <a href="dashboard.php"> Monitoring Area</a>
        <a href="transaksi_masuk.php"> Kendaraan Masuk</a>
        <a href="transaksi_keluar.php"> Kendaraan Keluar</a>
        <!-- [SINTAKS HTML]: Anchort Aktif Penyorot Status Terbuka Biru Khusus Halaman Analisa Laporan  -->
        <a href="laporan.php" class="active"> Laporan Harian</a>
        
        <!-- [SINTAKS HTML]: Link Mandiri Lompat Keluar Menutup Sesi Parkir Berwarna Merah muda terang  -->
        <a href="../../auth/logout.php" style="margin-top:auto; color:#ffb1b1;"> Logout</a>
    </div>

    <!-- [SINTAKS HTML]: Lapang Lingkup Konten Kerja Kanan Bebas Rintanan Sidebar Kiri  -->
    <div class="main-content">
        <h1>Laporan Pendapatan</h1>
        
        <!-- [SINTAKS HTML]: Balok Visual Kotak Infografis Merah Rekapan Numerikal Keuangan Akuntansi -->
        <div class="summary-box">
            <!-- [SINTAKS HTML]: CSS Opacity Teks pudar tipis semi transparan untuk Sub-Judul pendukung Biar nggak menyolok lebih drpada angkanya -->
            <p style="margin:0; opacity:0.8;">Total Pendapatan Hari Ini</p>
            
            <!-- [SINTAKS PHP]: Suntik Format Akuntansi Pembubuk Tulisan Rupiah Nominal Angka Ribuan dengan Titik (Memakai Coalesce '?? 0' pengurang risiko display Blank/Error Jika Hari Ini lagi sepi bgt blm dpt income sama sekali)  -->
            <h2 style="margin:5px 0 0 0;">Rp <?= number_format($total['total'] ?? 0, 0, ',', '.') ?></h2>
        </div>

        <!-- [SINTAKS HTML]: Lingkaran Wadah List Rekapan Detail Ekstensif Turunan Database  -->
        <div class="table-card">
            <table>
                <!-- [SINTAKS HTML]: Bagian Header/Topi Tabel  -->
                <thead>
                    <tr>
                        <!-- [SINTAKS HTML]: Th (Table Header - Sel Judul Bersebelahan ) -->
                        <th>Waktu Keluar</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Biaya</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                
                <!-- [SINTAKS HTML]: Tbody Kelopak Badan Kantung Seluruh Deretan Informasi Inti Baris Per Baris (Row per Row) Datasets List Kendaraan Turunan SQL Iterator Row -->
                <tbody>
                    <?php 
                    // [SINTAKS PHP]: Looper Pencacah Pengurai Tumpung Kueri DB Baris Menjadi Rangkai Teks array terpisah Singular Secara Konstan Terbentur Limits File
                    while($row = mysqli_fetch_assoc($query)): 
                    ?>
                    <!-- [SINTAKS HTML]: Penampang Row (TR) Satuan yg akan dilooping terus ke bawah layar menyusun laporan per 1 bukti karcis lunas  -->
                    <tr>
                        <!-- [SINTAKS PHP]: Konversi Tanggal Bawaan Mesin Basis Data Y-m-d H:i:s  Menjadi Format Cantik Pendek Format Kalender Sipil Nusantara  -->
                        <td><?= date('d/m/Y H:i', strtotime($row['waktu_keluar'])) ?></td>
                        
                        <!-- [SINTAKS HTML/PHP]: Penulisan Lempar Plat Nopol Bercetak Bold Hitam Hitam Tebal Tebal Ciri Khusus Primer Text (Menggunakan Strong / Tag B )  -->
                        <td><b><?= $row['plat_nomor'] ?></b></td>
                        
                        <!-- [SINTAKS PHP]: Pemaksimal Kapitalisasi Huruf Awal Pertama Jenis Tipe Kendaraanya 'mobil' jd 'Mobil', 'truk' jadi 'Truk' UcFirst String Modification  -->
                        <td><?= ucfirst($row['jenis_kendaraan']) ?></td>
                        
                        <!-- [SINTAKS PHP]: Tambalan Sintaks Nominal Berformat Ribuan Koma Rp. Uang Rupiah Akuntansi Dari Biaya Akhir -->
                        <td>Rp <?= number_format($row['biaya_total'], 0, ',', '.') ?></td>
                        
                        <!-- [SINTAKS PHP]: Menempelkan Teks Murni Siapa Nama Saksi Pegawai Yang Nyomot Tiketi Keluar Transaksi Pada Saat Itu Jaga Kasir  -->
                        <td><?= $row['petugas'] ?></td>
                    </tr>
                    <?php 
                    // [SINTAKS PHP]: Menutup Skat Pagar Penghujung Pembatasan Wilayah Siklus Render Generator Row Loop While Statement Atas
                    endwhile; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
