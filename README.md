# PARLINE — Smart Parking Management System

> Aplikasi Manajemen Parkir berbasis Web (PHP Native + MySQL)
> Dibuat untuk Ujian Kompetensi Keahlian (UKK) — Rekayasa Perangkat Lunak

---

## Deskripsi Proyek

**PARLINE** adalah sistem manajemen parkir berbasis web yang dibangun menggunakan **PHP Native** dan **MySQL**. Aplikasi ini dirancang untuk mengelola seluruh proses parkir mulai dari kendaraan masuk, pencatatan transaksi, perhitungan biaya otomatis, hingga cetak struk dan laporan pendapatan.

Sistem ini memiliki **3 level akses pengguna** (Role-Based Access Control):

| Role | Hak Akses |
|------|-----------|
| **Admin** | Kelola user, tarif, area parkir, lihat log aktivitas |
| **Petugas** | Input transaksi masuk/keluar, cetak struk |
| **Owner** | Lihat dashboard pendapatan, laporan detail, diagram analisis |

---

## Struktur Direktori Proyek

```
ukk_aplikasi_parkir/
│
├── config/
│   └── koneksi.php              # Koneksi database MySQL
│
├── admin/
│   ├── dashboard.php            # Dashboard statistik admin
│   ├── kelola_user.php          # CRUD pengguna (tambah + hapus inline)
│   ├── user_index.php           # Daftar user (versi tabel Hogwarts)
│   ├── user_tambah.php          # Form tambah user sederhana
│   ├── user_hapus.php           # Proses hapus user
│   ├── tarif_parkir.php         # CRUD tarif parkir (desain modern)
│   ├── tarif_index.php          # Daftar tarif (versi tabel Hogwarts)
│   ├── tarif_edit.php           # Form edit tarif
│   ├── area_parkir.php          # Informasi area parkir
│   ├── log_aktivitas.php        # Log aktivitas sistem (filter tanggal)
│   └── laporan.php              # Laporan pendapatan harian
│
├── petugas/
│   ├── dashboard.php            # Monitoring area real-time
│   ├── transaksi_masuk.php      # Form input kendaraan masuk
│   ├── transaksi_keluar.php     # Form checkout kendaraan
│   ├── proses_masuk.php         # Proses simpan transaksi masuk
│   ├── proses_keluar.php        # Proses hitung biaya + update status
│   ├── cetak_struk.php          # Cetak karcis masuk
│   └── cetak_struk_keluar.php   # Cetak struk pembayaran keluar
│
├── owner/
│   ├── dashboard.php            # Dashboard pendapatan + chart
│   ├── detail_laporan.php       # Tabel detail semua transaksi keluar
│   └── diagram_pendapatan.php   # Grafik tren pendapatan 7 hari
│
├── index.php                    # Halaman login utama
├── proses_login.php             # Proses autentikasi + log aktivitas
├── logout.php                   # Proses logout (destroy session)
├── hogwarts-removebg-preview.png # Logo aplikasi
├── logoo.png                    # Logo alternatif
├── schema_db.txt                # SQL schema database
├── all_code.txt                 # Kumpulan seluruh source code
└── README.md                    # Dokumentasi ini
```

---

## Skema Database (`ukk_parkir`)

### Tabel `tb_user`
Menyimpan data pengguna sistem.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_user` | INT, PK, AI | Primary key, auto increment |
| `nama_lengkap` | VARCHAR(100) | Nama lengkap pengguna |
| `username` | VARCHAR(50), UNIQUE | Username untuk login |
| `password` | VARCHAR(255) | Password (format MD5) |
| `role` | ENUM('admin','petugas','owner') | Level akses pengguna |
| `status_aktif` | TINYINT(1) | 1 = Aktif, 0 = Non-Aktif |

### Tabel `tb_tarif`
Menyimpan data tarif parkir per jenis kendaraan.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_tarif` | INT, PK, AI | Primary key |
| `jenis_kendaraan` | VARCHAR(50) | Jenis kendaraan (motor/mobil) |
| `harga_per_jam` | INT | Harga per jam |
| `tarif_per_jam` | INT | Alias harga (dipakai di JOIN) |

