
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/tarif_parkir.php
// -> Tujuan Spesifik: Modul/komponen fungsional spesifik aplikasi Smart Parking System.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
if($_SESSION['role'] != "admin") { // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../../auth/index.php"); exit; }
// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

// Ambil data kendaraan masuk untuk progress bar di sidebar
$kendaraan_masuk = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc(// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

// Proses Simpan Tarif
if(isset(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['tambah'])){
    $jenis = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['jenis_kendaraan']);
    $harga = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['harga_per_jam']);
    // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "INSERT INTO tb_tarif (jenis_kendaraan, harga_per_jam) VALUES ('$jenis', '$harga')");
    // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:tarif_parkir.php");
}

// Proses Hapus
if(isset(// [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
$_GET['hapus'])){
    $id = // [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
$_GET['hapus'];
    // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "DELETE FROM tb_tarif WHERE id_tarif='$id'");
    // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:tarif_parkir.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts - Data Tarif</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1d4ed8;
            --grad-1: #e0f2fe; 
            --grad-2: #bae6fd;
        }

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

        .app-container {
            width: 100%;
            max-width: 1400px;
            height: 92vh;
            background: white;
            border-radius: 32px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08);
        }

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
            box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25);
        }
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #0f172a; }

        .storage-box {
            margin-top: auto;
            padding: 25px;
            background: #f1f5f9;
            border-radius: 24px;
        }

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

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 28px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .grid-form { display: grid; grid-template-columns: 1fr 1fr auto; gap: 20px; align-items: end; }
        .input-group label { display: block; font-size: 11px; font-weight: 800; color: #cbd5e1; margin-bottom: 10px; text-transform: uppercase; }
        .input-group input { width: 100%; padding: 15px 20px; border-radius: 18px; border: 1px solid #e2e8f0; background: #f1f5f9; outline: none; font-size: 14px; }
        
        .btn-tambah { 
            background: #1d4ed8; color: white; border: none; padding: 16px 30px; 
            border-radius: 18px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); }

        .table-card {
            background: white;
            padding: 30px;
            border-radius: 32px;
            border: 1px solid #e2e8f0;
        }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #cbd5e1; font-size: 11px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
        td { padding: 22px 15px; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; }

        .price-tag { 
            font-weight: 800; color: #0f172a; background: #f1f5f9; 
            padding: 8px 15px; border-radius: 12px; border: 1px solid #e2e8f0;
        }
        .btn-hapus { color: #ef4444; text-decoration: none; font-weight: 700; font-size: 13px; }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="kelola_user.php">👥 Data User</a>
                <a href="tarif_parkir.php" class="active">📂 Data Tarif</a>
                <a href="area_parkir.php">🕒 Data Area</a>
                </div>

            <div class="storage-box">
                <p style="margin: 0 0 12px 0; color: #64748b; font-size: 10px; font-weight: 800;">STORAGE DETAILS</p>
                <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height: 100%; background: #1d4ed8;"></div>
                </div>
                <p style="margin: 0; font-weight: 700; font-size: 13px; color: #0f172a;">Slot: <?= $kendaraan_masuk ?> / 1350</p>
            </div>
            
            <a href="../../auth/logout.php" style="margin-top: 25px; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top">
                <div class="page-info">
                    <h1>Data Tarif Parkir</h1>
                    <p>Atur biaya parkir Gringotts Vault</p>
                </div>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Admin Hogwarts</div>
                        <div style="font-size: 11px; color: #64748b;">Gringotts Level</div>
                    </div>
                    <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 12px; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800;">H</div>
                </div>
            </div>

            <div class="form-card">
                <form action="" method="POST" class="grid-form">
                    <div class="input-group">
                        <label>Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" placeholder="Contoh: NAGA" required>
                    </div>
                    <div class="input-group">
                        <label>Harga Per Jam</label>
                        <input type="number" name="harga_per_jam" placeholder="5000" required>
                    </div>
                    <button type="submit" name="tambah" class="btn-tambah">Tambah Tarif</button>
                </form>
            </div>

            <div class="table-card">
                <table>
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
                        $q = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT * FROM tb_tarif");
                        while($data = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: #cbd5e1;">#<?= $data['id_tarif'] ?></td>
                            <td style="font-weight: 700; color: #0f172a;"><?= // [SINTAKS PHP]: strtoupper() | Fungsi konversi string, mengubah seluruh string plat nomor murni jadi HURUF KAPITAL (Uppercase)
strtoupper($data['jenis_kendaraan']) ?></td>
                            <td>
                                <span class="price-tag">
                                    <?php 
                                        $nominal = $data['harga_per_jam'] ?? $data['harga'] ?? 0;
                                        echo "Rp " . number_format($nominal, 0, ',', '.'); 
                                    ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="?hapus=<?= $data['id_tarif'] ?>" class="btn-hapus" onclick="return confirm('Hapus tarif ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

