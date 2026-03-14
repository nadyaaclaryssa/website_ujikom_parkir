
<?php 
// [SINTAKS PHP]: session_start() | Memulai sesi (session) browser untuk menyimpan data login pengguna agar sistem mengingat identitasnya
session_start();
// [SINTAKS PHP]: session_destroy() | Menghancurkan dan membersihkan semua data Session server untuk proses Logout menyeluruh
session_destroy();
// [SINTAKS PHP]: header() | Fungsi untuk melakukan Redirect (pengalihan otomatis) ke lokasi halaman web tertentu
header("location:index.php");
?>

