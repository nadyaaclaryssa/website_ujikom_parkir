<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/tarif_parkir.php
// -> Tujuan Spesifik: Modul Manajer Harga untuk membedakan nominal biaya parkir antara Motor, Mobil, dsb.
// ======================================

// [SINTAKS PHP]: session_start() | Membuka kunci state memori browser agar data login User/Admin bisa dibaca terus di tiap halaman
session_start();

// [SINTAKS PHP]: Validasi Role Privilege | Menendang mundur user yang status posisinya bukan admin utama
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: header location | Pengalihan otomatis via manipulasi Header HTTP tanpa nunggu reload
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: include | Tarik Mesin Koneksi MySQL
include '../../config/koneksi.php';

// [SINTAKS PHP]: Query Meteran Kapasitas | Ambil data jumlah mobil/motor yang masih tertahan di dalem belum CheckOut (status masuk) buat modal ngisi Persentase UI Sidebar
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

// ==== BLOK LOGIKA: TAMBAH (CREATE) TARIF BARU ====
// [SINTAKS PHP]: isset($_POST['tambah']) | Perangkap Logika yang aktif HANYA JIKA ada sinyal tombol submit bernama "tambah" dipencet dari Form
if(isset($_POST['tambah'])){
    // [SINTAKS PHP]: Sanitasi Input 1 | Menggurah string liar SQL Injection dari Input Jenis Kendaraan
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis_kendaraan']);
    
    // [SINTAKS PHP]: Coalesce Fallback Operator (??) Berganda | Mengantisipasi perbedaan Penamaan Name atribut HTML 'tarif_per_jam' atau 'harga_per_jam'. Kalau kosong ya nilainya 0 rupiah mutlak.
    $harga_input = $_POST['tarif_per_jam'] ?? $_POST['harga_per_jam'] ?? 0;
    
    // [SINTAKS PHP]: Sanitasi Input 2
    $harga = mysqli_real_escape_string($koneksi, $harga_input);
    
    // [SINTAKS PHP]: Tembakan SQL Insert | Memompakan Value harga dan Value Jenis kendaran ke Kantong Tabel tb_tarif Database
    mysqli_query($koneksi, "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis', '$harga')");
    
    // [SINTAKS PHP]: Segarkan halaman pasca-input untuk menghapus memori Cache form yang ngegantung di browser
    header("location:tarif_parkir.php");
}

// ==== BLOK LOGIKA: HAPUS TARIF LAMA ====
// [SINTAKS PHP]: isset($_GET['hapus']) | Pendeteksi Tembakan URL Query Parameter / URL Payload (?hapus=xxx) 
if(isset($_GET['hapus'])){
    // [SINTAKS PHP]: Menyerap ID Tarif yang disembunyikan dibalik tombol Hapus Merah
    $id = $_GET['hapus'];
    
    // [SINTAKS PHP]: Perintah SQL Pemusnah Instan | DELETE Record sebaris utuh berdaskan kunci ID yang sesuai
    mysqli_query($koneksi, "DELETE FROM tb_tarif WHERE id_tarif='$id'");
    
    // [SINTAKS PHP]: Refresh membersihkan sisa Teks "?hapus=xxx" di batang Address Bar URL atas.
    header("location:tarif_parkir.php");
}
?>

