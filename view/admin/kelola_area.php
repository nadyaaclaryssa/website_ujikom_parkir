<?php
session_start();
if($_SESSION['role'] != "admin") { header("location:../../auth/index.php"); exit; }
include '../../config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Area - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #eff6ff; padding: 20px; margin: 0; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 14px; }
        .btn-hapus { color: #ef4444; font-weight: bold; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: left; font-size: 14px; }
        th { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">📍 Kelola Area Parkir</h2>
            <a href="dashboard.php" style="color: #475569; text-decoration: none; font-weight: bold;">← Kembali</a>
        </div>
        <a href="area_tambah.php" class="btn">+ Tambah Area</a>
        
        <table>
            <tr><th>No</th><th>Nama Area</th><th>Kapasitas Total</th><th>Terisi</th><th>Aksi</th></tr>
            <?php
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir");
            while($d = mysqli_fetch_assoc($query)){
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><strong><?= $d['nama_area']; ?></strong></td>
                <td><?= $d['kapasitas']; ?> Kendaraan</td>
                <td><?= $d['terisi']; ?></td>
                <td><a href="area_hapus.php?id=<?= $d['id_area']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus area ini?')">Hapus</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>/