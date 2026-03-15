<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/petugas/transaksi_keluar.php
// -> Tujuan Spesifik: Fitur tracking C-Out (Check Out) dan mesin pencarian plat kendaraan di database bagi mereka yg bersiap keluar gerbang parkir.
// ======================================

// [SINTAKS PHP]: session_start() | Melanjutkan sesi browser penanda Session State
session_start();

// [SINTAKS PHP]: Validasi Session Privilege | Memaksa siapapun yang BUKAN petugas untuk tertendang keluar halaman ini
if($_SESSION['role'] != "petugas") { 
    // [SINTAKS PHP]: header location | Pengalihan sistem otomatis (Redirect) ke pintu luar indeks Auth
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: include | Tarikan file konektor MySQL
include '../../config/koneksi.php';

// [SINTAKS PHP]: Query Metrik Lahan | Menghitung kumulatif kendaraan terpakir (Dipakai untuk Bar loading UI)
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;
?>

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Menjamin render formasi HTML versi 5 -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts Petugas - Transaksi Keluar</title>
    <!-- [SINTAKS HTML]: <link> Font | Relasi ekstrenal pengunduhan Web-Font dari library Google API -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: <style> Inline Stylesheet -->
    <style>
        /* [SINTAKS CSS]: :root | Deklarator Konstan Variabel Hex Tema Aplikasi */
        :root {
            --primary-blue: #2563eb; 
            --primary-hover: #1e40af;
            --bg-gradient: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); 
            --text-main: #1e293b;
            --text-sub: #64748b;
            --white: #ffffff;
        }

        /* [SINTAKS CSS]: Reset CSS Margin (*) | Menonaktifkan jarak default elemen browser */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* [SINTAKS CSS]: body Tag Rule | Pengatur Layar Belakang Penuh (100vh) */
        body { 
            margin: 0; 
            background: var(--bg-gradient); 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 15px;
        }

        /* [SINTAKS CSS]: Container UI Frame */
        .app-container {
            width: 100%; max-width: 1200px; height: 85vh; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: .sidebar Menu | Setelan bilah menu pinggir Fix Width */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 20px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: Pemodelan Hyperlink Tab Menu bersudut tumpul tanpa garis biru */
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: #475569; font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        /* [SINTAKS CSS]: Active tab highlight | Menandai biru tebal item page Keluar yg sdg dibuka */
        .nav-menu a.active { 
            background: #2563eb; color: var(--white); 
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
        
        /* [SINTAKS CSS]: Efek Hover Halimun item selain Active */
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #2563eb; }

        /* [SINTAKS CSS]: Panel Frame Utama Flex dinamis (Ber-ScrollBar Kanan Y) */
        .main-content {
            flex: 1; background: #f1f5f9; padding: 40px 50px; overflow-y: auto;
        }

        .header-top h1 { font-size: 24px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: Pengatur titik gravitasi di Kotak Wrapper Card Layout */
        .form-wrapper {
            max-width: 550px; margin: 40px auto 0;
        }

        /* [SINTAKS CSS]: Balok Informatif warna biru telor asin. Menambahkan aksen profesional */
        .info-box {
            background: #eff6ff; border: 1px solid #bfdbfe;
            padding: 15px 20px; border-radius: 18px; margin-bottom: 25px;
            color: #1d4ed8; font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }

        /* [SINTAKS CSS]: Desain Bingkai Form Papan Cek Plat Nomor */
        .form-card {
            background: var(--white);
            padding: 35px; border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        }

        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block; font-size: 11px; font-weight: 800; color: #475569;
            margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* [SINTAKS CSS]: Modifikasi Bar Input Pengetikan Petugas */
        .form-group input {
            width: 100%; padding: 14px 18px; border-radius: 15px;
            border: 1px solid #e2e8f0; background: #f1f5f9;
            font-size: 14px; color: #0f172a; outline: none;
            transition: 0.3s; font-weight: 500;
        }

        /* [SINTAKS CSS]: Animasi dering nyala Box Shadow pada input bila fokus terpilih pointer */
        .form-group input:focus {
            border-color: #2563eb;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: .btn-search class | Styling khusus tombol Eksekutor pencarian plat SQL */
        .btn-search {
            background: #2563eb; color: white; border: none;
            width: 100%; padding: 16px; border-radius: 15px;
            font-weight: 800; font-size: 14px; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.25);
        }
        
        .btn-search:hover { background: var(--primary-hover); transform: translateY(-2px); }

        .storage-box {
            margin-top: auto; padding: 20px; background: #f1f5f9; border-radius: 20px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- [SINTAKS HTML]: Panel Sidebar Kiri -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" width="38">
                <h2>Parline</h2>
            </div>
            
            <!-- [SINTAKS HTML]: Navigasi Menu. Class active kini dipindah menempel di tab Transaksi Keluar -->
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="transaksi_masuk.php">📥 Transaksi Masuk</a>
                <a href="transaksi_keluar.php" class="active">📤 Transaksi Keluar</a>
            </div>

            <!-- [SINTAKS HTML]: Metrik Realtime Panel UI Kapasitas Gedung Parkir -->
            <div class="storage-box">
                <p style="margin:0 0 10px 0; font-size:10px; font-weight:800; color:#475569; letter-spacing:0.5px;">STORAGE DETAILS</p>
                <div style="height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px;">
                    <!-- [SINTAKS PHP]: Inline Kalkulasi Persen Meteran Biru -->
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height:100%; background:#2563eb;"></div>
                </div>
                <!-- [SINTAKS PHP]: Inline Variable Echo Print dari hasil query line 16 -->
                <p style="margin:0; font-size:13px; font-weight:800; color:#0f172a;">Slot: <?= $kendaraan_masuk ?> <span style="color:#94a3b8; font-weight:600;">/ 1350</span></p>
            </div>
            
            <!-- [SINTAKS HTML]: Tombol Log Out Darurat -->
            <a href="../../auth/logout.php" style="margin-top:25px; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪 Logout</a>
        </div>

        <!-- [SINTAKS HTML]: Frame Utama Sebelah Kanan (Ruangan Kerja Keluar Parkiran) -->
        <div class="main-content">
            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Checkout Parkir</h1>
                    <!-- [SINTAKS PHP]: Inline date format kalender (Dinamis berubah setiap berganti fajar hari) -->
                    <p style="color:#475569; margin:5px 0 0 0; font-size:14px;"><?= date('l, d F Y') ?></p>
                </div>
                <!-- [SINTAKS HTML]: Flex Container Atribut Profil Pekerja (Avatars & Nametag) -->
                <div style="display:flex; align-items:center; gap:15px;">
                    <div style="text-align:right">
                        <!-- [SINTAKS PHP]: Deklarasi Pemanggilan Nama User Pegawai yang lagi shif Log In saat ini -->
                        <div style="font-size:14px; font-weight:800; color:#0f172a;"><?= $_SESSION['nama'] ?></div>
                        <div style="font-size:11px; color:#475569; font-weight:600;">Petugas Aktif</div>
                    </div>
                    <!-- [SINTAKS PHP]: Potong Abjad Pertama buat Avatar Foto Profil huruf tunggal -->
                    <div style="width:45px; height:45px; background:#2563eb; border-radius:14px; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);">
                        <?= substr($_SESSION['nama'], 0, 1) ?>
                    </div>
                </div>
            </div>

            <!-- [SINTAKS HTML]: Pembungkus Kotak Kartu Form -->
            <div class="form-wrapper">
                <div class="form-card">
                    <!-- [SINTAKS HTML]: <form> Formulir Pencarian Database Metode transmisi POST (Aman Tersembunyi) tertuju melempar Value ke Proses_Keluar script -->
                    <form action="proses_keluar.php" method="POST">
                        <div class="form-group">
                            <label>Cari Nomor Plat / Kode Karcis</label>
                            <!-- [SINTAKS HTML]: <input Keyword> Kolom input yang menangkap Array string dari Plat mobil yang akan dicari. Atribut 'autofocus' menyongsong kursor aktif segera seiring webpage terbuka. -->
                            <input type="text" name="keyword" placeholder="Contoh: B 1234 ABC" required autofocus>
                        </div>

                        <!-- [SINTAKS HTML]: <button submit> Pemantik Kiriman Request Data dari Browser ke Server PHP (Action Trigger)-->
                        <button type="submit" name="cari_transaksi" class="btn-search">Cek Total Biaya</button>
                        
                        <!-- [SINTAKS HTML]: <a> Hyperlink kembali balik ke beranda Dashboard Panel -->
                        <a href="dashboard.php" style="display:block; text-align:center; margin-top:20px; font-size:13px; color:#475569; text-decoration:none; font-weight:700;">← Kembali ke Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
