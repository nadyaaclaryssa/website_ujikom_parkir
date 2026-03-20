<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/log_aktivitas.php
// -> Tujuan Spesifik: Modul jejak audit keamanan (Audit Trail) berbasis Log pencatatan riwayat aksi para pengguna di lapangan secara historikal runtut memanjang kebelakang.
// ======================================

session_start();
include '../../config/koneksi.php';

if($_SESSION['role'] != "admin") { 
    header("location:../../auth/index.php"); 
    exit; 
}

// ==== BLOK LOGIKA: FILTER PENYARINGAN JEJAK DIGITAL LOGS ====
$filter_tgl = $_GET['tanggal'] ?? '';
$search = $_GET['search'] ?? '';

// [SINTAKS PHP]: Rangkaian Utama Kueri Dinamik dengan INNER JOIN ke tb_user 
// untuk mendapatkan nama_lengkap dan role aktor secara gamblang.
$query_str = "
    SELECT l.*, u.nama_lengkap, u.role, u.username 
    FROM tb_log_aktivitas l 
    JOIN tb_user u ON l.id_user = u.id_user 
    WHERE 1=1
";

if($filter_tgl) {
    // Filter berdasarkan kolom waktu_aktivitas di tb_log_aktivitas
    $query_str .= " AND DATE(l.waktu_aktivitas) = '$filter_tgl'";
}

if($search) {
    $search_safe = mysqli_real_escape_string($koneksi, $search);
    $query_str .= " AND (u.nama_lengkap LIKE '%$search_safe%' OR l.aktivitas LIKE '%$search_safe%')";
}

// Order by terbaru
$query_str .= " ORDER BY l.waktu_aktivitas DESC";
$query = mysqli_query($koneksi, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Audit Trail - Hogwarts Admin</title>
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
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08); /* Bayang layang blur 60px */
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

        .main-content {
            flex: 1;
            background: #f1f5f9;
            padding: 40px 50px;
            overflow-y: auto;
        }

        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { color: #64748b; margin: 5px 0 0; font-size: 14px; }

        .filter-card {
            background: white; padding: 25px; border-radius: 20px; 
            display: flex; gap: 20px; margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .input-group { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .input-group label { font-size: 11px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; }

        .input-group input { 
            padding: 12px 18px; border-radius: 12px; border: 1px solid #e2e8f0; 
            font-family: inherit; font-size: 14px; outline: none; transition: 0.2s;
            background: #f1f5f9;
        }

        .input-group input:focus {
            border-color: #1d4ed8;
            background: white;
        }

        .log-container {
            background: white; border-radius: 20px; overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; padding: 18px 25px; font-size: 11px; font-weight: 800;
            color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.05em;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }

        td { 
            padding: 20px 25px; font-size: 14px; color: #475569; border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle;
        }

        .time-col { color: #64748b; width: 180px; font-size: 13px; }
        .user-col { width: 280px; }
        .activity-col { color: #0f172a; font-weight: 600; }

        .user-name { font-weight: 800; color: #0f172a; margin-bottom: 6px; display: block; }

        .role-badge {
            display: inline-block; padding: 4px 10px; border-radius: 8px; 
            font-size: 10px; font-weight: 800; text-transform: uppercase;
        }
        .role-admin { background: #fee2e2; color: #b91c1c; }
        .role-petugas { background: #dcfce7; color: #166534; }
        .role-owner { background: #fef9c3; color: #854d0e; }

        tr:hover { background: #f8fafc; }
        .btn-submit { display: none; }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: Logo Brand -->
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php"> Dashboard</a>
                <a href="kelola_user.php"> Data User</a>
                <a href="tarif_parkir.php"> Data Tarif</a>
                <a href="area_parkir.php"> Data Area</a>
                <a href="log_aktivitas.php" class="active"> Log Aktivitas (Audit)</a>
            </div>
            
            <a href="../../auth/logout.php" style="margin-top: auto; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;"> Logout</a>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>Audit Trail Sistem</h1>
                <p>Jejak rekam aktivitas seluruh entitas staf di dalam sistem aplikasi PARLINE.</p>
            </div>

            <form method="GET">
                <div class="filter-card">
                    <div class="input-group">
                        <label>Pilih Tanggal</label>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($filter_tgl) ?>" onchange="this.form.submit()">
                    </div>
                    <div class="input-group">
                        <label>Cari Aktivitas / Nama Staf</label>
                        <input type="text" name="search" placeholder="Ketik nama atau aktivitas..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <div class="log-container">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Aktor (Pengguna)</th>
                            <th>Rincian Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query)): 
                                // Tentukan kelas CSS untuk badge role
                                $role_class = '';
                                switch(strtolower($row['role'])) {
                                    case 'admin': $role_class = 'role-admin'; break;
                                    case 'petugas': $role_class = 'role-petugas'; break;
                                    case 'owner': $role_class = 'role-owner'; break;
                                }
                            ?>
                            <tr>
                                <td class="time-col">
                                    <div><?= date('d M Y', strtotime($row['waktu_aktivitas'])) ?></div>
                                    <div style="color:#0f172a; font-weight:600; margin-top:4px;"><?= date('H:i:s', strtotime($row['waktu_aktivitas'])) ?></div>
                                </td>
                                
                                <td class="user-col">
                                    <span class="user-name"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                                    <span class="role-badge <?= $role_class ?>"><?= htmlspecialchars($row['role']) ?></span>
                                </td>
                                
                                <td class="activity-col">
                                    <?= htmlspecialchars($row['aktivitas']) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 50px; color: #64748b;">
                                    Tidak ada log aktivitas yang ditemukan untuk filter tersebut.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
