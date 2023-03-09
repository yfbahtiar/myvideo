<?php

// mematikan semua error reporting
error_reporting(0);

// set locale time
date_default_timezone_set('Asia/Jakarta');

// base url
function base_url($link)
{
    $url = 'http://localhost/myvideo/';
    // $url = 'http://192.168.100.2/myvideo/';
    // $url = 'http://172.16.1.14/myvideo/';
	// $url = 'http://192.168.100.21/myvideo/';
    if ($link) {
        $toUrl = $url . $link;
        return $toUrl;
    } else {
        return $url;
    }
}

// koneksi db
// $conn = mysqli_connect("localhost", "id16027313_menpc3o", "mAWH611}VGhS=S44", "id16027313_myapps") or die("gagal konek :(");
$conn = mysqli_connect("localhost", "root", "", "myvideo") or die("gagal konek db:(");

// generate link dg acak huruf
function generateVideoLink($panjang)
{
    // tentukan karakter random
    $karakter = '0987654321_AaBbCcDdEeF-fGgHhIiJjKkLl_MmNnOoPpQqRrSsTtUuVv_WwXxYyZz-01234567890_';
    // buat variabel kosong untuke kembalian nilai retur
    $string = '';
    // lakukan proses looping 
    for ($i = 0; $i < $panjang; $i++) {
        // lakukan random data dengan nilai awal 0 dan nilai akhir sebanyak value 'karakter'
        $pos = rand(0, strlen($karakter) - 1);
        // melakukan penggabungan antara nilai karakter di kiri dengan nilai di kanan
        $string .= $karakter[$pos];
    }
    // kembalikan nilai dari function ke echo
    return $string;
}

// fungsi tammpil bulan indo
function month($month, $format = "mmmm")
{
    if ($format == "mmmm") {
        $fm = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
    } elseif ($format == "mmm") {
        $fm = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
    }

    return $fm[$month - 1];
}

// fungsi tammpil hari indo
function day($day, $format = "dddd")
{
    if ($format == "dddd") {
        $fd = array("Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Minggu");
    } elseif ($format == "ddd") {
        $fd = array("Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min");
    }

    return $fd[$day - 1];
}

// fungsi cek umur
function cekUmur($birthDate)
{
    $lahir = new DateTime(date("Y-m-d", $birthDate));
    $today = new DateTime('today');
    $y = $today->diff($lahir)->y;
    return $y;
}

// Outputnya : Februari
// month(2);

// Outputnya : Feb
// month(2, 'mmm');

// Outputnya : 16 Mei 2018
// date("d") . " " . month(date("n")) . " " . date("Y");

// Outputnya : Rabu, 16 Mei 2018
// day(date("N")) . ", " . date("d") . " " . month(date("n")) . " " . date("Y");

// Outputnya : Rab, 16 Mei 2018
// day(date("N"), 'ddd') . ", " . date("d") . " " . month(date("n")) . " " . date("Y");

// $tanggal = "2018-04-01"; // Set tanggal 1 April 2018

//TANGGAL 01-04-2018 (Format Full)
// date("d", strtotime($tanggal)) . " " . month(date("n", strtotime($tanggal))) . " " . date("Y", strtotime($tanggal));

// TANGGAL 01-04-2018 (Format Singkatan)
//date("d", strtotime($tanggal)) . " " . month(date("n", strtotime($tanggal)), 'mmm') . " " . date("Y", strtotime($tanggal));


// limit jumlah karakter return
function wordLimit($text, $jmlKarakter)
{
    if (strlen($text) > $jmlKarakter)
        $word = mb_substr($text, 0, $jmlKarakter - 3) . '...';
    else
        $word = $text;
    return $word;
}

// cari kategori dari video yg diputar
function getKtgFromVideo($id_video)
{
    global $conn;
    $query = $conn->query("SELECT kategori FROM videos WHERE id_video = '$id_video'")->fetch_assoc();
    return $query['kategori'];
}


// hitung jml video yg berkategori sama berdasarkn inpiut
function countVidInKtg($kategori)
{
    global $conn;
    $query = $conn->query("SELECT COUNT(id_video) AS total FROM videos WHERE kategori = '$kategori'")->fetch_assoc();
    return $query['total'];
}

