<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/user_tambah.php
// -> Tujuan Spesifik: Modul/komponen Form Tambah Staff/Akun Pegawai Baru (Insert SQL Logic Data Biasa Polosan).
// -> Perhatian: Nampaknya file cadangan alternatif yang sederhana bentuk polosan Form Nya, belm digabung di kelola_user.php utama.
// ======================================

// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser mengingat identitas token Login si Pemasuk Link
session_start();

// [SINTAKS PHP]: Validasi Paksa Pemeriksaan Posisi Kuasa Privilege. (Cegah Peretas lompat url langsung kemari padahal cm Pegawai Karcis Parkir yg mau nambah ID palsu) 
if($_SESSION['role'] != "admin") {
    // [SINTAKS PHP]: header() | Fungsi pengalihan mendepak User Balik menuju Halaman Default Awal Front Page Form Loket!
    header("location:../../auth/index.php"); 
    exit;
}

// [SINTAKS PHP]: include | Tarik Konektor Driver Penghubung Portal Database Engine MySQL.
include '../../config/koneksi.php';

// ==== BLOK LOGIKA: TAMBAH (CREATE) USER / AKUN STAFF BARU EKSKLUSIF ====
// [SINTAKS PHP]: Pengecekan Eksistensi Tindakan Menekan Pelatuk Submit Form (Dengan Parameter Identifier 'submit') lewat Metodologi Kiriman Pos Paket Super Rahasia dan tak tercecer dimanapun (POST) 
if(isset($_POST['submit'])){
    
    // [SINTAKS PHP]: Merampas Parameter ketikan Formulir "nama" masukin ke Kantong Variabel $nama Lokal Buat ditarik sama MySQL Nanti
    $nama = $_POST['nama'];
    
    // [SINTAKS PHP]: Menjabret Ketikan "user" yg dipos si Admin dari Kotakan form di Html bawah
    $user = $_POST['user'];
    
    // [SINTAKS PHP]: md5() Enkripsi | Memotong Motong dan mengacak Sandi (Hashing) secara searah One-way Algoritma Hashing Kriptografik Menjadi String Panjang tak terbaca 32 karakter hexadecimal Acak. Supaya klo Kena Hack DB Nya Maling gabisa Liat/Maling Password Asli Pegawai tsb Tampil Keliatan Di DB PhpMyAdmin Telanjang mentah. (Meski skrg disarankan pakai Bcrypt/Argon seleranya. Buat UKK aja md5)
    $pass = md5($_POST['pass']);
    
    // [SINTAKS PHP]: Memetakan Parameter Privilege (Owner / Kasir(petugas) / Bos(Admin) )
    $role = $_POST['role'];

    // [SINTAKS PHP]: Misi Injeksi MySQL Murni | SQL Query Command Menembakan Panah Value secara Berurutan Koloms Values Ke Dalam Table TB_User Data. Setan Nilai Pertama NULL Krn Dia itu id_User Yg Bertindak Sbgai PRIMARY KEY AUTO INCREMENT Berhitung Sendiri Otomatis Di DB Server Gapelu ikut campur tangan dari Koding kita di Input. Diakhiri Angka 1 Sebagai Konstumasi Default Setting Status = Aktif
    $query = mysqli_query($koneksi, "INSERT INTO tb_user VALUES(NULL, '$nama', '$user', '$pass', '$role', 1)");
    
    // [SINTAKS PHP]: Pengecekan Kesuksesan Insiatif Misi Nambah data di Atas! (Benar Sukses = If jalan / Salah Gagal Ya Ngga Jalan)
    if($query) {
        // [SINTAKS PHP]: echo Javascript Tag | Memanggil Pop-Up Window Mungil Menawan Menyugestikan Alert Notifikasi Text 'Berhasil' dari Mulut Jendela Browser! lalu Maksa Browser Muter Balik Ganti Halaman Location Menuju 'dashboard.php' lewat Jalan JVScript Redirect. 
        echo "<script>alert('Berhasil!'); window.location='dashboard.php';</script>";
    }
}
?>

<!-- [SINTAKS HTML]: Susunan Frame Tulang Form Mentahan Polosan Standar -->
<!-- Action Form Kosong (Dibiarkan agar Submit ke File Itu Dirinya Sendiri Ngerender Ulang). Metodenya pake POST yg lebih misterius tak tercium link -->
<form method="post">
    <!-- [SINTAKS HTML]: Kotak Panjang Input Tulisan NAMA LENGKAP tipe Teks dilindungi Sifat Required Ga boleh Kosong blong pas klik Simpan -->
    <input type="text" name="nama" placeholder="Nama Lengkap" required><br>
    
    <!-- [SINTAKS HTML]: Kotak Panjang Input Alias User Akses Akun (Spesial Teks Bebas Spasi Idealnya) -->
    <input type="text" name="user" placeholder="Username" required><br>
    
    <!-- [SINTAKS HTML]: Perlindungan Lapis Pandang Input Typo Passoword! Titik TItik Bintang Obfuscation Bawaan HTML 5 Mengunci penglihatan Spys disebelah kita -->
    <input type="password" name="pass" placeholder="Password" required><br>
    
    <!-- [SINTAKS HTML]: Dropdon Combo Box Pilihan Ekslusif Tunggal Peran/Role Akses Level Hak Karyawan Bersangkutan. Menyetel Pilihan Ke Database-->
    <select name="role">
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
        <option value="owner">Owner</option>
    </select><br>
    
    <!-- [SINTAKS HTML]: Trigerr Pemicu Aksi Tembakan Penyerahan Serah Terima Data Input Ke dalam Pelukan Array Post Paling atas File PHP tadi -->
    <button type="submit" name="submit">Simpan</button>
</form>
