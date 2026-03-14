<?php
session_start();
if($_SESSION['role'] != "admin") { header("location:../index.php"); exit; }
include '../config/koneksi.php';

// Simulasi data area (Nanti bisa kamu ambil dari database)
$areas = [
    ['nama' => 'Lantai 1', 'lokasi' => 'BLOK UTAMA', 'total' => 50, 'terisi' => 4],
    ['nama' => 'Blok A - Depan', 'lokasi' => 'BLOK LOKASI', 'total' => 50, 'terisi' => 7],
    ['nama' => 'Blok B - Samping', 'lokasi' => 'BLOK LOKASI', 'total' => 60, 'terisi' => 2],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hogwarts - Area Parkir</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --grad-1: #d4e9f7; 
            --grad-2: #b2d7f5;
            --success: #10b981;
        }

        /* Full Screen Locking */
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }

        body { 
            background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%);
            display: flex; justify-content: center; align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Main App Container */
        .app-container {
            width: 96%; height: 94vh;
            background: white; border-radius: 40px;
            display: flex; overflow: hidden;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar Identik */
        .sidebar {
            width: 280px; background: #fcfdfe;
            padding: 40px 25px; display: flex; flex-direction: column;
            border-right: 1px solid #f0f4f8;
        }

        .logo-section { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; padding-left: 10px; }
        .logo-section img { width: 40px; height: 40px; border-radius: 10px; }
        .logo-section h2 { font-size: 22px; margin: 0; color: #1e293b; font-weight: 800; }

        .nav-menu { flex-grow: 1; }
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            text-decoration: none; color: #94a3b8; font-size: 15px; font-weight: 600;
            margin-bottom: 8px; border-radius: 18px; transition: 0.3s;
        }
        .nav-menu a.active { background: var(--primary); color: white; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #1e293b; }

        /* Content Area */
        .main-content { flex: 1; background: white; padding: 40px 50px; overflow-y: auto; }

        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .section-title { font-size: 28px; color: #1e293b; margin: 0; font-weight: 800; }

        .area-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .area-card {
            background: white; border-radius: 30px; padding: 30px;
            border: 1px solid #f1f5f9; transition: 0.3s;
            position: relative;
        }
        .area-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); }

        .status-badge {
            position: absolute; top: 30px; right: 30px;
            background: #dcfce7; color: var(--success);
            padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800;
        }

        .area-name { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 5px 0; }
        .area-loc { color: #94a3b8; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 5px; margin-bottom: 25px; }

        .stat-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .stat-label { font-size: 13px; color: #64748b; font-weight: 600; }
        .stat-value { font-size: 14px; font-weight: 800; color: #1e293b; }
        .stat-value.available { color: var(--success); }

        .progress-container {
            height: 10px; background: #f1f5f9; border-radius: 20px;
            overflow: hidden; margin: 15px 0;
        }
        .progress-bar { height: 100%; background: var(--primary); border-radius: 20px; transition: 1s ease-in-out; }

        .perc-label { text-align: right; font-size: 11px; color: #94a3b8; font-weight: 700; }
        .avatar { width: 40px; height: 40px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; }
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
                <a href="kelola_user.php">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <a href="area_parkir.php" class="active">🕒 Data Area</a>
                </div>
            
            <a href="../logout.php" style="margin-top: auto; color: #94a3b8; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top">
                <h1 class="section-title">Informasi Area</h1>
                <div style="display: flex; align-items: center; gap: 15px; border-left: 1px solid #f1f5f9; padding-left: 20px;">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 14px; color: #1e293b;">Admin Hogwarts</div>
                        <div style="font-size: 11px; color: #94a3b8;">Gringotts Level</div>
                    </div>
                    <div class="avatar">H</div>
                </div>
            </div>

            <div class="area-grid">
                <?php foreach($areas as $a): 
                    $persen = ($a['terisi'] / $a['total']) * 100;
                    $sisa = $a['total'] - $a['terisi'];
                ?>
                <div class="area-card">
                    <span class="status-badge">TERSEDIA</span>
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

                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?= $persen ?>%;"></div>
                    </div>
                    
                    <div class="perc-label">Terisi: <?= round($persen) ?>%</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>
</html>