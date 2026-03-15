<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/owner/diagram_pendapatan.php
// -> Tujuan Spesifik: Modul visualisasi data Grafik Garis (Line Chart) untuk merepresentasikan tren perbandingan pendapatan selama seminggu ke belakang.
// ======================================

// [SINTAKS PHP]: session_start() | Memasuki siklus state manajemen login (Sesi Aktif)
session_start();
include '../../config/koneksi.php';

// [SINTAKS PHP]: Proteksi Lapis Eksekutif | Filter otoritas khusus hanya bagi level owner
if($_SESSION['role'] != "owner") { 
    // [SINTAKS PHP]: header location | Tendang user ke halaman depan login
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Deklarator Variabel Array Induk | Tempat penampungan wadah keranjang data yang akan dikirim ke Javascript
$label_tanggal = [];
$data_pendapatan = [];

// [SINTAKS PHP]: Perulangan Looping Mundur (for) | Meminta siklus iterasi berjalan bertebalikan dr angka 6 menurun hinga mencapai 0
for ($i = 6; $i >= 0; $i--) {
    // [SINTAKS PHP]: Kalkulator Hari Mundur | Menarik mundur waktu "Hari Ini" dikurangi sejauh besaran nilai $i (cth: "-6 days" berarti 6 hari yg lalu)
    $tgl = date('Y-m-d', strtotime("-$i days"));
    
    // [SINTAKS PHP]: Push elemen Data Array ([]) | Memasukkan hasil ejaan tanggal format "12 May" ke gerbong belakang list $label_tanggal
    $label_tanggal[] = date('d M', strtotime($tgl));
    
    // [SINTAKS PHP]: mysqli_query Agregasi Sum() Berantai Loop | Di dalam putaran looping, secara konstan melemparkan Query menghitung Pendapatan Kertas Struk ('Biaya Total') berdasarkan tanggal Filter berjalannya Siklus saat ini
    $sql = "SELECT SUM(biaya_total) as total FROM tb_transaksi WHERE DATE(waktu_keluar) = '$tgl' AND status = 'keluar'";
    $query = mysqli_query($koneksi, $sql);
    
    // [SINTAKS PHP]: Fetch Row Assoc ke Variabel Array
    $res = mysqli_fetch_assoc($query);
    
    // [SINTAKS PHP]: Array Push Data | Memasukkan Uptime angka akumulasi Rupiah ke Chart Array. Disertai Nilai Falback (?? 0) jaga-jaga apabila di hari tsb gak ada pengunjung sama sekali
    $data_pendapatan[] = $res['total'] ?? 0;
}
?>

<!-- [SINTAKS HTML]: Root Tree Setup HTML 5 -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Pendapatan - Hogwarts Owner</title>
    <!-- [SINTAKS HTML]: Pengimporan Typografi Inter Font via Google WebFont Services -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS JS]: <script src> | Mengintegrasikan Engine pustaka JS Visual ChartJS ke dalam aplikasi secara cloud (CDN delivery) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* [SINTAKS CSS]: Setup Dasar Konsistensi Margin Font Layout */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; 
            background: #f1f5f9; 
            color: #0f172a;
        }

        /* [SINTAKS CSS]: Box Menu Navigasi Samping Bercat Marun Terang */
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
            width: 80px; 
            margin-bottom: 15px; 
        }

        /* [SINTAKS CSS]: Styling Teks Owner Judul Sidebar Rapat Teks spasi melebar modern */
        .sidebar-header h2 { 
            font-size: 14px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin: 0; 
            font-weight: 600;
        }

        /* [SINTAKS CSS]: Modifikasi Label Tautan Menjadi Block Element Panjang Sebaris Penuh (Memudahkan diklik/dijari) */
        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: rgba(255,255,255,0.8); 
            padding: 14px 25px; 
            text-decoration: none; 
            transition: 0.3s; 
            font-size: 15px;
        }

        /* [SINTAKS CSS]: Transisi Geser Kanan (Padding-left) saat ditempel pointer mouse */
        .sidebar a:hover { 
            background: rgba(255,255,255,0.1); 
            color: white; 
            padding-left: 30px; 
        }

        /* [SINTAKS CSS]: Sorotan Tab Aktif / Sedang Diakses Warna Marun Gelap */
        .sidebar a.active { 
            background: #782626; 
            color: white; 
            border-radius: 0 50px 50px 0; 
            margin-right: 20px; 
            font-weight: 600; 
        }

        /* [SINTAKS CSS]: Dorong anchor text Logout ke Paling pantat elemen flexbox area margin otomatis penuh */
        .sidebar-footer { margin-top: auto; }
        .sidebar-footer a { color: #ffb1b1 !important; }

        /* [SINTAKS CSS]: Ruang Leluasa Area Kerja Chart dikanan Sidebar statis */
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

        /* [SINTAKS CSS]: Papan Bingkai Canvas Diagram melengkung bersudut Shadow halus putih elegan */
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
    
    <!-- [SINTAKS HTML]: Navigasi Kiri Panel Marun -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>Owner</h2>
        </div>
        
        <a href="dashboard.php">🏠 Dashboard Ringkas</a>
        <a href="diagram_pendapatan.php" class="active">📊 Diagram Analisis</a>
        <!-- Fix Link ke detail laporan (Supaya tdk 404 kalau di klik)-->
        <a href="detail_laporan.php">📄 Detail Laporan</a>
        
        <div class="sidebar-footer">
            <a href="../../auth/logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- [SINTAKS HTML]: Bagian Tubuh utama panggung Layout -->
    <div class="main-content">
        <h2 class="page-title">Statistik Pendapatan Parkir</h2>
        <p style="color: #475569; margin-bottom: 30px;">Grafik tren pendapatan harian selama 7 hari terakhir.</p>
        
        <!-- [SINTAKS HTML]: Wadah Penampil Diagram -->
        <div class="card-chart">
            <canvas id="pemasukanChart" height="120"></canvas>
        </div>
    </div>

    <!-- [SINTAKS JAVASCRIPT]: Otak Pengolah Data Array PHP menjadi Vector Grafik Bergerak (ChartJS Canvas Render logic) -->
    <script>
        // [SINTAKS JS]: document.getElementById.getContext | Titik Inisiasi pengambilan Kontrol Canvas Tag HTML oleh script JS 
        const ctx = document.getElementById('pemasukanChart').getContext('2d');
        
        // [SINTAKS JS]: ctx.createLinearGradient | Menciptakan Palet warna gradasi linear pada kanvas yang akan dipake ngisi background garis grafik (Biar warnanya pudar di bawah) 
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(143, 52, 52, 0.4)');
        gradient.addColorStop(1, 'rgba(143, 52, 52, 0.0)');

        // [SINTAKS JS]: Chart() class instance
        new Chart(ctx, {
            type: 'line', // Mode Grafik Garis Bergerigi Area
            data: {
                // [SINTAKS PHP]: json_encode | Konversi Ajaib Array PHP tulen menjadi Format teks Array format JS (Menjembatani lempar data lintas bahasa PHP->JS di Front-End)
                labels: <?php echo json_encode($label_tanggal); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    // [SINTAKS PHP]: Eksekutor pelempar Data Matriks Uang/Rupiah ke JS 
                    data: <?php echo json_encode($data_pendapatan); ?>,
                    borderColor: '#8f3434', //Warna Garis Marun Tebal
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true, // [SINTAKS JS]: Parameter pengisi balok area dibawah kurva dgn warna gradient
                    tension: 0.4, // Parameter Pelembut Tikungan Siku Garis Biar Bergelombang Estetik (Bezier curve smoothing)
                    pointBackgroundColor: '#8f3434',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false } // Semaputkan Legend Teks Atas
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // [SINTAKS JS]: callback function | Menyisipkan logika kode modifikasi kecil ketika mesin JS melukis Label Sumbu Axis Y menjadi bertipe Mata uang Lokal
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