### Tabel `tb_area`
Menyimpan data area/zona parkir.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_area` | INT, PK, AI | Primary key |
| `nama_area` | VARCHAR(100) | Nama area (Lantai 1, Blok A) |
| `kapasitas` | INT | Jumlah slot parkir |

### Tabel `tb_transaksi`
Menyimpan seluruh data transaksi parkir.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_transaksi` | INT, PK, AI | Primary key |
| `plat_nomor` | VARCHAR(20) | Nomor plat kendaraan |
| `jenis_kendaraan` | VARCHAR(50) | Motor / Mobil |
| `id_tarif` | INT, FK | Relasi ke tb_tarif |
| `id_area` | INT, FK | Relasi ke tb_area |
| `waktu_masuk` | DATETIME | Waktu kendaraan masuk |
| `waktu_keluar` | DATETIME | Waktu kendaraan keluar (NULL saat masuk) |
| `biaya_total` | INT | Total biaya (0 saat masuk, terisi saat keluar) |
| `status` | ENUM('masuk','keluar') | Status transaksi |
| `petugas` | VARCHAR(100) | ID/nama petugas |

### Tabel `tb_log_aktivitas`
Mencatat log login pengguna.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_log` | INT, PK, AI | Primary key |
| `id_user` | INT, FK | Relasi ke tb_user |
| `aktivitas` | VARCHAR(255) | Deskripsi aktivitas |
| `waktu` | DATETIME | Waktu aktivitas |

### Tabel `tb_log`
Log aktivitas sistem (format alternatif).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_log` | INT, PK, AI | Primary key |
| `user_petugas` | VARCHAR(100) | Nama user/petugas |
| `aktivitas` | VARCHAR(255) | Deskripsi aktivitas |
| `waktu` | DATETIME | Waktu aktivitas |

### Diagram Relasi Antar Tabel

```
tb_user ──────────────┐
                      │ (id_user)
                      ▼
                tb_log_aktivitas

tb_tarif ─────────────┐
                      │ (id_tarif)
                      ▼
tb_area ──────── tb_transaksi
         (id_area)
```

---

## Dokumentasi Per File (Detail Setiap Baris Kode)

---

### `config/koneksi.php`

**Fungsi:** Membuat koneksi ke database MySQL.

```php
<?php
// ── Baris 3: Variabel host — alamat server database (localhost = server lokal)
$host = "localhost";

// ── Baris 4: Variabel user — username MySQL (default XAMPP = "root")
$user = "root";

// ── Baris 5: Variabel pass — password MySQL (default XAMPP = kosong "")
$pass = "";

// ── Baris 6: Variabel db — nama database yang digunakan
$db   = "ukk_parkir";

// ── Baris 8: mysqli_connect() — fungsi bawaan PHP untuk membuat koneksi ke MySQL
// Parameter: (host, username, password, nama_database)
// Hasilnya disimpan di variabel $koneksi yang akan digunakan di seluruh file
$koneksi = mysqli_connect($host, $user, $pass, $db);

// ── Baris 10-12: Pengecekan koneksi
// Jika $koneksi bernilai false/null, artinya koneksi gagal
// die() — menghentikan eksekusi dan menampilkan pesan error
// mysqli_connect_error() — mengembalikan pesan error dari MySQL
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
```

---

### 📁 `index.php`

**Fungsi:** Halaman login utama. Menampilkan form login dengan desain split-screen (branding kiri, form kanan).

