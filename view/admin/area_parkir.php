<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/area_parkir.php
// -> Tujuan Spesifik: Modul Admin untuk Monitoring Layout Status Ketersediaan masing-masing Blok Lahan Parkir secara Visual Grid.
// ======================================

// [SINTAKS PHP]: session_start() | Persiapan penerimaan token memori akses User aktif
session_start();

// [SINTAKS PHP]: Verifikasi Cekam Role | Hanya kasta Administrator yg sanggup menembus pagar pembatas halaman ini
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: redirect header | Lemparan keluar kembali ke lobi utama login jika terdeteksi akun kasta petugas/owner
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Mengikutsertakan modul koneksi.php utama
include '../../config/koneksi.php';

/* [SINTAKS PHP]: Inisialisasi Data Array Mutidimensi Dummy Visual Layout (Hardcode Mock-up) */
// -> Karena sistem DB Area yg sesungguhnya tidak begitu detail sedia denah gedung per lantai, Admin pakai data visual hardcode buat pemanis di presentasi.
$areas = [
    ['nama' => 'Lantai 1', 'lokasi' => 'BLOK UTAMA', 'total' => 50, 'terisi' => 4],
    ['nama' => 'Blok A - Depan', 'lokasi' => 'BLOK LOKASI', 'total' => 50, 'terisi' => 7],
    ['nama' => 'Blok B - Samping', 'lokasi' => 'BLOK LOKASI', 'total' => 60, 'terisi' => 2],
];
?>