<!-- [SINTAKS HTML]: Templat Dasar Laman Web -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts - Data Tarif</title>
    <!-- [SINTAKS HTML]: API Jemput Font Plus Jakarta Sans Modern -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* [SINTAKS CSS]: Variabel Warna Induk Biru Laut Khas Menu Admin */
        :root {
            --primary: #1d4ed8;
            --grad-1: #e0f2fe; 
            --grad-2: #bae6fd;
        }

        /* [SINTAKS CSS]: Reset Padding Standar Universal (*) CSS */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            margin: 0; 
            background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* [SINTAKS CSS]: Kotak Bingkai Layar Putih Utama radius besar lebar maksimum PC 1400px */
        .app-container {
            width: 100%;
            max-width: 1400px;
            height: 92vh;
            background: white;
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08); /* Aksen Bayangan Terjun Drop Shadow Premium */
        }

        /* [SINTAKS CSS]: Kolom Navigasi Tetap Kiri */
        .sidebar {
            width: 280px;
            background: white;
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0 10px;
            margin-bottom: 40px;
        }
        .logo-section img { width: 45px; height: 45px; border-radius: 12px; }
        .logo-section h2 { font-size: 20px; margin: 0; color: #0f172a; font-weight: 800; }

        /* [SINTAKS CSS]: Konfigurasi Panel Tab Anchor links flex vertikal */
        .nav-menu { flex-grow: 1; }
        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            text-decoration: none;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            border-radius: 18px;
            transition: 0.3s;
        }
        .nav-menu a.active {
            background: #1d4ed8;
            color: white;
            box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25); /* Menyolokan Tab yang lagi diakses menyala biru */
        }
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #0f172a; }

        /* [SINTAKS CSS]: Blok Indikator Informasi Storage Kendaraan terdampar di Pantat flex */
        .storage-box {
            margin-top: auto;
            padding: 25px;
            background: #f1f5f9;
            border-radius: 24px;
        }

        /* [SINTAKS CSS]: Hamparan Area Eksekusi Modul Utama abu lembut dengan scrolling y */
        .main-content {
            flex: 1;
            background: #f1f5f9;
            padding: 40px 50px;
            overflow-y: auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .page-info h1 { font-size: 24px; color: #0f172a; margin: 0; font-weight: 800; }
        .page-info p { color: #64748b; margin: 5px 0 0 0; font-size: 14px; }

        /* [SINTAKS CSS]: Permak Papan Formulir Tambah Data (Warna Putih border halusan) */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 28px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        
        /* [SINTAKS CSS]: Custom CSS GRID memotong form jadi baris horizontal terbagi fractional (fr) dengan celah kosong berimbang */
        .grid-form { display: grid; grid-template-columns: 1fr 1fr auto; gap: 20px; align-items: end; }
        .input-group label { display: block; font-size: 11px; font-weight: 800; color: #cbd5e1; margin-bottom: 10px; text-transform: uppercase; }
        
        /* [SINTAKS CSS]: Styling Input Field membulat melengkung tanpa garis tepi fokus default browser (outline none) */
        .input-group input { width: 100%; padding: 15px 20px; border-radius: 18px; border: 1px solid #e2e8f0; background: #f1f5f9; outline: none; font-size: 14px; }
        
        /* [SINTAKS CSS]: Gaya Tombol Submit biru */
        .btn-tambah { 
            background: #1d4ed8; color: white; border: none; padding: 16px 30px; 
            border-radius: 18px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); }

        /* [SINTAKS CSS]: Bungkus area Tabel History Data */
        .table-card {
            background: white;
            padding: 30px;
            border-radius: 32px;
            border: 1px solid #e2e8f0;
        }
        
        /* [SINTAKS CSS]: Memaksa garis antar Table Cell bersatu padu tak memisah */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #cbd5e1; font-size: 11px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
        td { padding: 22px 15px; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }

        /* [SINTAKS CSS]: Stempel penonjol Area Angka Uang Duit Harga Biar Nyata dipandang mata */
        .price-tag { 
            font-weight: 800; color: #0f172a; background: #f1f5f9; 
            padding: 8px 15px; border-radius: 12px; border: 1px solid #e2e8f0;
        }
        
        /* [SINTAKS CSS]: Tombol Link Aksi Hapus warna merah tulisan murni */
        .btn-hapus { color: #ef4444; text-decoration: none; font-weight: 700; font-size: 13px; }
    </style>
</head>
<body>

    <div class="app-container">
        <!-- [SINTAKS HTML]: Kompartemen Navigasi Lateral -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Impor Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php"> Dashboard</a>
                <a href="kelola_user.php"> Data User</a>
                <!-- [SINTAKS HTML]: Anchort Teks Disorot status Aktif (Warna Biru Tegas) -->
                <a href="tarif_parkir.php" class="active"> Data Tarif</a>
                <a href="area_parkir.php"> Data Area</a>
                <a href="log_aktivitas.php"> Log Aktivitas (Audit)</a>
            </div>

            <div class="storage-box">
                <p style="margin: 0 0 12px 0; color: #64748b; font-size: 10px; font-weight: 800;">STORAGE DETAILS</p>
                <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                    <!-- [SINTAKS PHP]: Modifikasi Inline Style Progres Panjang Lahan Parkir Biru -->
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height: 100%; background: #1d4ed8;"></div>
                </div>
                <p style="margin: 0; font-weight: 700; font-size: 13px; color: #0f172a;">Slot: <?= $kendaraan_masuk ?> / 1350</p>
            </div>
            
            <!-- [SINTAKS HTML]: Exit Door Session Destroyer (Keluar Laman) -->
            <a href="../../auth/logout.php" style="margin-top: 25px; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;"> Logout</a>
        </div>

        <div class="main-content">
            <!-- [SINTAKS HTML]: Grup Komponen Header Papan Nama modul Admin -->
            <div class="header-top">
                <div class="page-info">
                    <h1>Data Tarif Parkir</h1>
                    <p>Atur biaya parkir Titik Pos Pemeriksaan</p>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Admin Hogwarts</div>
                        <div style="font-size: 11px; color: #64748b;">Gringotts Level</div>
                    </div>
                    <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 12px; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800;">H</div>
                </div>
            </div>

            <!-- [SINTAKS HTML]: Papan UI Formulir Pengetikan Tipe & Harga Modal Tarif Parkir Anyar -->
            <div class="form-card">
                <!-- [SINTAKS HTML]: Action Kosong berarti akan ditembak POST Submitnya ke file PHP dirinya sendiri -->
                <form action="" method="POST" class="grid-form">
                    <div class="input-group">
                        <label>Jenis Kendaraan</label>
                        <!-- [SINTAKS HTML]: Wajib Isi Pengetikan Identifier (Mobil/Motor/Bus) -->
                        <input type="text" name="jenis_kendaraan" placeholder="Contoh: NAGA" required>
                    </div>
                    <div class="input-group">
                        <label>Harga Per Jam</label>
                        <!-- [SINTAKS HTML]: Number Tipe Inputan Spesifik Angka, Cegah Admin iseng nulis Huruf 'Lima Rebu' biar Query SQL ngga Error Crash -->
                        <input type="number" name="tarif_per_jam" placeholder="5000" required>
                    </div>
                    <button type="submit" name="tambah" class="btn-tambah">Tambah Tarif</button>
                </form>
            </div>

            <!-- [SINTAKS HTML]: Zona Susunan Tabel Data Mentah Relasi Dari Database MySQL -->
            <div class="table-card">
                <table>
                    <!-- [SINTAKS HTML]: THEAD Grup Kumpulan Judul Kolom Indeks atas -->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategori Kendaraan</th>
                            <th>Tarif / Jam</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // [SINTAKS PHP]: Query Tarik Bebas Tanpa Filter ORDER BY apapun seluruh Row isi Kantong Tabel Master Tarif DB
                        $q = mysqli_query($koneksi, "SELECT * FROM tb_tarif");
                        
                        // [SINTAKS PHP]: Pencacah Eksekusi Menjadi Row Berbaris-baris Selama list array Datanya Tak Berujung Habis
                        while($data = mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <!-- [SINTAKS PHP]: Tag Pagar Tanda '#' ID Database Mentah MySQL Asli -->
                            <td style="font-weight: 600; color: #cbd5e1;">#<?= $data['id_tarif'] ?></td>
                            
                            <!-- [SINTAKS PHP]: Print strtoupper uppercase Biar Namanya Terbaca Gagah Besar (ex: MOTOR, TRUK) -->
                            <td style="font-weight: 700; color: #0f172a;"><?= strtoupper($data['jenis_kendaraan']) ?></td>
                            
                            <td>
                                <!-- [SINTAKS HTML]: Lencana Pembungkus Visual Uang Rupiah Tampilan Minimalis Modern -->
                                <span class="price-tag">
                                    <?php 
                                        // [SINTAKS PHP]: Menengahi Konflik Inkonsistensi Penamaan Kolom Database Mahasiswa PKL (Kadang namain tarif_per_jam, kadang harga) dgn Coalesce Fallback ganda
                                        $nominal = $data['tarif_per_jam'] ?? $data['harga'] ?? 0;
                                        
                                        // [SINTAKS PHP]: Penitik Angka Ribuan Koma (Number_Format) Pemisah Angka Akuntansi Keuangan Rupiah
                                        echo "Rp " . number_format($nominal, 0, ',', '.'); 
                                    ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <!-- [SINTAKS HTML & JS]: Onclick Confirm JS Alert Box Penahan Klik Ngebangkang Menghindari Penghapusan Ga Sengaja di Tengah Hari -->
                                <a href="?hapus=<?= $data['id_tarif'] ?>" class="btn-hapus" onclick="return confirm('Hapus tarif ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php 
                        // [SINTAKS PHP]: Closing Batasan Looping While Berakhir
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
