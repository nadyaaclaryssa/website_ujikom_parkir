<?php
include 'config/koneksi.php';
$q = mysqli_query($koneksi, "DESCRIBE tb_log_aktivitas");
if($q) {
  while($r = mysqli_fetch_assoc($q)) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
  }
} else {
  echo mysqli_error($koneksi);
}
?>