<!-- [SINTAKS HTML]: Document Node Tree Versioning Type -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts - Area Parkir</title>
    <!-- [SINTAKS HTML]: Link Google Fonts Api 'Plus Jakarta Sans' -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: Penjabaran Cascading style inline -->
    <style>
        /* [SINTAKS CSS]: Root Variabel Koleksi Warna Tema Konsisten Area Admin */
        :root {
            --primary: #1d4ed8;
            --grad-1: #e0f2fe; 
            --grad-2: #bae6fd;
            --success: #10b981;
        }

        /* [SINTAKS CSS]: Full Screen Locking | Mengunci Ketinggian Penuh (100%) dan melarang scroll bar jelek web asli agar tampak serupa aplikasi desktop/kios responsif */
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }

        body { 
            background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%);
            display: flex; justify-content: center; align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* [SINTAKS CSS]: Main Frame Container Layout Dashboard Lebar Hampir Penuh (96%) Putih lengkung */
        .app-container {
            width: 96%; height: 94vh;
            background: white; border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08); /* Efek Soft Shadow Terjun ke Bawah tebal */
        }

        /* [SINTAKS CSS]: Navigasi Pilah Sisi Kiri Lebar Tetap statis 280px */
        .sidebar {
            width: 280px; background: #f1f5f9;
            padding: 40px 25px; display: flex; flex-direction: column;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* [SINTAKS CSS]: Styling Bagian Judul Merek Sudut Atas */
        .logo-section { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; padding-left: 10px; }
        .logo-section img { width: 40px; height: 40px; border-radius: 10px; }
        .logo-section h2 { font-size: 22px; margin: 0; color: #0f172a; font-weight: 800; }

        /* [SINTAKS CSS]: Area Menu Membesar dinamis mengikuti sisa tinggi */
        .nav-menu { flex-grow: 1; }
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            text-decoration: none; color: #64748b; font-size: 15px; font-weight: 600;
            margin-bottom: 8px; border-radius: 18px; transition: 0.3s;
        }
        
        /* [SINTAKS CSS]: Status Active yg Menyala Biru di Tab Menu Akses saat ini */
        .nav-menu a.active { background: #1d4ed8; color: white; box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25); }
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #0f172a; }

        /* [SINTAKS CSS]: Content Area | Sisa Lahan di kanan untuk konten utama dengan Overflow Y auto agar konten yg kepanjangan bisa discroll lokal */
        .main-content { flex: 1; background: white; padding: 40px 50px; overflow-y: auto; }

        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .section-title { font-size: 28px; color: #0f172a; margin: 0; font-weight: 800; }

        /* [SINTAKS CSS]: area-grid (Advanced CSS GRID) | Membagi elemen menjadi Kolom-Kolom responsif elastis auto-fill. Minimal 320px lebarnya dan meregang maksimum 1 Fractional*/
        .area-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        /* [SINTAKS CSS]: Area Card Layout | Kotak Putih Pemisah Per Tipe Denah Lantai Parkir */
        .area-card {
            background: white; border-radius: 24px; padding: 30px;
            border: 1px solid #e2e8f0; transition: 0.3s;
            position: relative;
        }
        
        /* [SINTAKS CSS]: Transisi animasi Hover yang mengangkat Card menjauh bayangan ke atas sebesar 5 piksel (-5px) */
        .area-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); }

        /* [SINTAKS CSS]: Pill Absolute Positioning | Label/Tag hijau 'Tersedia' nangkring menempel dikanan atas area kotak tanpa terpengaruh flow elemen baris lain */
        .status-badge {
            position: absolute; top: 30px; right: 30px;
            background: #dcfce7; color: var(--success);
            padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800;
        }

        .area-name { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 5px 0; }
        .area-loc { color: #64748b; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 5px; margin-bottom: 25px; }

        .stat-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .stat-label { font-size: 13px; color: #475569; font-weight: 600; }
        .stat-value { font-size: 14px; font-weight: 800; color: #0f172a; }
        .stat-value.available { color: var(--success); }

        /* [SINTAKS CSS]: Lintasan Progress Warna Abu */
        .progress-container {
            height: 10px; background: #f1f5f9; border-radius: 20px;
            overflow: hidden; margin: 15px 0;
        }
        
        /* [SINTAKS CSS]: Warna Biru Pengisi Meteran Lintasan Progress Dinamis dgn Transisi Lembut */
        .progress-bar { height: 100%; background: #1d4ed8; border-radius: 20px; transition: 1s ease-in-out; }

        .perc-label { text-align: right; font-size: 11px; color: #64748b; font-weight: 700; }
        .avatar { width: 40px; height: 40px; background: #1d4ed8; color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- [SINTAKS HTML]: Kerangka Menu Samping -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="kelola_user.php">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <!-- [SINTAKS HTML]: Hyperlink Page Anchor Kelas Aktif (Tab Bergelap Biru) -->
                <a href="area_parkir.php" class="active">🕒 Data Area</a>
            </div>
            
            <a href="../../auth/logout.php" style="margin-top: auto; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <!-- [SINTAKS HTML]: Blok Pembungkus Nama Judul & Profil Petugas Admin Kanan -->
            <div class="header-top">
                <h1 class="section-title">Informasi Area</h1>
                <div style="display: flex; align-items: center; gap: 15px; border-left: 1px solid #f1f5f9; padding-left: 20px;">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Admin Hogwarts</div>
                        <div style="font-size: 11px; color: #64748b;">Gringotts Level</div>
                    </div>
                    <div class="avatar">H</div>
                </div>
            </div>

            <!-- [SINTAKS HTML]: Tempat Susunan Box Area Parkir yg bisa berlipat otomatis via GRID CSS -->
            <div class="area-grid">
                <?php 
                // [SINTAKS PHP]: Foreach Loop () | Membedah satu persatu nilai koleksi Array multi Mockup $areas yg dilist pd deklarasi awal ke Var Singular $a berturut turut
                foreach($areas as $a): 
                    // [SINTAKS PHP]: Operasi Pecahan Matematis untuk menghitung ratio Persentase Mobil yang nangkring mengisi Blok Parkir
                    $persen = ($a['terisi'] / $a['total']) * 100;
                    
                    // [SINTAKS PHP]: Aritmatika hitung sisa tempat longgar
                    $sisa = $a['total'] - $a['terisi'];
                ?>
                <!-- [SINTAKS HTML]: Box Komponen Data Looping (Ini akan di Generate terus Sebanyak Blok Area yg didefinisikan db array) -->
                <div class="area-card">
                    <span class="status-badge">TERSEDIA</span>
                    <!-- [SINTAKS PHP]: Echo Array Key pemutar Nilai Tulisan 'Lantai 1, Blok A, dsb' -->
                    <h3 class="area-name"><?= $a['nama'] ?></h3>
                    <div class="area-loc">📍 <?= $a['lokasi'] ?></div>

                    <div class="stat-row">
                        <span class="stat-label">Total Kapasitas</span>
                        <span class="stat-value"><?= $a['total'] ?> Slot</span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">Tersedia</span>
                        <span class="stat-value available"><?= $sisa ?> Slot</span>
                    </div>

                    <!-- [SINTAKS HTML]: Track rel warna abu progres bar -->
                    <div class="progress-container">
                        <!-- [SINTAKS PHP]: Style Inline CSS Eksekutor (Width Persentase % Dinamis) | Memuaikan panjang garis pengisi meteran linear dgn suntikan kalkulasi variabel angka persen PHP secara inline  -->
                        <div class="progress-bar" style="width: <?= $persen ?>%;"></div>
                    </div>
                    
                    <!-- [SINTAKS PHP]: PHP Round() Math function | Membulatkan panjang desimal berkoma misal "15.4243%" menjadi bulat "15%" biar rapi dilihat -->
                    <div class="perc-label">Terisi: <?= round($persen) ?>%</div>
                </div>
                <?php 
                // [SINTAKS PHP]: Endforeach | Tutup Siklus eksekutor Perulangan List Elemen
                endforeach; 
                ?>
            </div>
        </div>
    </div>

</body>
</html>
