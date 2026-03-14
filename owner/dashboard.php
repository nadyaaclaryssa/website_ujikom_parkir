<?php
session_start();
if($_SESSION['role'] != "owner") { header("location:../index.php"); exit; }
include '../config/koneksi.php';

// Data Ringkasan - Tetap dipertahankan agar tidak error
$hari_ini = date('Y-m-d');
$bulan_ini = date('Y-m');

$pendapatan_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$hari_ini'"))['total'] ?? 0;
$pendapatan_bulan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE_FORMAT(waktu_keluar, '%Y-%m') = '$bulan_ini'"))['total'] ?? 0;
$unit_keluar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$hari_ini'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parline - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #3b82f6; 
            --royal-blue: #1e40af;
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
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
            width: 100%; max-width: 1250px; height: 90vh; 
            background: var(--white); border-radius: 40px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.12);
        }

        /* Sidebar Identik dengan Admin & Petugas */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid #f1f5f9;
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 18px; color: var(--text-main); font-weight: 800; margin: 0; }

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

        /* Main Content Area */
        .main-content { flex: 1; padding: 40px; overflow-y: auto; background: #fcfdfe; }

        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .header-top h1 { font-size: 24px; color: var(--text-main); font-weight: 800; margin: 0; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        
        .stat-card {
            background: var(--white); padding: 25px; border-radius: 24px;
            border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover { transform: translateY(-5px); }

        .stat-card.highlight { 
            background: var(--primary-blue); color: white; border: none; 
            box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.4);
        }

        .stat-card label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; }
        .stat-card h2 { font-size: 24px; margin: 10px 0 5px; font-weight: 800; }
        .stat-card p { font-size: 12px; margin: 0; opacity: 0.7; }

        /* Chart Section */
        .chart-container {
            background: var(--white); padding: 30px; border-radius: 30px;
            border: 1px solid #f1f5f9; margin-bottom: 20px;
        }

        .btn-report {
            background: var(--text-main); color: white; border: none;
            padding: 12px 22px; border-radius: 14px; font-weight: 700;
            font-size: 13px; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        
        .btn-report:hover { background: #000; transform: translateY(-2px); }

        /* Scrollbar styling */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../hogwarts-removebg-preview.png" width="35" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="active">🏠 Dashboard</a>
                <a href="detail_laporan.php">📜 Detail Laporan</a>
            </div>

            <a href="../logout.php" style="margin-top:auto; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top">
                <div>
                    <h1>Dashboard Pemilik</h1>
                    <p style="color:var(--text-sub); margin:5px 0 0; font-size:14px;">Pantau data keuangan Gringotts hari ini.</p>
                </div>
                <button class="btn-report" onclick="window.print()">
                    <span>🖨️</span> Cetak Laporan Harian
                </button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <label>Pendapatan Hari Ini</label>
                    <h2 style="color: var(--primary-blue);">Rp <?= number_format($pendapatan_hari, 0, ',', '.') ?></h2>
                    <p>Berdasarkan data keluar hari ini</p>
                </div>
                
                <div class="stat-card highlight">
                    <label>Pendapatan Bulan Ini</label>
                    <h2>Rp <?= number_format($pendapatan_bulan, 0, ',', '.') ?></h2>
                    <p>Total pendapatan brankas bulan ini</p>
                </div>
                
                <div class="stat-card">
                    <label>Total Kendaraan Keluar</label>
                    <h2 style="color: var(--text-main);"><?= $unit_keluar ?> <span style="font-size: 14px; color: var(--text-sub);">Unit</span></h2>
                    <p>Traffic keluar hari ini</p>
                </div>
            </div>

            <div class="chart-container">
                <h3 style="margin: 0 0 25px 0; font-size: 16px; color: var(--text-main);">📈 Tren Pendapatan</h3>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [1200000, 1900000, 1500000, <?= $pendapatan_hari ?>, 0, 0, 0],
                    backgroundColor: '#3b82f6',
                    borderRadius: 12,
                    barThickness: 35
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' }, 
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } 
                    },
                    x: { 
                        grid: { display: false }, 
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } 
                    }
                }
            }
        });
    </script>
</body>
</html>