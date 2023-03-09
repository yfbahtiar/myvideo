<?php
header('Access-Control-Allow-Origin: *');
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
    if (isset($_GET['nextVid']) && isset($_GET['uid'])) {
        $id_video = $_GET['nextVid'];
        $id_viewer = $_GET['uid'];
        // cek kategorinya dulu
        $kategori = getKtgFromVideo($id_video);
        // cari dan tampilkan video untuk next play
        echo nextVideo($id_viewer, $id_video, $kategori);
        // create jejak tmp_view
        createNewTmpView($id_viewer, $id_video);
        exit;
    }
} else {
    $reps = array(
        'status' => 200,
        'data' => array(
            'server' => base_url(''),
            'name' => 'API My Video',
			'code' => '@yfbahtiar',
            'version' => '1.0'
        )
    );
    echo json_encode($reps);
    exit;
}