| Bagian | Baris | Penjelasan |
|--------|-------|------------|
| **HTML Head** | 1-7 | Deklarasi DOCTYPE, charset UTF-8, viewport responsive, title, Google Fonts (Plus Jakarta Sans) |
| **CSS Variables** | 9-15 | Mendefinisikan warna tema: `--primary-blue` (#3b82f6), `--royal-blue`, `--bg-gradient`, `--text-main` |
| **Body Style** | 19-24 | Flexbox center, min-height 100vh, background gradient |
| **Login Container** | 26-32 | Max-width 900px, border-radius 40px, flexbox horizontal, box-shadow biru |
| **Brand Side** | 35-43 | Sisi kiri: logo, nama app "PARLINE", subtitle. Background #fcfdfe |
| **Form Side** | 46-52 | Sisi kanan: judul "Selamat Datang", form input |
| **Input Styling** | 54-66 | Input rounded 16px, border 2px, focus effect dengan box-shadow biru |
| **Button** | 68-76 | Tombol login full-width, gradient biru, hover translateY(-2px) |
| **Responsive** | 79-83 | @media max-width 768px: layout berubah jadi vertikal (column) |
| **Form HTML** | 99-111 | `<form action="proses_login.php" method="POST">` — mengirim data ke proses_login.php |
| **Input Username** | 102 | `name="username"` — atribut name untuk diambil di PHP via `$_POST['username']` |
| **Input Password** | 107 | `type="password"` — otomatis menyembunyikan karakter yang diketik |
| **Tombol Submit** | 110 | `name="login"` — digunakan untuk pengecekan `isset($_POST['login'])` di proses_login.php |

---

### 📁 `proses_login.php`

**Fungsi:** Memproses autentikasi login, menyimpan session, mencatat log, dan mengarahkan berdasarkan role.

| Baris | Kode | Penjelasan |
|-------|------|------------|
| 2 | `session_start()` | Memulai session PHP agar bisa menyimpan data antar halaman |
| 3 | `include 'config/koneksi.php'` | Memanggil file koneksi agar variabel `$koneksi` tersedia |
| 4 | `date_default_timezone_set('Asia/Jakarta')` | Set zona waktu ke WIB agar waktu log akurat |
| 6 | `if (isset($_POST['login']))` | Cek apakah form login sudah di-submit (tombol name="login" diklik) |
| 7 | `mysqli_real_escape_string()` | **Sanitasi input** — mencegah SQL Injection dengan meng-escape karakter khusus |
| 9 | `md5($password)` | Meng-hash password dengan algoritma MD5 untuk dicocokkan dengan database |
| 12 | `SELECT * FROM tb_user WHERE...` | Query SQL untuk mencari user dengan username DAN password yang cocok |
| 13 | `mysqli_num_rows($query)` | Menghitung jumlah baris hasil query (>0 = user ditemukan) |
| 16 | `mysqli_fetch_assoc($query)` | Mengambil 1 baris data sebagai array asosiatif (key = nama kolom) |
| 19-26 | `$_SESSION[...]` | Menyimpan data user ke session: id_user, username, nama_lengkap, role |
| 24 | `$_SESSION['nama']` | Duplikat dari nama_lengkap — digunakan di dashboard petugas |
| 33 | `INSERT INTO tb_log_aktivitas` | Mencatat log bahwa user berhasil login beserta waktu login |
| 36-42 | `if($data['role'] == ...)` | **Routing berdasarkan role** — admin→admin/, petugas→petugas/, owner→owner/ |
| 43 | `exit` | Menghentikan eksekusi setelah redirect agar kode di bawahnya tidak jalan |
| 45 | `echo "<script>alert(...)"` | Jika login gagal, tampilkan popup alert dan redirect kembali ke index.php |

---

### 📁 `logout.php`

**Fungsi:** Menghapus session dan redirect ke halaman login.

| Baris | Kode | Penjelasan |
|-------|------|------------|
| 2 | `session_start()` | Wajib dipanggil sebelum `session_destroy()` |
| 3 | `session_destroy()` | Menghapus SEMUA data session (user keluar dari sistem) |
| 4 | `header("location:index.php")` | Redirect ke halaman login |

---

### 📁 `admin/dashboard.php`

**Fungsi:** Dashboard admin menampilkan statistik (pendapatan, kendaraan masuk, slot, petugas) dan tabel 5 log transaksi terakhir.

| Bagian | Penjelasan |
|--------|------------|
| **Proteksi Role (baris 3)** | `if($_SESSION['role'] != "admin")` — Jika bukan admin, tendang ke login |
| **Query Statistik (baris 7-10)** | 4 query SQL: SUM biaya_total, COUNT masuk, COUNT petugas, hitung sisa slot (1350 - masuk) |
| **Sidebar** | Logo Parline, navigasi: Dashboard, Data User, Data Tarif, Data Area, progress bar slot, tombol Logout |
| **Stats Grid** | 4 kartu statistik dalam CSS Grid 4 kolom. Kartu pertama pakai gradient biru (primary-card) |
| **Tabel Log** | Query 5 transaksi terakhir ORDER BY DESC. Menampilkan petugas, jenis kendaraan (badge), plat (monospace), waktu, biaya |
| **number_format()** | Fungsi PHP untuk format angka: `number_format(5000, 0, ',', '.')` → "5.000" |

---

### 📁 `admin/kelola_user.php`

**Fungsi:** Halaman kelola user dengan form tambah user inline dan tabel daftar user + tombol hapus.

| Bagian | Penjelasan |
|--------|------------|
| **Proses Simpan (baris 11-31)** | Cek duplikat username → INSERT INTO tb_user → redirect |
| **Proses Hapus (baris 34-39)** | DELETE FROM tb_user WHERE id_user = GET parameter |
| **Form Grid** | CSS Grid 5 kolom: Nama, Username, Password, Role (select), Tombol Simpan |
| **Tabel User** | Loop `while($data = mysqli_fetch_assoc())` — menampilkan nomor, nama, username, role (badge warna), tombol hapus |
| **Role Badge** | Class dinamis: `role-ADMIN` (merah), `role-PETUGAS` (hijau), `role-OWNER` (kuning) |
| **Konfirmasi Hapus** | `onclick="return confirm('Hapus?')"` — popup konfirmasi sebelum hapus |

---

### 📁 `admin/tarif_parkir.php`

**Fungsi:** CRUD tarif parkir — form tambah tarif dan tabel daftar tarif dengan tombol hapus.

| Bagian | Penjelasan |
|--------|------------|
| **Tambah Tarif** | INSERT INTO tb_tarif (jenis_kendaraan, harga_per_jam) |
| **Hapus Tarif** | DELETE FROM tb_tarif WHERE id_tarif = GET parameter |
| **Form** | 2 input: jenis kendaraan (text) + harga per jam (number) |
| **Tabel** | Menampilkan ID, kategori (UPPERCASE), harga (formatted), tombol hapus |
| **Null Coalescing** | `$data['harga_per_jam'] ?? $data['harga'] ?? 0` — fallback chain |

---

### 📁 `admin/tarif_edit.php`

**Fungsi:** Form edit tarif per jam untuk jenis kendaraan tertentu.

| Bagian | Penjelasan |
|--------|------------|
| **Ambil Data** | SELECT dari tb_tarif berdasarkan ID dari GET parameter |
| **Update** | UPDATE tb_tarif SET tarif_per_jam WHERE id_tarif |
| **Alert** | Setelah update sukses, tampilkan alert + redirect ke tarif_index.php |

---

### 📁 `admin/tarif_index.php`

**Fungsi:** Daftar tarif parkir (versi desain Hogwarts dengan sidebar marun).

| Bagian | Penjelasan |
|--------|------------|
| **Array** | Data tarif disimpan ke array `$semua_tarif[]` menggunakan while loop |
| **Ikon Kendaraan** | `strpos()` untuk deteksi "motor" → 🛵, "mobil" → 🚗, "lainnya" → 🚚 |
| **Link Edit** | Mengarah ke `tarif_edit.php?id=` dengan parameter id_tarif |

---

### 📁 `admin/area_parkir.php`

**Fungsi:** Menampilkan informasi area parkir (data simulasi dari array PHP).

| Bagian | Penjelasan |
|--------|------------|
| **Array Simulasi** | Data area dalam array asosiatif: nama, lokasi, total kapasitas, terisi |
| **Perhitungan** | `$persen = (terisi / total) * 100` dan `$sisa = total - terisi` |
| **Kartu Area** | Foreach loop menampilkan setiap area: nama, lokasi, kapasitas, slot tersedia, progress bar, persentase |

---

### 📁 `admin/log_aktivitas.php`

**Fungsi:** Menampilkan log aktivitas sistem dengan filter tanggal dan pencarian.

| Bagian | Penjelasan |
|--------|------------|
| **Filter** | GET parameter `tanggal` dan `search` — ditambahkan ke WHERE clause |
| **Query Dinamis** | String query dibangun secara kondisional: `$query_str .= " AND ..."` |
| **Auto Submit** | `onchange="this.form.submit()"` — form otomatis submit saat pilih tanggal/ketik |
| **Tampilan** | Tabel 3 kolom: Waktu, User/Petugas, Aktivitas. Jika kosong tampilkan pesan |

---

### 📁 `admin/laporan.php`

**Fungsi:** Laporan pendapatan harian (hanya transaksi yang sudah keluar).

| Bagian | Penjelasan |
|--------|------------|
| **Query** | `WHERE status='keluar'` — hanya ambil transaksi yang sudah selesai |
| **SUM** | `SUM(biaya_total)` — total seluruh pendapatan |
| **Summary Box** | Kotak total pendapatan di atas tabel |

---

### 📁 `admin/user_index.php`, `user_tambah.php`, `user_hapus.php`

**Fungsi:** Versi alternatif kelola user (desain Hogwarts marun).

| File | Penjelasan |
|------|------------|
| `user_index.php` | Tabel user dengan kolom Status Aktif (badge hijau/merah), tombol Edit & Hapus |
| `user_tambah.php` | Form sederhana tanpa styling: nama, username, password (MD5), role → INSERT |
| `user_hapus.php` | Proteksi role → DELETE user berdasarkan ID → alert + redirect |

---

### 📁 `petugas/dashboard.php`

**Fungsi:** Dashboard petugas menampilkan monitoring area parkir real-time.

| Bagian | Penjelasan |
|--------|------------|
| **Query Area** | `SELECT * FROM tb_area` — mengambil semua area dari database |
| **Kartu Area** | Grid card per area: nama, kapasitas, slot tersedia (angka besar merah), tombol Check-In |
| **Check-In Link** | `transaksi_masuk.php?area=` — membawa nama area sebagai parameter |
| **Session Nama** | `$_SESSION['nama']` — menampilkan nama petugas yang login + inisial di avatar |
| **Storage Box** | Progress bar menunjukkan persentase slot terpakai dari 1350 total |

---

### 📁 `petugas/transaksi_masuk.php`

**Fungsi:** Form input kendaraan masuk (plat nomor, jenis kendaraan, area parkir).

| Bagian | Penjelasan |
|--------|------------|
| **Area Terpilih** | `$_GET['area']` — dari link Check-In di dashboard, otomatis selected di dropdown |
| **Dropdown Tarif** | Query tb_tarif → loop option: `<option value='id_tarif'>jenis_kendaraan</option>` |
| **Dropdown Area** | Query tb_area → loop option dengan `selected` otomatis jika cocok |
| **Submit** | Form dikirim ke `proses_masuk.php` method POST |

---

### 📁 `petugas/proses_masuk.php`

**Fungsi:** Memproses penyimpanan transaksi kendaraan masuk ke database.

| Baris | Penjelasan |
|-------|------------|
| 8 | `strtoupper()` — konversi plat nomor ke huruf besar |
| 14 | `$_SESSION['id_user']` — ID petugas dari session login |
| 17-19 | Query tb_tarif untuk mendapatkan nama jenis_kendaraan berdasarkan id_tarif |
| 22-23 | INSERT ke tb_transaksi dengan status='masuk' dan biaya_total=0 |
| 26 | `mysqli_insert_id()` — mengambil ID terakhir yang baru di-insert |
| 28 | Redirect ke cetak_struk.php dengan parameter ID |

---

### 📁 `petugas/proses_keluar.php`

**Fungsi:** Proses checkout — cari transaksi, hitung durasi & biaya, update status ke 'keluar'.

| Baris | Penjelasan |
|-------|------------|
| 9 | `strtoupper()` — normalisasi input plat nomor |
| 13-17 | **JOIN query** — tb_transaksi JOIN tb_tarif untuk dapat tarif_per_jam |
| 22-24 | `DateTime` + `diff()` — menghitung selisih waktu masuk dan keluar |
| 27-29 | Hitung jam: `$diff->h + (days * 24)`, jika ada menit/detik lebih → tambah 1 jam, minimum 1 jam |
| 31 | `$total_bayar = $jam * tarif_per_jam` — perhitungan biaya |
| 34-38 | UPDATE transaksi: set waktu_keluar, biaya_total, status='keluar' |
| 42 | Redirect ke cetak_struk_keluar.php |
| 48 | Jika plat tidak ditemukan/sudah keluar → alert + redirect balik |

---

### 📁 `petugas/cetak_struk.php`

**Fungsi:** Cetak karcis parkir masuk (format struk thermal 280px).

| Bagian | Penjelasan |
|--------|------------|
| **Query** | SELECT transaksi berdasarkan ID dari GET parameter |
| **Validasi** | `if (!$r) die(...)` — jika data tidak ditemukan, tampilkan error |
| **Auto Print** | `<body onload="window.print()">` — otomatis buka dialog print |
| **Format** | Monospace font, header "HOGWARTS", plat besar, info kendaraan, petugas |
| **@media print** | Class `.no-print` disembunyikan saat cetak (tombol Kembali) |

---

### 📁 `petugas/cetak_struk_keluar.php`

**Fungsi:** Cetak struk pembayaran keluar (lebih detail dari karcis masuk).

| Bagian | Penjelasan |
|--------|------------|
| **JOIN Query** | Gabung 3 tabel: tb_transaksi + tb_tarif + tb_area → dapat tarif & nama area |
| **Durasi** | Dihitung ulang menggunakan DateTime diff (sama seperti proses_keluar.php) |
| **Info Lengkap** | ID transaksi, plat, jenis, area, waktu masuk/keluar, durasi, tarif/jam, TOTAL BAYAR |
| **Status** | "LUNAS" + ucapan terima kasih |

---

### 📁 `owner/dashboard.php`

**Fungsi:** Dashboard owner menampilkan ringkasan pendapatan harian/bulanan dan grafikChart.js.

| Bagian | Penjelasan |
|--------|------------|
| **Query Hari Ini** | `WHERE DATE(waktu_keluar) = '$hari_ini'` — pendapatan hari ini |
| **Query Bulan** | `DATE_FORMAT(waktu_keluar, '%Y-%m')` — pendapatan bulan ini |
| **Chart.js** | Library JavaScript untuk membuat grafik bar — pendapatan per hari |
| **Stats Grid** | 3 kartu: Pendapatan Hari Ini (biru), Pendapatan Bulan Ini (highlight), Total Kendaraan Keluar |
| **Tombol Cetak** | `onclick="window.print()"` — cetak halaman dashboard |

---

### 📁 `owner/detail_laporan.php`

**Fungsi:** Tabel detail seluruh transaksi yang sudah keluar.

| Bagian | Penjelasan |
|--------|------------|
| **Query** | `WHERE status='keluar' ORDER BY waktu_keluar DESC` |
| **Tabel** | No, Plat Nomor, Jenis (badge), Waktu Masuk, Waktu Keluar, Total Bayar (badge hijau) |
| **Tombol Cetak** | Print laporan lengkap |

---

### 📁 `owner/diagram_pendapatan.php`

**Fungsi:** Grafik tren pendapatan 7 hari terakhir menggunakan Chart.js (line chart).

| Bagian | Penjelasan |
|--------|------------|
| **Loop 7 Hari** | `for ($i = 6; $i >= 0; $i--)` — ambil data 7 hari terakhir secara otomatis |
| **Query per Hari** | SUM biaya_total per tanggal dari transaksi keluar |
| **json_encode()** | Mengkonversi array PHP ke format JSON untuk dipakai di JavaScript |
| **Line Chart** | Garis melengkung (tension: 0.4), warna marun, area di bawah garis diisi gradient transparan |
| **Format Rupiah** | `value.toLocaleString('id-ID')` — format angka Indonesia di sumbu Y |

---

## ⚙️ Cara Instalasi & Menjalankan

### Prasyarat
- **XAMPP** (Apache + MySQL + PHP) atau server lokal sejenis
- **Browser** modern (Chrome, Firefox, Edge)

### Langkah-Langkah

1. **Clone / Copy** folder proyek ke direktori `htdocs` XAMPP:
   ```
   C:\xampp\htdocs\ukk_aplikasi_parkir\
   ```

2. **Buat database** di phpMyAdmin:
   - Buka `http://localhost/phpmyadmin`
   - Buat database baru bernama `ukk_parkir`
   - Import file `schema_db.txt` (atau jalankan query-nya secara manual)

3. **Konfigurasi koneksi** (jika perlu):
   - Edit `config/koneksi.php`
   - Sesuaikan `$host`, `$user`, `$pass`, `$db`

4. **Akses aplikasi**:
   ```
   http://localhost/ukk_aplikasi_parkir/
   ```

5. **Login** dengan akun default:

   | Username | Password | Role |
   |----------|----------|------|
   | admin | admin123 | Admin |
   | petugas1 | admin123 | Petugas |
   | owner | admin123 | Owner |

---

## 🔑 Fitur Utama

| No | Fitur | Deskripsi |
|----|-------|-----------|
| 1 | **Login Multi-Role** | Autentikasi dengan routing otomatis per role |
| 2 | **CRUD User** | Admin bisa tambah, lihat, hapus user |
| 3 | **CRUD Tarif** | Admin kelola tarif parkir per jenis kendaraan |
| 4 | **Manajemen Area** | Monitoring slot parkir per zona |
| 5 | **Transaksi Masuk** | Petugas input kendaraan → cetak karcis otomatis |
| 6 | **Transaksi Keluar** | Cari plat → hitung durasi & biaya → cetak struk |
| 7 | **Laporan Pendapatan** | Owner lihat detail pendapatan harian & bulanan |
| 8 | **Grafik Chart.js** | Visualisasi tren pendapatan 7 hari (bar + line chart) |
| 9 | **Log Aktivitas** | Catatan login user dengan filter tanggal & pencarian |
| 10 | **Cetak Struk** | Auto-print karcis masuk dan struk pembayaran |

---

## 🛡️ Teknologi yang Digunakan

| Teknologi | Kegunaan |
|-----------|----------|
| **PHP 7/8** | Backend logic, server-side processing |
| **MySQL / MariaDB** | Database relasional |
| **HTML5 + CSS3** | Struktur dan styling halaman |
| **JavaScript** | Interaktivitas (Chart.js, window.print) |
| **Chart.js** | Library grafik (bar chart, line chart) |
| **Google Fonts** | Typography (Plus Jakarta Sans, Inter) |
| **Session PHP** | Manajemen autentikasi dan otorisasi |

---

## 📝 Catatan Penting

- Password disimpan menggunakan **MD5** (untuk kebutuhan UKK). Untuk produksi, disarankan menggunakan `password_hash()` dan `password_verify()`
- Kapasitas total parkir di-hardcode **1350 slot** di beberapa halaman
- Field `tarif_per_jam` dan `harga_per_jam` di tb_tarif harus selalu sinkron
- Beberapa halaman admin memiliki 2 versi desain (modern biru & Hogwarts marun)

---

## 👩‍💻 Dibuat Oleh

Proyek UKK — Rekayasa Perangkat Lunak

---

*Dokumentasi ini di-generate pada 14 Maret 2026*
