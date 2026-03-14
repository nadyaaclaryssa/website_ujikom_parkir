<?php
session_start();
if($_SESSION['role'] != "admin") { header("location:../index.php"); exit; }
include '../config/koneksi.php';

// Tambahan untuk mengambil data slot (agar sidebar sama dengan dashboard)
$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;

$error_msg = "";

if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $user = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $pass = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']); 
    
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$user'");
    
    if(mysqli_num_rows($cek) > 0) {
        $error_msg = "Username '$user' terdeteksi sudah ada di sistem!"; 
    } else {
        $q = "INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')";
        $simpan = mysqli_query($koneksi, $q);
        
        if($simpan) {
            header("location:kelola_user.php");
            exit;
        } else {
            $error_msg = "Gagal simpan! Error: " . mysqli_error($koneksi);
        }
    }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user='$id'");
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
        :root { --primary: #2563eb; --grad-1: #d4e9f7; --grad-2: #b2d7f5; }
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%); display: flex; justify-content: center; align-items: center; }
        
        .app-container { width: 96%; height: 94vh; background: white; border-radius: 40px; display: flex; overflow: hidden; box-shadow: 0 40px 100px -20px rgba(0,0,0,0.1); }
        
        /* Sidebar Updated to Dashboard Style */
        .sidebar { width: 260px; background: #fcfdfe; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid #f0f4f8; }
        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .logo-section img { width: 30px; height: 30px; border-radius: 8px; }
        .logo-section h2 { font-size: 18px; margin: 0; color: #1e293b; font-weight: 800; }
        
        .nav-menu { flex-grow: 1; }
        .nav-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 18px; text-decoration: none; color: #94a3b8; font-size: 14px; font-weight: 600; margin-bottom: 5px; border-radius: 12px; transition: 0.3s; }
        .nav-menu a.active { background: var(--primary); color: white; box-shadow: 0 8px 15px -5px rgba(37,99,235,0.3); }

        .storage-sidebar { margin-top: auto; padding: 15px; background: #f1f5f9; border-radius: 15px; margin-bottom: 15px; }
        .storage-sidebar p { margin: 0 0 5px 0; font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
        .progress-mini { height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--primary); }

        .btn-logout-sidebar { color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; padding-left: 10px; }

        /* Main Content Style */
        .main-content { flex: 1; background: white; padding: 30px 40px; overflow-y: auto; }
        .section-title { font-size: 22px; color: #1e293b; margin: 0 0 20px 0; font-weight: 800; }
        
        .form-card { background: #f8fafc; padding: 20px; border-radius: 20px; margin-bottom: 25px; border: 1px solid #f1f5f9; }
        .grid-form { display: grid; grid-template-columns: 1.2fr 1fr 1fr 0.8fr 0.6fr; gap: 12px; align-items: end; }
        .input-group { display: flex; flex-direction: column; gap: 6px; }
        .input-group label { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        .input-group input, .input-group select { padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 13px; outline: none; }
        
        .btn-simpan { background: var(--primary); color: white; border: none; padding: 11px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-simpan:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37,99,235,0.2); }
        
        .table-container { background: white; border-radius: 20px; border: 1px solid #f1f5f9; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 18px; color: #cbd5e1; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #fcfdfe; }
        td { padding: 12px 18px; font-size: 13px; color: #475569; border-bottom: 1px solid #f8fafc; }
        
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
                <img src="../../hogwarts-removebg-preview.png" alt="Logo">
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

            <a href="../logout.php" class="btn-logout-sidebar">🚪 Logout</a>
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
                        $q = mysqli_query($koneksi, "SELECT * FROM tb_user ORDER BY id_user DESC");
                        while($data = mysqli_fetch_assoc($q)){
                        ?>
                        <tr>
                            <td>#<?= $no++ ?></td>
                            <td style="font-weight: 700;"><?= $data['nama_lengkap'] ?></td>
                            <td>@<?= $data['username'] ?></td>
                            <td><span class="role-badge role-<?= strtoupper($data['role']) ?>"><?= strtoupper($data['role']) ?></span></td>
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