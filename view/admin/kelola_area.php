<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/kelola_area.php
// -> Tujuan Spesifik: Modul CRUD Admin tingkat dasar (Simple List Display) untuk mengelola data nyata (Real Data DB) Kapasitas Blok Zona Parkir yang terdaftar di Sistem.
// ======================================

// [SINTAKS PHP]: session_start | Mengikat memori jejak otentikasi User
session_start();

// [SINTAKS PHP]: Sistem Penjaring Akses (If Statement) | Blokade mutlak menolak peran Petugas Pos & Owner mampir kemari.
if($_SESSION['role'] != "admin") { 
    // [SINTAKS PHP]: Redirect Lempar Pental ke depan
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Ekstensi Pembuka Gerbang Jembatan Database MySQL
include '../../config/koneksi.php';
?>

<!-- [SINTAKS HTML]: Set Rangka dasar web -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Area - Admin</title>
    <!-- [SINTAKS HTML]: Referensi Font Eksternal API Google -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: Desain Lembar Styling Terpadu secara Ringkas Inline Page (Mini CMS Style) -->
    <style>
        /* [SINTAKS CSS]: Background Biru Es Tipis body */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #eff6ff; padding: 20px; margin: 0; }
        
        /* [SINTAKS CSS]: Kolom Laci Kotak Kertas Berada di Tengah membatasi lebar 1000px dengan bayang Soft */
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        /* [SINTAKS CSS]: Pill Button Dekorator class btn umum */
        .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 14px; }
        
        /* [SINTAKS CSS]: Warna teks font aksi Merah Berbahaya untuk Hapus Delete */
        .btn-hapus { color: #ef4444; font-weight: bold; text-decoration: none; }
        
        /* [SINTAKS CSS]: Komponen Lebar 100 persen Tabel Standar tanpa sekat batas baris double collapse */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        
        /* [SINTAKS CSS]: Atribut Sel Kolom Padding ruang di selang-seling dgn garis solid bawah */
        th, td { padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: left; font-size: 14px; }
        th { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <!-- [SINTAKS HTML]: Baris Fleksibel Kontainer (Rata Kiri-Kanan / Space-between) untuk Judul Atas dan Tombol Back -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;"> Kelola Area Parkir</h2>
            <!-- [SINTAKS HTML]: Hyperlink Anchor (a) Tautan pintas balik pulang ke rute utama navigasi Admin -->
            <a href="dashboard.php" style="color: #475569; text-decoration: none; font-weight: bold;">← Kembali</a>
        </div>
        
        <!-- [SINTAKS HTML]: Action Button Create Add (C-RUD) Halaman Pengisi Form Data Tambah Blok Area Baru DB -->
        <a href="area_tambah.php" class="btn">+ Tambah Area</a>
        
        <!-- [SINTAKS HTML]: Tabel Frame Rangka Data -->
        <table>
            <!-- [SINTAKS HTML]: TR (Table Row) Baris Awal Pembentuk Judul Header List Kolom -->
            <tr><th>No</th><th>Nama Area</th><th>Kapasitas Total</th><th>Terisi</th><th>Aksi</th></tr>
            
            <?php
            // [SINTAKS PHP]: Indexer Increment (Nomor urut Buatan Manual biar estetik daripada pakai Primary key lompat-lompat id SQL)
            $no = 1;
            
            // [SINTAKS PHP]: mysqli_query Fetcher Semua Data dari Label tb_area_parkir mentah Real Tanpa Filter
            $query = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");
            
            // [SINTAKS PHP]: While Loop | Putar baling-baling pemecah array Fetch_Assoc membuahkan baris Tabel Terpisah secara bergantian sejumlah data yang terekam MySQL
            while($d = mysqli_fetch_assoc($query)){
            ?>
            <!-- [SINTAKS HTML]: Konstruksi TR (Table Row) Konten Inti untuk masing-masing Record yang dilempar Loop While Loop DB PHP -->
            <tr>
                <!-- [SINTAKS PHP]: Inline Echo Auto Increment Nomor (++ Tambah Satu Setelah Nulis) -->
                <td><?= $no++; ?></td>
                
                <!-- [SINTAKS PHP]: Echoing Kolom Identifier Nama Blok/Lantai -->
                <td><strong><?= $d['nama_area']; ?></strong></td>
                
                <!-- [SINTAKS PHP]: Echoing Kolom Batas Daya Tampung slot terkonstruksi -->
                <td><?= $d['kapasitas']; ?> Kendaraan</td>
                
                <!-- [SINTAKS PHP]: Echoing Kolom yg merekam slot aktual termakan -->
                <td><?= $d['terisi']; ?></td>
                
                <!-- [SINTAKS HTML/JS]: Anchor URL Hapus dengan lemparan Parameter "?id=" melalui Metode GET Route URL (Akan ditangkap mesin Penghancur area_hapus.php). Dipadu dengan event onclick() konfirmasi JS ringan biar ga kepencet ga sengaja -->
                <td><a href="area_hapus.php?id=<?= $d['id_area']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus area ini?')">Hapus</a></td>
            </tr>
            <?php 
            } // [SINTAKS PHP]: Break Closing kurung siku penutup batas akhir Loop While Array
            ?>
        </table>
    </div>
</body>
</html>