// pilih 1 next video jika dalam kategori masih ada video lain then buat json
function nextVidByKategori($id_video, $kategori)
{
    global $conn;
    $query = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video != '$id_video' AND videos.kategori = '$kategori' LIMIT 1")->fetch_assoc();
    // $query = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video != '$id_video' AND videos.kategori = '$kategori' ORDER BY RAND() LIMIT 1")->fetch_assoc();

    $reps = array(
        'status' => 200,
        'data' => array(
            'id_video' => $query['id_video'],
            'id_user' => $query['id_user'],
            'judul' => $query['judul'],
            'target' => base_url('assets/file/') . $query['target'],
            'kategori' => $query['kategori'],
            'keterangan' => $query['keterangan'],
            'tanggal' => date('d', $query['tanggal']) . ' ' . month(date('n', $query['tanggal'])) . ' ' . date('Y', $query['tanggal']),
            'viewer' => $query['viewers'],
            'video_like' => $query['video_like'],
            'video_dislike' => $query['video_dislike']
        )
    );

    // update viewers video
    $id_video = $reps['data']['id_video'];
    $conn->query("UPDATE views SET viewers = viewers + 1 WHERE id_video = '$id_video'");
    return json_encode($reps);
}

function nextVidOtherKategori($id_video)
{
    global $conn;
    $query = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video != '$id_video' LIMIT 1");
    // $query = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video != '$id_video' ORDER BY RAND() LIMIT 1");

    $nextVideo = (mysqli_num_rows($query) > 0) ? mysqli_fetch_assoc($query) : null;

    if ($nextVideo != null) {
        $reps = array(
            'status' => 200,
            'data' => array(
                'id_video' => $nextVideo['id_video'],
                'id_user' => $nextVideo['id_user'],
                'judul' => $nextVideo['judul'],
                'target' => base_url('assets/file/') . $nextVideo['target'],
                'kategori' => $nextVideo['kategori'],
                'keterangan' => $nextVideo['keterangan'],
                'tanggal' => date('d', $nextVideo['tanggal']) . ' ' . month(date('n', $nextVideo['tanggal'])) . ' ' . date('Y', $nextVideo['tanggal']),
                'viewer' => $nextVideo['viewers'],
                'video_like' => $nextVideo['video_like'],
                'video_dislike' => $nextVideo['video_dislike']
            )
        );
        // update viewers video
        $id_video = $reps['data']['id_video'];
        $conn->query("UPDATE views SET viewers = viewers + 1 WHERE id_video = '$id_video'");
    } else {
        $reps = array(
            'status' => 404
        );
    }
    return json_encode($reps);
}

// minta data untuk diplay
function getVidToPlay($id_video)
{
    global $conn;
    $query = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video = '$id_video' LIMIT 1");

    if (mysqli_num_rows($query) == 0) {
        $reps = array(
            'status' => 404
        );
    } else {
        $video = mysqli_fetch_assoc($query);
        $reps = array(
            'status' => 200,
            'data' => array(
                'id_video' => $video['id_video'],
                'id_user' => $video['id_user'],
                'judul' => $video['judul'],
                'target' => base_url('assets/file/') . $video['target'],
                'kategori' => $video['kategori'],
                'keterangan' => $video['keterangan'],
                'tanggal' => date('d', $video['tanggal']) . ' ' . month(date('n', $video['tanggal'])) . ' ' . date('Y', $video['tanggal']),
                'viewer' => $video['viewers'],
                'video_like' => $video['video_like'],
                'video_dislike' => $video['video_dislike']
            )
        );
        // update viewers video
        $conn->query("UPDATE views SET viewers = viewers + 1 WHERE id_video = '$id_video'");
    }
    return json_encode($reps);
}

