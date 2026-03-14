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

## Dokumentasi Perintah Sintaks PHP (Line-by-Line Guide)

Berikut adalah panduan penjelasan detail (perintah per baris) yang umum digunakan dalam seluruh baris kode aplikasi ini. Dokumentasi ini dibuat khusus untuk mempermudah saat sesi presentasi UKK:

### 1. Sistem Autentikasi & Keamanan Dasar
* `session_start();` : Semboyan wajib di awal file. Perintah ini mengaktifkan dan memulai Sesi (Session) browser agar server dapat mengingat identitas pengguna yang sedang login. Tanpa ini, sistem tidak tahu siapa yang sedang mengakses halaman.
* `$_SESSION['role'];` : Sebuah variabel *superglobal array* bawaan PHP yang digunakan untuk menyimpan data spesifik (misal hak akses) dari user aktif selama browser belum ditutup atau logout.
* `session_destroy();` : Menghapus dan menghancurkan seluruh memori Sesi (*Session*) dari server. Digunakan secara eksklusif dalam fitur Logout agar user benar-benar keluar dari sistem.

### 2. Manajemen Navigasi (Pengalihan Halaman)
* `header("location: filepath.php");` : Fungsi *Redirect*. Perintah otomatis dari PHP untuk langsung melempar atau memindahkan halaman user ke lokasi spesifik (`filepath.php`) tanpa harus klik link manual.
* `include 'config/koneksi.php';` : Berperan sebagai jembatan. Fungsi ini akan memanggil dan "menempelkan" seluruh isi file koneksi database ke dalam baris tempat perintah ini dipanggil, sehingga file saat ini punya akses penuh baca/tulis ke database.

### 3. Eksekusi Database (MySQLi)
* `mysqli_connect($host, $user, $pass, $db);` : Sintaks inisiasi koneksi gembok utama antara skrip aplikasi PHP dengan mesin Server Database MySQL. Wajib ada parameter host (IP server), username database, password, dan nama database target.
* `mysqli_query($koneksi, "SINTAKS SQL");` : Mengeksekusi instruksi data (*query*). Apapun sintaks SQL yang ada di dalamnya (baik itu `SELECT`, `INSERT`, `UPDATE`, maupun `DELETE`), perintah inilah yang menjadi *eksekutornya* dengan melemparkan permintaan tersebut ke dalam pipa `$koneksi` yang sedang aktif.
* `mysqli_fetch_assoc($query_result);` : Berfungsi sebagai penerjemah data. Mengambil data hasil eksekusi (yang masih berbentuk *raw object*) lalu merubah satu baris datanya ke dalam format "Associative Array" (array yang *key*-nya berupa nama kolom dari tabel).
* `mysqli_num_rows($query_result);` : Mesin penghitung otomatis. Akan mereturn angka numerik bulat (`integer`) yang merepresentasikan seberapa banyak Total Baris yang ditarik/terdapat pada hasil query eksekusi `SELECT`.

### 4. Manajemen Pertukaran Data (Request & Security)
* `$_POST['nama_input'];` : Menangkap data rahasia yang dikirimkan oleh form HTML meggunakan *method POST* (lewat *body request*, tidak terlihat di URL). Misalnya data dari kolom *Username* atau *Password*.
* `$_GET['parameter'];` : Menangkap data terbuka yang dilemparkan menempel pada alamat link (URL). Contoh url: `file.php?id=5`, maka `$_GET['id']` akan bernilai `5`.
* `mysqli_real_escape_string($koneksi, $parameter);` : *Bodyguard* aplikasi. Fungsi keamanan paling mendasar untuk membersihkan teks input dari karakter-karakter aneh (`'`, `"`, dll) sebelum masuk ke database. Fungsinya mutlak untuk mencegah penyerangan peretasan *SQL Injection*.
* `strtoupper($string);` : Manipulasi string PHP untuk mengubah satu kalimat utuh menjadi huruf kapital sepenuhnya *(Uppercase)*. Di sini banyak digunakan untuk menyeragamkan format Plat Nomor kendaraan.

### 5. Sintaks Pendukung Lanjutan
* `date_default_timezone_set('Asia/Jakarta');` : Sinkronisasi waktu. Memaksa fungsi *Date/Time* pada skrip PHP agar menggunakan zona waktu lokal WIB (Waktu Indonesia Barat).
* `date("Y-m-d H:i:s");` : Mencetak waktu mutlak saat ini untuk mesin *back-end*. Format tersebut adalah format standar *TIMESTAMP* yang dapat diterima oleh sistem database MySQL (Tahun-Bulan-Tanggal Jam:Menit:Detik).
* `mysqli_insert_id($koneksi);` : Ini adalah trik penting. Begitu perintah "INSERT" sukses dilakukan, fungsi ini seketika menangkap dan mengembalikan nilai unik `AUTO_INCREMENT` (ID Primary Key) terbaru yang barusan digenerate sistem MySQL, sangat bermanfaat untuk menghubungkan relasi antar tabel secara langsung (misal: ID Kendaraan -> ID Transaksi).
