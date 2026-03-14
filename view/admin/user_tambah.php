
<?php
// === DOKUMENTASI KLASIFIKASI FILE ===
// -> Nama File: view/admin/user_tambah.php
// -> Tujuan Spesifik: Modul/komponen fungsional spesifik aplikasi Smart Parking System.
// -> Penjelasan ini digenerate secara khusus untuk membantu penjabaran materi presentasi UKK RPL agar terstruktur.
// ======================================
// [SINTAKS PHP]: session_start() Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
if($_SESSION['role'] != "admin") // [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:../../auth/index.php");
// [SINTAKS PHP]: include | Menyertakan file konfigurasi koneksi ke server MySQL Database agar tabel bisa dibaca/ditulis
include '../../config/koneksi.php';

if(isset(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['submit'])){
    $nama = // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['nama'];
    $user = // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['user'];
    $pass = // [SINTAKS PHP]: md5() | Enkripsi hashing string satu arah menggunakan metode standar MD5 Message-Digest Algorithm
md5(// [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['pass']);
    $role = // [SINTAKS PHP]: $_POST | Mengambil sekumpulan data yang di-submit dan dikirim melalui form-method POST
$_POST['role'];

    $query = // [SINTAKS PHP]: mysqli_query() | Digunakan untuk mengeksekusi perintah sintaks SQL ke dalam database yang aktif
mysqli_query($koneksi, "INSERT INTO tb_user VALUES(NULL, '$nama', '$user', '$pass', '$role', 1)");
    if($query) // [SINTAKS PHP]: echo JS | Men-cetak sintaks javascript HTML untuk memunculkan pesan (Alert Pop-up) interaktif pada browser
echo "<script>alert('Berhasil!'); window.location='dashboard.php';</script>";
}
?>
<form method="post">
    <input type="text" name="nama" placeholder="Nama Lengkap" required><br>
    <input type="text" name="user" placeholder="Username" required><br>
    <input type="password" name="pass" placeholder="Password" required><br>
    <select name="role">
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
        <option value="owner">Owner</option>
    </select><br>
    <button type="submit" name="submit">Simpan</button>
</form>

