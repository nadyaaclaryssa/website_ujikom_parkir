
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/kelola_user.php
// -> Tujuan Spesifik: Halaman fungsional CRUD (Create, Read, Delete) data Petugas, Owner, dan Manajemen.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
if($_SESSION['role'] != "admin") { // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../../auth/index.php"); exit; }
// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

// Tambahan untuk mengambil data slot (agar sidebar sama dengan dashboard)
$kendaraan_masuk = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc(// [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

$error_msg = "";

if(isset(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['simpan'])){
    $nama = mysqli_real_escape_string($koneksi, trim(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['nama_lengkap']));
    $user = mysqli_real_escape_string($koneksi, trim(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['username']));
    $pass = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['password']);
    $role = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['role']); 
    
    $cek = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$user'");
    
    if(// [SINTAKS PHP]: mysqli_num_rows() | Menghitung dan mendapatkan jumlah total baris/records dari hasil eksekusi query SELECT
mysqli_num_rows($cek) > 0) {
        $error_msg = "Username '$user' terdeteksi sudah ada di sistem!"; 
    } else {
        $q = "INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')";
        $simpan = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, $q);
        
        if($simpan) {
            // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:kelola_user.php");
            exit;
        } else {
            $error_msg = "Gagal simpan! Error: " . mysqli_error($koneksi);
        }
    }
}

if(isset(// [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
$_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, // [SINTAKS PHP]: $_GET | Menangkap data atau parameter ID yang menempel/dikirim via URL (Misalnya dari link href)
$_GET['hapus']);
    // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user='$id'");
    // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:kelola_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parline - Kelola Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1d4ed8; --grad-1: #e0f2fe; --grad-2: #bae6fd; }
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%); display: flex; justify-content: center; align-items: center; }
        
        .app-container { width: 96%; height: 94vh; background: white; border-radius: 32px; display: flex; overflow: hidden; box-shadow: 0 40px 100px -20px rgba(0,0,0,0.1); }
        
        /* Sidebar Updated to Dashboard Style */
        .sidebar { width: 260px; background: #f1f5f9; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8); }
        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .logo-section img { width: 30px; height: 30px; border-radius: 8px; }
        .logo-section h2 { font-size: 18px; margin: 0; color: #0f172a; font-weight: 800; }
        
        .nav-menu { flex-grow: 1; }
        .nav-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 18px; text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600; margin-bottom: 5px; border-radius: 12px; transition: 0.3s; }
        .nav-menu a.active { background: #1d4ed8; color: white; box-shadow: 0 8px 15px -5px rgba(37,99,235,0.3); }

        .storage-sidebar { margin-top: auto; padding: 15px; background: #f1f5f9; border-radius: 15px; margin-bottom: 15px; }
        .storage-sidebar p { margin: 0 0 5px 0; font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        .progress-mini { height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; }
        .progress-fill { height: 100%; background: #1d4ed8; }

        .btn-logout-sidebar { color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; padding-left: 10px; }

        /* Main Content Style */
        .main-content { flex: 1; background: white; padding: 30px 40px; overflow-y: auto; }
        .section-title { font-size: 22px; color: #0f172a; margin: 0 0 20px 0; font-weight: 800; }
        
        .form-card { background: #f1f5f9; padding: 20px; border-radius: 20px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .grid-form { display: grid; grid-template-columns: 1.2fr 1fr 1fr 0.8fr 0.6fr; gap: 12px; align-items: end; }
        .input-group { display: flex; flex-direction: column; gap: 6px; }
        .input-group label { font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase; }
        .input-group input, .input-group select { padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 13px; outline: none; }
        
        .btn-simpan { background: #1d4ed8; color: white; border: none; padding: 11px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-simpan:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37,99,235,0.2); }
        
        .table-container { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 18px; color: #cbd5e1; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #f1f5f9; }
        td { padding: 12px 18px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
        
        .role-badge { padding: 3px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; }
        .role-ADMIN { background: #fee2e2; color: #b91c1c; }
        .role-PETUGAS { background: #dcfce7; color: #166534; }
        .role-OWNER { background: #fef9c3; color: #854d0e; }
        .btn-hapus { color: #ef4444; text-decoration: none; font-weight: 700; font-size: 11px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 15px; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
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
                <a href="kelola_user.php" class="active">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <a href="area_parkir.php">🕒 Data Area</a>
            </div>

            <div class="storage-sidebar">
                <p>SLOT TERISI</p>
                <div class="progress-mini">
                    <div class="progress-fill" style="width:<?= ($kendaraan_masuk/1350)*100 ?>%;"></div>
                </div>
                <p style="margin-top:5px; font-size:10px; color:#1e293b;"><?= $kendaraan_masuk ?> / 1350</p>
            </div>

            <a href="../../auth/logout.php" class="btn-logout-sidebar">🚪 Logout</a>
        </div>

        <div class="main-content">
            <h1 class="section-title">Kelola Pengguna</h1>

            <?php if($error_msg != ""): ?>
                <div class="alert-error">⚠️ <?= $error_msg ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" class="grid-form">
                    <div class="input-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" placeholder="Nama..." required></div>
                    <div class="input-group"><label>Username</label><input type="text" name="username" placeholder="Username..." required></div>
                    <div class="input-group"><label>Password</label><input type="password" name="password" placeholder="•••••" required></div>
                    <div class="input-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn-simpan">Simpan</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Nama Lengkap</th><th>Username</th><th>Role</th><th style="text-align: right;">Aksi</th></tr></thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "SELECT * FROM tb_user ORDER BY id_user DESC");
                        while($data = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <td>#<?= $no++ ?></td>
                            <td style="font-weight: 700;"><?= $data['nama_lengkap'] ?></td>
                            <td>@<?= $data['username'] ?></td>
                            <td><span class="role-badge role-<?= // [SINTAKS PHP]: strtoupper() | Fungsi konversi string, mengubah seluruh string plat nomor murni jadi HURUF KAPITAL (Uppercase)
strtoupper($data['role']) ?>"><?= // [SINTAKS PHP]: strtoupper() | Fungsi konversi string, mengubah seluruh string plat nomor murni jadi HURUF KAPITAL (Uppercase)
strtoupper($data['role']) ?></span></td>
                            <td style="text-align: right;"><a href="?hapus=<?= $data['id_user'] ?>" class="btn-hapus" onclick="return confirm('Hapus?')">Hapus</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

