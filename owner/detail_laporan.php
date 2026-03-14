<?php
session_start();
if($_SESSION['role'] != "owner") { header("location:../index.php"); exit; }
include '../config/koneksi.php';

// Ambil data laporan (Hanya yang statusnya 'keluar' agar ada biaya_total-nya)
$query = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE status='keluar' ORDER BY waktu_keluar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts Owner - Detail Laporan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6; 
            --bg: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
            --text-dark: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { margin: 0; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 15px; }

        .app-container {
            width: 100%; max-width: 1250px; height: 90vh; 
            background: var(--white); border-radius: 40px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.15);
        }

        /* Sidebar - Samain persis sama Dashboard */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid #f1f5f9;
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 20px; color: var(--text-dark); margin: 0; font-weight: 800; }

        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: var(--text-light); font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        .nav-menu a.active { background: var(--primary); color: white; box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; overflow-y: auto; background: #fcfdfe; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: var(--text-dark); margin: 0; font-weight: 800; }

        /* Table Container Modern */
        .table-container {
            background: white; border-radius: 24px; border: 1px solid #f1f5f9;
            overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; }
        thead th { 
            padding: 18px 25px; font-size: 12px; font-weight: 700; 
            color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;
        }

        tbody tr { border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 18px 25px; font-size: 14px; color: var(--text-dark); }
        
        .badge-money {
            background: #dcfce7; color: #166534; padding: 6px 12px;
            border-radius: 10px; font-weight: 700; font-size: 13px;
        }

        .badge-type {
            background: #eff6ff; color: #1e40af; padding: 4px 10px;
            border-radius: 8px; font-size: 12px; font-weight: 600;
        }

        .btn-print {
            background: var(--text-dark); color: white; border: none;
            padding: 12px 20px; border-radius: 14px; font-weight: 700; cursor: pointer;
        }

        /* Scrollbar biar rapi */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../hogwarts-removebg-preview.png" width="35">
                <h2>Parline</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="laporan_detail.php" class="active">📜 Detail Laporan</a>
            </div>
            <a href="../logout.php" style="margin-top:auto; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Detail Laporan</h1>
                    <p style="color:var(--text-light); margin:5px 0 0; font-size:14px;">Seluruh data transaksi keluar dari brankas Gringotts.</p>
                </div>
                <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($data = mysqli_fetch_array($query)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= $data['plat_nomor']; ?></strong></td>
                            <td><span class="badge-type"><?= $data['jenis_kendaraan'] ?? 'Umum'; ?></span></td>
                            <td style="color: var(--text-light); font-size: 13px;"><?= $data['waktu_masuk']; ?></td>
                            <td style="color: var(--text-light); font-size: 13px;"><?= $data['waktu_keluar']; ?></td>
                            <td>
                                <span class="badge-money">
                                    Rp <?= number_format($data['biaya_total'], 0, ',', '.'); ?>
                                </span>
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