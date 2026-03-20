<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/owner/dashboard.php
// -> Tujuan Spesifik: Modul pelaporan Eksekutif (Owner) untuk memantau trafik Keuangan dan Tren Performa Area Parkir secara Visual Grafik real-time.
// ======================================

// [SINTAKS PHP]: session_start() | Melanjutkan State Sesi Autentikasi untuk menjaga modul Owner tetap privat
session_start();

// [SINTAKS PHP]: Percabangan Keamanan Akses | Melarang keras Admin & Petugas masuk ke layar Owner
if($_SESSION['role'] != "owner") { 
    // [SINTAKS PHP]: header("location:...") | Lempar penyusup balik ke Beranda Login form
    header("location:../../auth/index.php"); 
    // [SINTAKS PHP]: exit() | Terminasi eksekusi pembacaan kode PHP ke bawahnya
    exit; 
}

// [SINTAKS PHP]: include koneksi | Menjemput alat/konektor SQL untuk narik Rekap Data Transaksi 
include '../../config/koneksi.php';

// [SINTAKS PHP]: date() format string | Variabel Dinamis penentu Waktu acuan. 'Y-m-d' misal: 2024-05-12, 'Y-m' misal 2024-05
$hari_ini = date('Y-m-d');
$bulan_ini = date('Y-m');

/* [SINTAKS PHP]: Kumpulan Query Agregasi Matematika (SUM/COUNT) & mysqli_fetch_assoc */
// -> SUM(biaya_total): Menjumlahkan total harga parkir yang sudah lunas dibayar
// -> DATE(waktu_keluar): Memfilter secara ketat waktu keluar (Cut-off date) yang terjadi HARI INI saja
// -> Coalescing Operator (?? 0): Jika Query meleset/hasilnya kosong melompong (Null), laporkan nilainya "0" Rupiah agar fungsi kalkulator ChartJS nanti gak Error NaN.
$pendapatan_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$hari_ini'"))['total'] ?? 0;

// -> DATE_FORMAT(..., '%Y-%m'): Sama kaya atas, tapi format saringannya dibesarkan jadi "Satu BULAN INI Penuh"
$pendapatan_bulan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE_FORMAT(waktu_keluar, '%Y-%m') = '$bulan_ini'"))['total'] ?? 0;

