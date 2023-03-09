<?php
// require
require '../function.php';

if (isset($_GET['page']) && $_GET['page'] == 'getData') {

    // minta data di home..
    // data kategori
    if (isset($_GET['kategori'])) {
        $opsi = $_GET['kategori'];
        // opsi : aktif n kosong
        echo getKategori($opsi);
        exit;
    }

    // data 5 video teratas
    if (isset($_GET['videoPopuler'])) {
        $jumlah = $_GET['videoPopuler'];
        echo getVideoPopuler($jumlah);
        exit;
    }

    // data 10 video terbaru
    if (isset($_GET['videoNew'])) {
        $jumlah = $_GET['videoNew'];
        echo getVideoNew($jumlah);
        exit;
    }

    // minta data di search..
    if (isset($_GET['q'])) {
        $cari = mysqli_escape_string($conn, htmlspecialchars($_GET['q'], ENT_QUOTES));
        echo resultSearch($cari, 1, 0, 10);
        exit;
    }

    // minta data di view video..
    // data untuk diputar
    if (isset($_GET['v'])) {
        $id_video = $_GET['v'];
        // tampilkan hasil
        echo getVidToPlay($id_video);
        exit;
    }

    // data untuk next video
    if (isset($_GET['nextVid'])) {
        $id_video = $_GET['nextVid'];
        // cek kategorinya dulu
        $kategori = getKtgFromVideo($id_video);
        // hitung jmlh video di katg tersebut
        $jmlVideo = countVidInKtg($kategori);
        // jika hanya 2 cari lain
        // > 2 sikattt
        if ($jmlVideo > 1)
            echo nextVidByKategori($id_video, $kategori);
        else
            echo nextVidOtherKategori($id_video);
        exit;
    }
}

// $query = $conn->query("SELECT * FROM categories");
// // $data = array();
// while ($d = mysqli_fetch_assoc($query)) {
//     $data[] = array(
//         'id' => $d['id'],
//         'judul' => $d['judul']
//     );
// }
// echo '<pre>';
// print_r($data);
// echo json_encode($data);
// echo '</pre>';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API MyVideo v1.0</title>
</head>

<body>
    <h1>Hello World!</h1>
</body>

</html>