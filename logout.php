<?php
// hapus data tmp_view
mysqli_query($conn, "DELETE FROM tmp_viewers WHERE id_user = '$session'");

//di website tidak pakai session start
session_start();
$_SESSION = [];
session_unset();
session_destroy();


$_SESSION['pesan'] = 'Berhasil logout.';
// di website nya pake js
//echo "<script>window.location.href = 'login.php';</script>";
header("Location: index.php");
exit;
