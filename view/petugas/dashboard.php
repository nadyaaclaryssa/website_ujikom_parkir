<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/petugas/dashboard.php
// -> Tujuan Spesifik: Halaman antarmuka monitor Kasir Petugas Parkir (Frontend) u/ memantau stok area slot parkir yang ada vs terpakai secara real-time.
// ======================================

// [SINTAKS PHP]: session_start() | Memulai sesi browser untuk menyisipkan & membaca memori kredensial login (Menjaga agar layar ini tak bisa di bypass URL)
session_start();

// [SINTAKS PHP]: Percabangan if() verifikasi Role | Memblokir user Admin/Owner masuk ke halaman khusus Petugas 
if($_SESSION['role'] != "petugas") { 
    // [SINTAKS PHP]: header("location...") | Melempar paksa penyelundup kembali ke halaman form Login depan
    header("location:../../auth/index.php"); 
    // [SINTAKS PHP]: exit | Memastikan eksekusi pemuatan halaman direm / dihentikan seutuhnya saat dilempar
    exit; 
}

// [SINTAKS PHP]: include | Menyertakan core engine database `koneksi.php` untuk ritual penarikan dan eksekusi Query MySQL
include '../../config/koneksi.php';

// [SINTAKS PHP]: Query Agregasi Bersarang | Menghitung total seluruh baris di tabel transaksi yang statusnya masih 'masuk' (Kendaraan yang parkir as-of-now)
// [SINTAKS PHP]: Operator Coalescing (??) | Jika hasil query null/kosong, rubah ke angka 0. (Mengamankan variabel dari nilai Undefined)
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;
?>

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Pernyataan bahwa kerangka ini menggunakan versi HTML5 standar -->
<!DOCTYPE html>
<!-- [SINTAKS HTML]: <html lang="id"> | Root node dengan settingan lokalisasi wilayah ID (Indonesia) -->
<html lang="id">
<head>
    <!-- [SINTAKS HTML]: <meta charset="UTF-8"> | Memampukan website membaca ragam karakter encoding global (Unicode) termasuk emoji dan karakter unik -->
    <meta charset="UTF-8">
    <!-- [SINTAKS HTML]: <title> | Nama label di bagian atas tab Browser -->
    <title>Hogwarts Petugas - Dashboard</title>
    
    <!-- [SINTAKS HTML]: <link> Google Fonts | Memanggil pustaka Font eksternal dari server Google secara dinamis -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: <style> | Menulis baris kode Internal Stylings (Vanilla CSS) untuk mempercantik tata letak Visual HTML -->
    <style>
        /* [SINTAKS CSS]: :root | Kamus penyimpanan rentang warna (Value variables hex custom) untuk kemudahan reuse warna tema aplikasi */
        :root {
            --primary-blue: #2563eb; 
            --primary-hover: #1e40af;
            --bg-gradient: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); 
            --text-main: #1e293b;
            --text-sub: #64748b;
            --white: #ffffff;
        }

        /* [SINTAKS CSS]: Universal Reset (*) | Menormalkan margin & padding pabrik di seluruh jenis browser agar ukuran grid tidak meleset/rusak */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* [SINTAKS CSS]: body | Pembungkus luar background yang akan diatur model layouting-nya menjadi Center Flexbox (rata tengah 100%) */
        body { 
            margin: 0; 
            background: var(--bg-gradient); 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 15px;
        }

        /* [SINTAKS CSS]: .app-container | Kanvas / Layar Kaca utama aplikasi dengan batasan ukuran Max Height & Max Width (Modern App Layout) */
        .app-container {
            width: 100%; max-width: 1200px; height: 85vh; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: .sidebar | Panel navigasi sebelah kiri membentang Vertikal */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* [SINTAKS CSS]: Flex Grid Gap | Menyisipkan jarak kosong dekorasi di antara Logo Image & Text "Parline" */
        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 20px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: a anchor styles | Merombak Link garis bawah biru default browser menjadi tombol Navbar elegan ber-Padding lengkung */
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: #475569; font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        /* [SINTAKS CSS]: .active class spesifik | Penanda (Indikator warna) menyala yang menyorot halaman apa yang sedang dibuka oleh User saat ini */
        .nav-menu a.active { 
            background: #2563eb; color: var(--white); 
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
        
        /* [SINTAKS CSS]: Hover Pseudo Not | Memberi efek sorotan abu-abu hanya untuk Menu yg tidak aktif ketika Mouse menggeser lewat di atasnya */
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #2563eb; }

        /* [SINTAKS CSS]: .main-content | Panel Utama sisi Kanan selebar sisa jendela (Flex 1) di mana konten list berubah dinamis dapat di-Scroll (Overflow Y) */
        .main-content {
            flex: 1; background: #f1f5f9; padding: 40px 50px; overflow-y: auto;
        }

        .header-top h1 { font-size: 24px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: CSS Grid Layout | Konfigurasi Layout canggih pembuat tabel matrix kartu yang ukurannya otomatis merapat melebar (Auto-fit Minmax) */
        .area-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px; 
            margin-top: 30px;
        }

        /* [SINTAKS CSS]: .area-card | Sentuhan Visual Box pada Data Lahan Parkir per area */
        .area-card {
            background: var(--white);
            padding: 20px 15px; 
            border-radius: 25px; 
            border: 1px solid #e2e8f0; text-align: center;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        
        /* [SINTAKS CSS]: Hover Transform | Efek kartu lahan parkir sedikit terapung melonjak 5px ke atas dengan bayangan ketika dimonitor pointer Mouse */
        .area-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.05);
            border-color: #2563eb;
        }
        
        .area-card h3 { font-size: 15px; color: #0f172a; margin: 0; font-weight: 800; }
        
        /* [SINTAKS CSS]: Display Block | Memaksa angka merah memecah line jadi paragraf blok tersendiri agar estetik turun ke bawah tulisan judul Area */
        .area-card .count { 
            font-size: 42px; 
            font-weight: 800; 
            color: #ef4444; 
            margin: 10px 0; 
            display: block;
        }
        
        .area-card .label { 
            font-size: 9px; 
            font-weight: 800; 
            color: #475569; 
            letter-spacing: 0.5px; 
            text-transform: uppercase;
        }

        /* [SINTAKS CSS]: Tombol Check-in Lahan Pintas hijau */
        .btn-checkin {
            background: #22c55e; color: var(--white); text-decoration: none;
            padding: 8px 25px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: 700; 
            display: inline-block; 
            margin-top: 15px; 
            transition: 0.3s;
        }
        /* [SINTAKS CSS]: Hover Scale | Membuat tombol sekilas membesar 5% */
        .btn-checkin:hover { background: #16a34a; transform: scale(1.05); }

        /* [SINTAKS CSS]: margin-top: auto | Mendorong kotak storage summary ke pojok terbawah area Flex Sidebar */
        .storage-box {
            margin-top: auto; padding: 20px; background: #f1f5f9; border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- [SINTAKS HTML]: <div> class app-container | Window Utama Layar Aplikasi Web  -->
    <div class="app-container">
        
        <!-- [SINTAKS HTML]: <div> Sidebar Menu Utama (Bagian Kiri) -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Menyeret dan menampilkan gambar aset lokal untuk hiasan merek -->
                <img src="../../public/hogwarts-removebg-preview.png" width="38">
                <h2>Parline</h2>
            </div>
            
            <!-- [SINTAKS HTML]: <nav> Group Hyperlink Menu Petugas | Akses Dashboard, TransMasuk, & TransKeluar -->
            <div class="nav-menu">
                <a href="dashboard.php" class="active">🏠 Dashboard</a>
                <a href="transaksi_masuk.php">📥 Transaksi Masuk</a>
                <a href="transaksi_keluar.php">📤 Transaksi Keluar</a>
            </div>

            <!-- [SINTAKS HTML]: <div> Storage Tracking UI | Indikator Progress stok parkir gedung dari limit max 1350 Data -->
            <div class="storage-box">
                <p style="margin:0 0 10px 0; font-size:10px; font-weight:800; color:#475569; letter-spacing:0.5px;">STORAGE DETAILS</p>
                <div style="height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px;">
                    <!-- [SINTAKS PHP]: Inline Echo (=) | Memompa perhitungan aritmatika progress width loading bar berdasarkan persenan Data Relasional di MySQL -->
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height:100%; background:#2563eb;"></div>
                </div>
                <!-- [SINTAKS PHP]: Inline Echo (=) | Mencetak String Variabel Total parkir vs kapasitas statik -->
                <p style="margin:0; font-size:13px; font-weight:800; color:#0f172a;">Slot: <?= $kendaraan_masuk ?> <span style="color:#94a3b8; font-weight:600;">/ 1350</span></p>
            </div>
            
            <!-- [SINTAKS HTML]: <a> Anchor Link Perpisahan Logout -->
            <a href="../../auth/logout.php" style="margin-top:25px; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪 Logout</a>
        </div>

        <!-- [SINTAKS HTML]: <div> Canvas Kanan (Dashboard Utama) -->
        <div class="main-content">
            <!-- [SINTAKS HTML]: Inline Flexbox Header UI | Merapatkan profile dan teks Salam di sisi saling berseberangan (Justify Space Between) -->
            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Quick Access</h1>
                    <p style="color:#475569; margin:5px 0 0 0; font-size:14px;">Kapasitas Parkir Real-time</p>
                </div>
                
                <!-- [SINTAKS HTML]: Inline Layouting Widget Profile HUD Info -->
                <div style="display:flex; align-items:center; gap:15px;">
                    <div style="text-align:right">
                        <!-- [SINTAKS PHP]: $_SESSION['nama'] | Mencetak nama profil riil hasil Auth session dinamis dari login -->
                        <div style="font-size:14px; font-weight:800; color:#0f172a;"><?= $_SESSION['nama'] ?></div>
                        <div style="font-size:11px; color:#475569; font-weight:600;">Petugas Level</div>
                    </div>
                    <!-- [SINTAKS PHP]: substr() Inline | Memotong karakter profil session untuk mengambil HURUF AWAL/Inisial sebagai avatar icon -->
                    <div style="width:45px; height:45px; background:#2563eb; border-radius:14px; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);">
                        <?= substr($_SESSION['nama'], 0, 1) ?>
                    </div>
                </div>
            </div>

            <!-- [SINTAKS HTML]: <div> Wrapper Area Grid Kolom Matrix Layouting Lahan Master Area -->
            <div class="area-grid">
                <?php
                // [SINTAKS PHP]: mysqli_query() Eksekusi Dasar | Memilih, memanggil keseluruhan Row dari tabel Master Data Lokasi Gedung/Slot Parkir
                $q_area = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");
                
                // [SINTAKS PHP]: Perulangan While Looping | Memutar skrip ini berulang kali melahirkan blok-blok tampilan DOM HTML sebanyak Jumlah Area di Database
                while($area = mysqli_fetch_assoc($q_area)) {
                    // [SINTAKS PHP]: Penetapan stok lahan | Mencatat kapasitas dinamis lahan dari record per-Looping (cth: Lantai 1 dikirim Kapasitas 50, Lantai 2 ada 35 dsb.)
                    $sisa = $area['kapasitas']; 
                ?>
                <!-- [SINTAKS HTML]: <div class area-card> | Kartu Satuan List Area -->
                <div class="area-card">
                    <!-- [SINTAKS PHP]: Inline Echo (=) | Memuntahkan rekaman text record Nama_Area per Looping ini -->
                    <h3><?= $area['nama_area'] ?></h3>
                    <!-- [SINTAKS PHP]: Inline Echo (=) | Merender Maks Kapasitas -->
                    <div style="font-size:11px; color:#475569; font-weight:700; margin-top:5px;">MAKS: <?= $area['kapasitas'] ?></div>
                    <!-- [SINTAKS PHP]: Inline Echo (=) | Merender Stock sisa real-time -->
                    <span class="count"><?= $sisa ?></span>
                    <div class="label">SLOT TERSEDIA</div>
                    <!-- [SINTAKS HTML]: <a> | Anchor pengirim ke formulir Transaksi Masuk dengan sisipan URL Payload ($_GET) agar form otomatis men-Select area yg dimaksud kasir -->
                    <a href="transaksi_masuk.php?area=<?= urlencode($area['nama_area']) ?>" class="btn-checkin">Check-In</a>
                </div>
                <?php 
                } // [SINTAKS PHP]: Akhir Blok Penutup Looping While Kartu Parkir 
                ?>
            </div>
        </div>
    </div>

</body>
</html>
