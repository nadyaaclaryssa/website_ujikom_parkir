<?php
session_start();
if($_SESSION['role'] != "petugas") { header("location:../index.php"); exit; }
include '../config/koneksi.php';

$kendaraan_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE status='masuk'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts Petugas - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3b82f6; 
            --primary-hover: #2563eb;
            --bg-gradient: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); 
            --text-main: #1e293b;
            --text-sub: #64748b;
            --white: #ffffff;
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            margin: 0; 
            background: var(--bg-gradient); 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 15px;
        }

        .app-container {
            width: 100%; max-width: 1200px; height: 85vh; 
            background: var(--white); border-radius: 40px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.15);
        }

        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid #f1f5f9;
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 20px; color: var(--text-main); font-weight: 800; margin: 0; }

        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: var(--text-sub); font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        .nav-menu a.active { 
            background: var(--primary-blue); color: var(--white); 
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
        .nav-menu a:hover:not(.active) { background: #f8fafc; color: var(--primary-blue); }

        .main-content {
            flex: 1; background: #fcfdfe; padding: 40px 50px; overflow-y: auto;
        }

        .header-top h1 { font-size: 24px; color: var(--text-main); font-weight: 800; margin: 0; }

        /* Grid Kolom Area - Ukuran minmax dikecilkan agar lebih rapat */
        .area-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px; 
            margin-top: 30px;
        }

        .area-card {
            background: var(--white);
            padding: 20px 15px; /* Padding dikecilkan */
            border-radius: 25px; /* Radius disesuaikan dengan ukuran kecil */
            border: 1px solid #f1f5f9; text-align: center;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        
        .area-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-blue);
        }
        
        .area-card h3 { font-size: 15px; color: var(--text-main); margin: 0; font-weight: 800; }
        
        .area-card .count { 
            font-size: 42px; /* Angka dikecilkan dari 56px */
            font-weight: 800; 
            color: #ef4444; 
            margin: 10px 0; /* Margin dirampingkan */
            display: block;
        }
        
        .area-card .label { 
            font-size: 9px; /* Label diperkecil */
            font-weight: 800; 
            color: var(--text-sub); 
            letter-spacing: 0.5px; 
            text-transform: uppercase;
        }

        .btn-checkin {
            background: #22c55e; color: var(--white); text-decoration: none;
            padding: 8px 25px; /* Button lebih pendek dan ramping */
            border-radius: 12px; 
            font-size: 11px; /* Font button dikecilkan */
            font-weight: 700; 
            display: inline-block; 
            margin-top: 15px; /* Jarak atas dikurangi */
            transition: 0.3s;
        }
        .btn-checkin:hover { background: #16a34a; transform: scale(1.05); }

        .storage-box {
            margin-top: auto; padding: 20px; background: #f8fafc; border-radius: 20px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../hogwarts-removebg-preview.png" width="38">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="active">🏠 Dashboard</a>
                <a href="transaksi_masuk.php">📥 Transaksi Masuk</a>
                <a href="transaksi_keluar.php">📤 Transaksi Keluar</a>
            </div>

            <div class="storage-box">
                <p style="margin:0 0 10px 0; font-size:10px; font-weight:800; color:var(--text-sub); letter-spacing:0.5px;">STORAGE DETAILS</p>
                <div style="height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:12px;">
                    <div style="width: <?= ($kendaraan_masuk/1350)*100 ?>%; height:100%; background:var(--primary-blue);"></div>
                </div>
                <p style="margin:0; font-size:13px; font-weight:800; color:var(--text-main);">Slot: <?= $kendaraan_masuk ?> <span style="color:#94a3b8; font-weight:600;">/ 1350</span></p>
            </div>
            
            <a href="../logout.php" style="margin-top:25px; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Quick Access</h1>
                    <p style="color:var(--text-sub); margin:5px 0 0 0; font-size:14px;">Kapasitas Parkir Real-time</p>
                </div>
                <div style="display:flex; align-items:center; gap:15px;">
                    <div style="text-align:right">
                        <div style="font-size:14px; font-weight:800; color:var(--text-main);"><?= $_SESSION['nama'] ?></div>
                        <div style="font-size:11px; color:var(--text-sub); font-weight:600;">Petugas Level</div>
                    </div>
                    <div style="width:45px; height:45px; background:var(--primary-blue); border-radius:14px; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);">
                        <?= substr($_SESSION['nama'], 0, 1) ?>
                    </div>
                </div>
            </div>

            <div class="area-grid">
                <?php
                $q_area = mysqli_query($koneksi, "SELECT * FROM tb_area");
                while($area = mysqli_fetch_assoc($q_area)) {
                    $sisa = $area['kapasitas']; 
                ?>
                <div class="area-card">
                    <h3><?= $area['nama_area'] ?></h3>
                    <div style="font-size:11px; color:var(--text-sub); font-weight:700; margin-top:5px;">MAKS: <?= $area['kapasitas'] ?></div>
                    <span class="count"><?= $sisa ?></span>
                    <div class="label">SLOT TERSEDIA</div>
                    <a href="transaksi_masuk.php?area=<?= urlencode($area['nama_area']) ?>" class="btn-checkin">Check-In</a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

</body>
</html>