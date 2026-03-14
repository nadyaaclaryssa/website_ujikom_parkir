
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/owner/diagram_pendapatan.php
// -> Tujuan Spesifik: Modul/komponen fungsional spesifik aplikasi Smart Parking System.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
include '../../../config/koneksi.php';

// Proteksi akses Owner
if($_SESSION['role'] != "owner") { 
    // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../../auth/index.php"); 
    exit; 
}

// Ambil data pendapatan 7 hari terakhir secara otomatis
$label_tanggal = [];
$data_pendapatan = [];

for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label_tanggal[] = date('d M', strtotime($tgl));
    
    // Query hitung total per hari
    $sql = "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$tgl' AND status = 'keluar'";
    $query = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, $sql);
    $res = // [SINTAKS PHP]: mysqli_fetch_assoc() | Mem-parsing / Mengambil satu baris data array dari hasil query berdasarkan NAma Kolom / Index String
mysqli_fetch_assoc($query);
    $data_pendapatan[] = $res['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Pendapatan - Hogwarts Owner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* --- CSS KONSISTEN (Sama dengan Dashboard & Laporan) --- */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; 
            background: #f1f5f9; 
            color: #0f172a;
        }

        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: #8f3434; 
            color: white; 
            position: fixed; 
            display: flex; 
            flex-direction: column; 
            padding: 20px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header { 
            text-align: center; 
            padding: 20px; 
            margin-bottom: 20px; 
        }

        .sidebar-header img { 
            width: 80px; /* Konsisten dengan ukuran yang kita sepakati */
            margin-bottom: 15px; 
        }

        .sidebar-header h2 { 
            font-size: 14px; /* Konsisten dengan Owner */
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin: 0; 
            font-weight: 600;
        }

        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: rgba(255,255,255,0.8); 
            padding: 14px 25px; 
            text-decoration: none; 
            transition: 0.3s; 
            font-size: 15px;
        }

        .sidebar a:hover { 
            background: rgba(255,255,255,0.1); 
            color: white; 
            padding-left: 30px; 
        }

        .sidebar a.active { 
            background: #782626; 
            color: white; 
            border-radius: 0 50px 50px 0; 
            margin-right: 20px; 
            font-weight: 600; 
        }

        .sidebar-footer { margin-top: auto; }
        .sidebar-footer a { color: #ffb1b1 !important; }

        .main-content { 
            margin-left: 260px; 
            padding: 40px; 
            width: 100%; 
        }

        .page-title { 
            font-size: 24px; 
            font-weight: 700; 
            color: #0f172a; 
            margin: 0 0 10px 0; 
        }

        .card-chart { 
            background: white; 
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            border: 1px solid #e2e8f0; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>Owner</h2>
        </div>
        
        <a href="dashboard.php">🏠 Dashboard Ringkas</a>
        <a href="diagram_pendapatan.php" class="active">📊 Diagram Analisis</a>
        <a href="laporan_detail.php">📄 Detail Laporan</a>
        
        <div class="sidebar-footer">
            <a href="../../../../logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="page-title">Statistik Pendapatan Parkir</h2>
        <p style="color: #475569; margin-bottom: 30px;">Grafik tren pendapatan harian selama 7 hari terakhir.</p>
        
        <div class="card-chart">
            <canvas id="pemasukanChart" height="120"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('pemasukanChart').getContext('2d');
        
        // Membuat gradien warna marun transparan agar terlihat modern
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(143, 52, 52, 0.4)');
        gradient.addColorStop(1, 'rgba(143, 52, 52, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($label_tanggal); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode($data_pendapatan); ?>,
                    borderColor: '#8f3434',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4, // Membuat garis melengkung estetik
                    pointBackgroundColor: '#8f3434',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

