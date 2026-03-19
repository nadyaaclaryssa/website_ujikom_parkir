<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/area_parkir.php
// -> Tujuan Spesifik: Modul Admin untuk Monitoring + CRUD (Tambah/Edit/Hapus) Area Parkir secara Real dari Database.
// ======================================

// [SINTAKS PHP]: session_start() | Persiapan penerimaan token memori akses User aktif
session_start();

// [SINTAKS PHP]: Verifikasi Cekam Role | Hanya kasta Administrator yg sanggup menembus pagar pembatas halaman ini
if($_SESSION['role'] != "admin") { 
    header("location:../../auth/index.php"); 
    exit; 
}

// [SINTAKS PHP]: Mengikutsertakan modul koneksi.php utama
include '../../config/koneksi.php';

// =============================================
// PROSES FORM: Tambah Area Baru
// =============================================
$pesan = '';
$pesan_type = '';

if(isset($_POST['tambah_area'])) {
    $nama_area = mysqli_real_escape_string($koneksi, $_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];
    
    if(!empty($nama_area) && $kapasitas > 0) {
        $sql = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES ('$nama_area', $kapasitas, 0)";
        if(mysqli_query($koneksi, $sql)) {
            $pesan = "Area '$nama_area' berhasil ditambahkan!";
            $pesan_type = 'success';
            // Log aktivitas
            $id_user = $_SESSION['id_user'];
            mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ($id_user, 'Menambah area parkir: $nama_area', NOW())");
        } else {
            $pesan = "Gagal menambahkan area!";
            $pesan_type = 'error';
        }
    } else {
        $pesan = "Nama area dan kapasitas harus diisi dengan benar!";
        $pesan_type = 'error';
    }
}

// =============================================
// PROSES FORM: Edit Area
// =============================================
if(isset($_POST['edit_area'])) {
    $id_area = (int)$_POST['id_area'];
    $nama_area = mysqli_real_escape_string($koneksi, $_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];
    
    if(!empty($nama_area) && $kapasitas > 0) {
        $sql = "UPDATE tb_area_parkir SET nama_area='$nama_area', kapasitas=$kapasitas WHERE id_area=$id_area";
        if(mysqli_query($koneksi, $sql)) {
            $pesan = "Area berhasil diperbarui!";
            $pesan_type = 'success';
            $id_user = $_SESSION['id_user'];
            mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ($id_user, 'Mengedit area parkir: $nama_area', NOW())");
        } else {
            $pesan = "Gagal memperbarui area!";
            $pesan_type = 'error';
        }
    }
}

// =============================================
// PROSES: Hapus Area
// =============================================
if(isset($_GET['hapus'])) {
    $id_area = (int)$_GET['hapus'];
    // Cek apakah area sedang digunakan (ada kendaraan masuk)
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT terisi FROM tb_area_parkir WHERE id_area=$id_area"));
    if($cek && $cek['terisi'] > 0) {
        $pesan = "Tidak bisa menghapus area yang sedang terisi kendaraan!";
        $pesan_type = 'error';
    } else {
        // Cek apakah ada transaksi aktif di area ini
        $cek_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_transaksi WHERE id_area=$id_area AND status='masuk'"));
        if($cek_transaksi && $cek_transaksi['total'] > 0) {
            $pesan = "Tidak bisa menghapus area yang memiliki transaksi aktif!";
            $pesan_type = 'error';
        } else {
            $nama_hapus = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_area FROM tb_area_parkir WHERE id_area=$id_area"))['nama_area'] ?? '';
            if(mysqli_query($koneksi, "DELETE FROM tb_area_parkir WHERE id_area=$id_area")) {
                $pesan = "Area '$nama_hapus' berhasil dihapus!";
                $pesan_type = 'success';
                $id_user = $_SESSION['id_user'];
                mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ($id_user, 'Menghapus area parkir: $nama_hapus', NOW())");
            } else {
                $pesan = "Gagal menghapus area!";
                $pesan_type = 'error';
            }
        }
    }
}