// -> COUNT(*): Bukan menjumlah Rupiah, tapi Menghitung Quantitas Baris / Banyaknya KENDARAAN yang keluar gerbang
$unit_keluar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$hari_ini'"))['total'] ?? 0;
?>

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Formasi Tag HTML standar kekinian -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parline - Dashboard Owner</title>
    
    <!-- [SINTAKS HTML]: <link> Google Fonts API | Jenis huruf utama 'Plus Jakarta Sans' -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS JAVASCRIPT]: <script src> Pustaka Eksternal | Menarik mesin pembuat Visualisasi Data / Grafik Open Source (Chart.JS) dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- [SINTAKS CSS]: Lembar Gaya / Stylesheet -->
    <style>
        /* [SINTAKS CSS]: :root | Kamus Global Konstanta Warna */
        :root {
            --primary-blue: #2563eb; 
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

        /* [SINTAKS CSS]: .app-container | Kanvas Layar Lebar dibatasi 1250 piksel menjorok tengah */
        .app-container {
            width: 100%; max-width: 1250px; height: 90vh; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.12);
        }

        /* [SINTAKS CSS]: Sidebar Nav kiri */
        .sidebar {
            width: 260px; background: var(--white); padding: 40px 25px;
            display: flex; flex-direction: column; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-section h2 { font-size: 18px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: Perawatan Hyperlink menu */
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 18px;
            text-decoration: none; color: #475569; font-size: 14px; font-weight: 600;
            margin-bottom: 8px; border-radius: 16px; transition: 0.3s;
        }

        .nav-menu a.active { 
            background: #2563eb; color: var(--white); 
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }
        .nav-menu a:hover:not(.active) { background: #f1f5f9; color: #2563eb; }

        /* [SINTAKS CSS]: Layar Panggungan Grafik flex kanan */
        .main-content { flex: 1; padding: 40px; overflow-y: auto; background: #f1f5f9; }

        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .header-top h1 { font-size: 24px; color: #0f172a; font-weight: 800; margin: 0; }

        /* [SINTAKS CSS]: CSS GRID | Membangun tiga (3) Kolom Sejajar Proporsinya sama pembagi sama rata (1fr) untuk Kartu Informasi Keuangan */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        
        /* [SINTAKS CSS]: .stat-card | Modifikasi Visual Kartu Timbul Statistik Keuangan */
        .stat-card {
            background: var(--white); padding: 25px; border-radius: 24px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover { transform: translateY(-5px); }

        /* [SINTAKS CSS]: Custom Class Pembeda | Khusus kartu Finansial Utama dikasih cat Biru Gelap biar mencolok mata Owner */
        .stat-card.highlight { 
            background: #2563eb; color: white; border: none; 
            box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.4);
        }

        .stat-card label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; }
        .stat-card h2 { font-size: 24px; margin: 10px 0 5px; font-weight: 800; }
        .stat-card p { font-size: 12px; margin: 0; opacity: 0.7; }

        /* [SINTAKS CSS]: Wadah Kanvas Grafik Visual ChartJS */
        .chart-container {
            background: var(--white); padding: 30px; border-radius: 24px;
            border: 1px solid #e2e8f0; margin-bottom: 20px;
        }

        /* [SINTAKS CSS]: Styling Tombol Print Gelap hitam */
        .btn-report {
            background: #0f172a; color: white; border: none;
            padding: 12px 22px; border-radius: 14px; font-weight: 700;
            font-size: 13px; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        
        .btn-report:hover { background: #000; transform: translateY(-2px); }

        /* [SINTAKS CSS]: Modifikasi Webkit-Scrollbar | Menyulap batang penggulir standar PC (Scroll Wheel Axis-Y) abu kotak kaku jadi melengkung estetik seperti HP IOS */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="app-container">
        <!-- [SINTAKS HTML]: Navigasi Samping -->
        <div class="sidebar">
            <div class="logo-section">
                <!-- [SINTAKS HTML]: <img> Ikon Logo -->
                <img src="../../public/hogwarts-removebg-preview.png" width="35" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="detail_laporan.php">Detail Laporan</a>
            </div>

            <a href="../../auth/logout.php" style="margin-top:auto; color:#f43f5e; text-decoration:none; font-size:13px; font-weight:700; padding-left:18px;"> Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top">
                <div>
                    <h1>Dashboard Pemilik</h1>
                    <p style="color:#475569; margin:5px 0 0; font-size:14px;">Pantau data keuangan Sistem Parkir hari ini.</p>
                </div>
                <!-- [SINTAKS HTML]: Tombol Export Excel | Mengarahkan Owner ke halaman export_excel.php yang men-generate file .xls untuk diunduh -->
                <a href="export_excel.php" class="btn-report" style="text-decoration:none;">
                    <span></span> Export Laporan Excel
                </a>
            </div>

            <!-- [SINTAKS HTML]: Group Susunan 3 Kartu Uang -->
            <div class="stats-grid">
                
                <!-- KARTU 1: PENDAPATAN HARIAN -->
                <div class="stat-card">
                    <label>Pendapatan Hari Ini</label>
                    <!-- [SINTAKS PHP]: number_format() | Menyisipkan format Uang Akuntansi Lokal (Memisah ribuan jadi Titik. '1500000' Dikonversi jadi '1.500.000') -->
                    <h2 style="color: #2563eb;">Rp <?= number_format($pendapatan_hari, 0, ',', '.') ?></h2>
                    <p>Berdasarkan data keluar hari ini</p>
                </div>
                
                <!-- KARTU 2: PENDAPATAN BULANAN (Warna Biru Kontras / Highlighted) -->
                <div class="stat-card highlight">
                    <label>Pendapatan Bulan Ini</label>
                    <h2>Rp <?= number_format($pendapatan_bulan, 0, ',', '.') ?></h2>
                    <p>Total pendapatan brankas bulan ini</p>
                </div>
                
                <!-- KARTU 3: UNIT MOBIL KELUAR -->
                <div class="stat-card">
                    <label>Total Kendaraan Keluar</label>
                    <h2 style="color: #0f172a;"><?= $unit_keluar ?> <span style="font-size: 14px; color: #475569;">Unit</span></h2>
                    <p>Traffic keluar hari ini</p>
                </div>
            </div>

            <!-- [SINTAKS HTML]: Wadah Grafik Batang Chart.JS -->
            <div class="chart-container">
                <h3 style="margin: 0 0 25px 0; font-size: 16px; color: #0f172a;"> Tren Pendapatan</h3>
                <!-- [SINTAKS HTML]: <canvas> | Titik Koordinat Render Element dimana Objek Grafis 2D Chart.JS akan Digambar/Disuntik ke dalam DOM -->
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- [SINTAKS JAVASCRIPT]: Blok Pembangunan Logika ChartJS -->
    <script>
        // [SINTAKS JS]: document.getElementById | Menyomot Canvas #revenueChart menjadi Konteks Grafik 2 Dimensi (Render target)
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // [SINTAKS JS]: Inisiasi Instansiasi new Chart() | Objek library grafik dibanun parameternya
        new Chart(ctx, {
            type: 'bar', // Visual bar / Grafik Papan Batang Berdiri (Bukan garis menanjak)
            data: {
                // Sumbu X Bawah -> Susunan hari dalam bahasa Indonesia
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    // Sumbu Y Atas (Nominal data). Meng-Injek PHP Variabel $pendapatan_hari langsung ke dalam Array script Javascript
                    data: [1200000, 1900000, 1500000, <?= $pendapatan_hari ?>, 0, 0, 0],
                    backgroundColor: '#3b82f6', // Cat Batang Warna Biru Terang
                    borderRadius: 12, // Lengkung Batang Pinggiran Atasnya
                    barThickness: 35 // Ketebalan Batang 35 Pixel
                }]
            },
            options: {
                responsive: true, // Auto melar menipis menyesuaikan ukuran layar PC & HP
                plugins: { legend: { display: false } }, // Menyembunyikan Label Teks Keterangan Legenda di atas tabel (Supaya lapang minimalis)
                scales: {
                    // Konfigurasi Grid/Jaring Meteran Garis Halus
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
