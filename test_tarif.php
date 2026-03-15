<?php
include 'config/koneksi.php';
$q = mysqli_query($koneksi, "DESCRIBE tb_tarif");
while($row = mysqli_fetch_assoc($q)) {
    print_r($row);
}
?>