// [SINTAKS PHP]: AMBIL data area dari DATABASE (bukan hardcode)
$query = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir ORDER BY id_area ASC");
?>

<!-- [SINTAKS HTML]: Document Node Tree Versioning Type -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parline - Area Parkir</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1d4ed8;
            --grad-1: #e0f2fe; 
            --grad-2: #bae6fd;
            --success: #10b981;
            --danger: #ef4444;
        }

        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }

        body { 
            background: linear-gradient(135deg, var(--grad-1) 0%, var(--grad-2) 100%);
            display: flex; justify-content: center; align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .app-container {
            width: 96%; height: 94vh;
            background: white; border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.08);
        }

        .sidebar {
            width: 280px; background: #f1f5f9;
            padding: 40px 25px; display: flex; flex-direction: column;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .logo-section { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; padding-left: 10px; }
        .logo-section img { width: 40px; height: 40px; border-radius: 10px; }
        .logo-section h2 { font-size: 22px; margin: 0; color: #0f172a; font-weight: 800; }

        .nav-menu { flex-grow: 1; }
        .nav-menu a {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            text-decoration: none; color: #64748b; font-size: 15px; font-weight: 600;
            margin-bottom: 8px; border-radius: 18px; transition: 0.3s;
        }
        .nav-menu a.active { background: #1d4ed8; color: white; box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.25); }
        .nav-menu a:hover:not(.active) { background: #e2e8f0; color: #0f172a; }

        .main-content { flex: 1; background: white; padding: 40px 50px; overflow-y: auto; }

        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .section-title { font-size: 28px; color: #0f172a; margin: 0; font-weight: 800; }

        /* === FORM TAMBAH AREA === */
        .form-card {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-radius: 20px; padding: 28px 30px;
            margin-bottom: 30px; border: 1px solid #bfdbfe;
        }
        .form-card h3 { margin: 0 0 18px; font-size: 16px; color: #1e40af; font-weight: 700; }
        .form-row { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 200px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input {
            width: 100%; padding: 12px 16px; border: 2px solid #cbd5e1; border-radius: 14px;
            font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none; transition: 0.3s; background: white; box-sizing: border-box;
        }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(29,78,216,0.1); }
        .btn-tambah {
            background: var(--primary); color: white; border: none;
            padding: 12px 28px; border-radius: 14px; font-weight: 700; font-size: 14px;
            cursor: pointer; transition: 0.3s; white-space: nowrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-tambah:hover { background: #1e40af; transform: translateY(-2px); box-shadow: 0 8px 20px -5px rgba(29,78,216,0.3); }

        /* === AREA GRID === */
        .area-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .area-card {
            background: white; border-radius: 24px; padding: 30px;
            border: 1px solid #e2e8f0; transition: 0.3s;
            position: relative;
        }
        .area-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); }

        .status-badge {
            position: absolute; top: 25px; right: 25px;
            padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800;
        }
        .status-badge.tersedia { background: #dcfce7; color: var(--success); }
        .status-badge.penuh { background: #fee2e2; color: var(--danger); }

        .area-name { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 5px 0; }
        .area-id { color: #94a3b8; font-size: 11px; font-weight: 600; margin-bottom: 20px; }

        .stat-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .stat-label { font-size: 13px; color: #475569; font-weight: 600; }
        .stat-value { font-size: 14px; font-weight: 800; color: #0f172a; }
        .stat-value.available { color: var(--success); }
        .stat-value.full { color: var(--danger); }

        .progress-container { height: 10px; background: #f1f5f9; border-radius: 20px; overflow: hidden; margin: 15px 0; }
        .progress-bar { height: 100%; border-radius: 20px; transition: 1s ease-in-out; }
        .progress-bar.low { background: #1d4ed8; }
        .progress-bar.mid { background: #f59e0b; }
        .progress-bar.high { background: var(--danger); }

        .perc-label { text-align: right; font-size: 11px; color: #64748b; font-weight: 700; }

        /* === TOMBOL AKSI DI CARD === */
        .card-actions { display: flex; gap: 8px; margin-top: 18px; padding-top: 15px; border-top: 1px solid #f1f5f9; }
        .btn-edit {
            flex: 1; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
            padding: 10px; border-radius: 12px; font-weight: 700; font-size: 12px;
            cursor: pointer; transition: 0.3s; text-align: center; text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-edit:hover { background: #dbeafe; }
        .btn-hapus {
            flex: 1; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
            padding: 10px; border-radius: 12px; font-weight: 700; font-size: 12px;
            cursor: pointer; transition: 0.3s; text-align: center; text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-hapus:hover { background: #fee2e2; }

        .avatar { width: 40px; height: 40px; background: #1d4ed8; color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; }

        /* === ALERT === */
        .alert {
            padding: 14px 20px; border-radius: 14px; margin-bottom: 20px;
            font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* === MODAL EDIT === */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 999;
            justify-content: center; align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: white; border-radius: 24px; padding: 35px;
            width: 100%; max-width: 450px; box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        .modal-box h3 { margin: 0 0 20px; font-size: 18px; color: #0f172a; }
        .modal-box .form-group { margin-bottom: 15px; }
        .modal-actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn-cancel {
            flex: 1; background: #f1f5f9; color: #475569; border: none;
            padding: 12px; border-radius: 14px; font-weight: 700; cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px;
        }
        .btn-save {
            flex: 1; background: var(--primary); color: white; border: none;
            padding: 12px; border-radius: 14px; font-weight: 700; cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px;
        }
        .btn-save:hover { background: #1e40af; }

        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        .empty-state {
            text-align: center; padding: 60px 20px; color: #94a3b8;
        }
        .empty-state span { font-size: 48px; display: block; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <div class="sidebar">
            <div class="logo-section">
                <img src="../../public/hogwarts-removebg-preview.png" alt="Logo">
                <h2>Parline</h2>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="kelola_user.php">👥 Data User</a>
                <a href="tarif_parkir.php">📂 Data Tarif</a>
                <a href="area_parkir.php" class="active">🕒 Data Area</a>
            </div>
            
            <a href="../../auth/logout.php" style="margin-top: auto; color: #64748b; text-decoration: none; font-size: 14px; padding-left: 20px; font-weight: 600;">🚪 Logout</a>
        </div>

        <div class="main-content">
            <div class="header-top">
                <h1 class="section-title">Kelola Area Parkir</h1>
                <div style="display: flex; align-items: center; gap: 15px; border-left: 1px solid #f1f5f9; padding-left: 20px;">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 14px; color: #0f172a;"><?= $_SESSION['nama_lengkap'] ?? 'Admin' ?></div>
                        <div style="font-size: 11px; color: #64748b;">Administrator</div>
                    </div>
                    <div class="avatar">A</div>
                </div>
            </div>

            <!-- ALERT PESAN -->
            <?php if(!empty($pesan)): ?>
            <div class="alert alert-<?= $pesan_type ?>">
                <?= $pesan_type == 'success' ? '✅' : '❌' ?> <?= $pesan ?>
            </div>
            <?php endif; ?>

            <!-- FORM TAMBAH AREA BARU -->
            <div class="form-card">
                <h3>➕ Tambah Area Parkir Baru</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Area</label>
                            <input type="text" name="nama_area" placeholder="Contoh: Lantai 2, Blok C - Belakang" required>
                        </div>
                        <div class="form-group">
                            <label>Total Kapasitas (Slot)</label>
                            <input type="number" name="kapasitas" placeholder="50" min="1" required>
                        </div>
                        <button type="submit" name="tambah_area" class="btn-tambah">➕ Tambah Area</button>
                    </div>
                </form>
            </div>

            <!-- GRID AREA PARKIR (DATA REAL DARI DATABASE) -->
            <div class="area-grid">
                <?php 
                $jumlah = 0;
                while($a = mysqli_fetch_assoc($query)): 
                    $jumlah++;
                    $persen = ($a['kapasitas'] > 0) ? ($a['terisi'] / $a['kapasitas']) * 100 : 0;
                    $sisa = $a['kapasitas'] - $a['terisi'];
                    $is_penuh = ($sisa <= 0);
                    
                    // Tentukan warna progress bar berdasarkan persentase
                    $bar_class = 'low';
                    if($persen >= 70) $bar_class = 'high';
                    elseif($persen >= 40) $bar_class = 'mid';
                ?>
                <div class="area-card">
                    <span class="status-badge <?= $is_penuh ? 'penuh' : 'tersedia' ?>">
                        <?= $is_penuh ? 'PENUH' : 'TERSEDIA' ?>
                    </span>
                    
                    <h3 class="area-name"><?= htmlspecialchars($a['nama_area']) ?></h3>
                    <div class="area-id">ID: AREA-<?= str_pad($a['id_area'], 3, '0', STR_PAD_LEFT) ?></div>

                    <div class="stat-row">
                        <span class="stat-label">Total Kapasitas</span>
                        <span class="stat-value"><?= $a['kapasitas'] ?> Slot</span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">Terisi</span>
                        <span class="stat-value"><?= $a['terisi'] ?> Slot</span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">Tersedia</span>
                        <span class="stat-value <?= $is_penuh ? 'full' : 'available' ?>"><?= max(0, $sisa) ?> Slot</span>
                    </div>

                    <div class="progress-container">
                        <div class="progress-bar <?= $bar_class ?>" style="width: <?= min(100, $persen) ?>%;"></div>
                    </div>
                    <div class="perc-label">Terisi: <?= round($persen) ?>%</div>

                    <!-- Tombol Aksi Edit & Hapus -->
                    <div class="card-actions">
                        <button class="btn-edit" onclick="openEdit(<?= $a['id_area'] ?>, '<?= htmlspecialchars($a['nama_area'], ENT_QUOTES) ?>', <?= $a['kapasitas'] ?>)">✏️ Edit</button>
                        <a href="area_parkir.php?hapus=<?= $a['id_area'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus area \'<?= htmlspecialchars($a['nama_area'], ENT_QUOTES) ?>\'?')">🗑️ Hapus</a>
                    </div>
                </div>
                <?php endwhile; ?>

                <?php if($jumlah == 0): ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <span>🅿️</span>
                    <h3>Belum ada area parkir</h3>
                    <p>Tambahkan area parkir pertama Anda menggunakan form di atas.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT AREA -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <h3>✏️ Edit Area Parkir</h3>
            <form method="POST" action="">
                <input type="hidden" name="id_area" id="edit_id">
                <div class="form-group">
                    <label>Nama Area</label>
                    <input type="text" name="nama_area" id="edit_nama" required>
                </div>
                <div class="form-group">
                    <label>Total Kapasitas (Slot)</label>
                    <input type="number" name="kapasitas" id="edit_kapasitas" min="1" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEdit()">Batal</button>
                    <button type="submit" name="edit_area" class="btn-save">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // [SINTAKS JS]: Fungsi membuka modal edit dengan data area yang diklik
        function openEdit(id, nama, kapasitas) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_kapasitas').value = kapasitas;
            document.getElementById('editModal').classList.add('active');
        }

        // [SINTAKS JS]: Fungsi menutup modal edit
        function closeEdit() {
            document.getElementById('editModal').classList.remove('active');
        }

        // [SINTAKS JS]: Tutup modal jika klik di luar kotak
        document.getElementById('editModal').addEventListener('click', function(e) {
            if(e.target === this) closeEdit();
        });

        // [SINTAKS JS]: Auto-hide alert setelah 4 detik
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() { alert.remove(); }, 500);
            }
        }, 4000);
    </script>

</body>
</html>