// minta data kategori 
function getKategori($opsi)
{
    global $conn;
    switch ($opsi) {
        case 'aktif':
            $query = $conn->query("SELECT kategori, COUNT(kategori) AS total FROM videos GROUP BY kategori");
            if ($query) {
                $resp = array(
                    'status' => 200
                );
                while ($d = mysqli_fetch_assoc($query)) {
                    $data[] = array(
                        'judul' => $d['kategori'],
                        'total' => $d['total']
                    );
                }
                $resp['data'] = $data;
                array_push($resp, $resp['data']);
                unset($resp[0]);
            } else {
                $resp = array(
                    'status' => 404
                );
            }
            break;
        case 'kosong':
            $query = $conn->query("SELECT categories.id, categories.judul FROM categories WHERE NOT EXISTS ( SELECT videos.kategori FROM videos WHERE categories.judul = videos.kategori)");
            if ($query) {
                $resp = array(
                    'status' => 200
                );
                while ($d = mysqli_fetch_assoc($query)) {
                    $data[] = array(
                        'id' => $d['id'],
                        'judul' => $d['judul']
                    );
                }
                $resp['data'] = $data;
                array_push($resp, $resp['data']);
                unset($resp[0]);
            } else {
                $resp = array(
                    'status' => 404
                );
            }
            break;
    }
    return json_encode($resp);
}

// data video teratas / populer
function getVideoPopuler($jumlah)
{
    global $conn;
    $query = $conn->query("SELECT videos.id_video , videos.judul, videos.kategori FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video GROUP BY videos.id_video ORDER BY views.viewers ASC, video_like.video_like ASC LIMIT $jumlah");
    if ($query) {
        $resp = array(
            'status' => 200
        );
        while ($d = mysqli_fetch_assoc($query)) {
            $data[] = array(
                'id_video' => $d['id_video'],
                'judul' => wordLimit($d['judul'], 50),
                'kategori' => $d['kategori']
            );
        }
        $resp['data'] = $data;
        array_push($resp, $resp['data']);
        unset($resp[0]);
    } else {
        $resp = array(
            'status' => 404
        );
    }
    return json_encode($resp);
}

// data video terbaru
function getVideoNew($jumlah)
{
    global $conn;
    $query = $conn->query("SELECT videos.id_video , videos.judul, videos.kategori FROM videos GROUP BY videos.id_video ORDER BY videos.tanggal DESC LIMIT $jumlah");
    if ($query) {
        $resp = array(
            'status' => 200
        );
        while ($d = mysqli_fetch_assoc($query)) {
            $data[] = array(
                'id_video' => $d['id_video'],
                'judul' => wordLimit($d['judul'], 50),
                'kategori' => $d['kategori']
            );
        }
        $resp['data'] = $data;
        array_push($resp, $resp['data']);
        unset($resp[0]);
    } else {
        $resp = array(
            'status' => 404
        );
    }
    return json_encode($resp);
}

// search
function resultSearch($cari, $pageNow, $limitStart, $limit)
{
    global $conn;
    // untuk pagination
    $SqlQuery = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%' OR videos.id_user LIKE '%$cari%' OR videos.id_video LIKE '%$cari%') AND videos.id_video = views.id_video");
    //Hitung semua jumlah data yang berada pada tabel
    $jumlahData = mysqli_num_rows($SqlQuery);
    // Hitung jumlah halaman yang tersedia
    $jumlahPage = ceil($jumlahData / $limit);
    // Jumlah link number 
    $jumlahNumber = 1;
    // backPage
    $linkPrev = ($pageNow > 1) ? $pageNow - 1 : 1;
    // Untuk awal link number
    $startNumber = ($pageNow > $jumlahNumber) ? $pageNow - $jumlahNumber : 1;
    // Untuk akhir link number
    $endNumber = ($pageNow < ($jumlahPage - $jumlahNumber)) ? $pageNow + $jumlahNumber : $jumlahPage;
    // hasil datanya
    $query = $conn->query("SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%' OR videos.id_user LIKE '%$cari%' OR videos.id_video LIKE '%$cari%') AND videos.id_video = views.id_video ORDER BY views.viewers DESC LIMIT $limitStart, $limit");

    // jika ada hasinyla
    if ($query) {
        $resp = array(
            'status' => 200,
            'jumlahAllData' => $jumlahData,
            'dataInPage' => mysqli_num_rows($query),
            'limitData' => $limit,
            'jumlahPage' => $jumlahPage,
            'pageNow' => $pageNow,
            'backPage' => $linkPrev,
            'statNumber' => $startNumber,
            'endNumber' => $endNumber
        );
        while ($d = mysqli_fetch_assoc($query)) {
            $data[] = array(
                'id_video' => $d['id_video'],
                'judul' => wordLimit($d['judul'], 50),
                'kategori' => $d['kategori']
            );
        }
        if ($data == null) {
            $resp = array(
                'status' => 404
            );
        } else {
            $resp['data'] = $data;
            array_push($resp, $resp['data']);
            unset($resp[0]);
        }
    } else {
        $resp = array(
            'status' => 404
        );
    }
    return json_encode($resp);
}

// hapus tiga jam yg lalu dan tambahkan yg sekarang diputar
function createNewTmpView($id_viewer, $id_video)
{
    global $conn;
    // hapus row tmp_view yg waktunya tiga jam yg lalu ke atas
    $lastTime = time() - (60 * 180); // tiga jam
    // ekse
    $conn->query("DELETE FROM tmp_view WHERE time <= '$lastTime'");
    //  masukkan $id_viewer ke db
    $time = time();
    // ekse
    $conn->query("INSERT INTO tmp_view(id_viewer, id_video, time) VALUES ('$id_viewer', '$id_video', '$time')");
}

// nextVid untuk diputar
function nextVideo($id_viewer, $id_video, $kategori)
{
    global $conn;
    $cariData = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video NOT IN (SELECT tmp_view.id_video FROM tmp_view WHERE tmp_view.id_viewer = '$id_viewer') AND videos.kategori = '$kategori' AND videos.id_video != '$id_video' ORDER BY RAND() LIMIT 1");
    // $cariData = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.id_video NOT IN (SELECT tmp_view.id_video FROM tmp_view WHERE tmp_view.id_viewer = '$id_viewer') AND videos.kategori = '$kategori' ORDER BY RAND() LIMIT 1");

    if (mysqli_num_rows($cariData) == 0) {
        // other kategori
        $cariData = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.kategori != '$kategori' AND videos.id_video != '$id_video' ORDER BY RAND() LIMIT 1");
        // $cariData = $conn->query("SELECT videos.*, views.viewers, video_like.video_like, video_dislike.video_dislike FROM videos JOIN views ON views.id_video = videos.id_video JOIN video_like ON video_like.id_video = videos.id_video JOIN video_dislike ON video_dislike.id_video = videos.id_video WHERE videos.kategori != '$kategori' ORDER BY RAND() LIMIT 1");
        // jika row == 0
        if (mysqli_num_rows($cariData) == 0) {
            $resp = array(
                'status' => 404
            );
        } else {
            $query = mysqli_fetch_assoc($cariData);
            $resp = array(
                'status' => 200,
                'data' => array(
                    'id_video' => $query['id_video'],
                    'id_user' => $query['id_user'],
                    'judul' => $query['judul'],
                    'target' => base_url('assets/file/') . $query['target'],
                    'kategori' => $query['kategori'],
                    'keterangan' => $query['keterangan'],
                    'tanggal' => date('d', $query['tanggal']) . ' ' . month(date('n', $query['tanggal'])) . ' ' . date('Y', $query['tanggal']),
                    'viewer' => $query['viewers'],
                    'video_like' => $query['video_like'],
                    'video_dislike' => $query['video_dislike']
                )
            );
        }
    } else {
        $query = mysqli_fetch_assoc($cariData);
        $resp = array(
            'status' => 200,
            'data' => array(
                'id_video' => $query['id_video'],
                'id_user' => $query['id_user'],
                'judul' => $query['judul'],
                'target' => base_url('assets/file/') . $query['target'],
                'kategori' => $query['kategori'],
                'keterangan' => $query['keterangan'],
                'tanggal' => date('d', $query['tanggal']) . ' ' . month(date('n', $query['tanggal'])) . ' ' . date('Y', $query['tanggal']),
                'viewer' => $query['viewers'],
                'video_like' => $query['video_like'],
                'video_dislike' => $query['video_dislike']
            )
        );
    }
    // update viewers video
    $id_video = $resp['data']['id_video'];
    $conn->query("UPDATE views SET viewers = viewers + 1 WHERE id_video = '$id_video'");
    return json_encode($resp);
    // return json_encode($cariData);
}
