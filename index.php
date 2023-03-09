<?php
session_start();

require 'function.php';

$session = $_SESSION['username'];

// mematikan semua error reporting
error_reporting(0);

// tampilkan pesan jika url tidak ada saat watch
if (isset($_GET['page'])) {
    if ($_GET['page'] == 'watch' && !$_GET['v']) {
        // jika didapati user jail menghapus url saat watch
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
        $dataTitle = 'My Video';
    } elseif ($_GET['page'] == 'watch' && $_GET['v']) {
        $cari = $_GET['v'];
        // cari videonya
        $cariTitle = mysqli_query($conn, "SELECT videos.judul FROM videos WHERE videos.id_video = '$cari'")->fetch_assoc();
        if (count($cariTitle) < 1) {
            // url tidak ditemukan
            $_SESSION['pesan'] = 'URL video tidak ditemukan.';
            $dataTitle = 'My Video';
        } else {
            // ganti title menjadi judul video
            $dataTitle = $cariTitle['judul'];
        }
    }
} else {
    // page = null
    $dataTitle = 'My Video';
}

// tangani video-like
if (isset($_GET['video-like'])) {
    // tangani jika kosongan
    if (!$_GET['video-like'] || !$_GET['v']) {
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['video-like'] == 'anonymous' || !$_GET['v']) {
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $video_likeURL = $_GET['v'];
        $idUserLike = $_GET['video-like'];
        $cariVideoLike = mysqli_query($conn, "SELECT * FROM video_like WHERE id_video = '$video_likeURL' AND id_user = '$idUserLike'")->fetch_assoc();
        if (!$cariVideoLike) {
            // user like video
            $like_true = mysqli_query($conn, "INSERT INTO video_like (id_video, id_user, video_like) VALUES ('$video_likeURL', '$idUserLike', '1')");
            if ($like_true) {
                $_SESSION['pesan'] = 'Berhasil like video.';
                echo "<script>window.location.href = 'index.php?page=watch&v=$video_likeURL';</script>";
                // header("Location: index.php?page=watch&v=$video_likeURL");
                exit;
            } else {
                $_SESSION['pesan'] = 'Gagal like video. Coba lagi nanti';
            }
        } else {
            // user unlike
            mysqli_query($conn, "DELETE FROM video_like WHERE id_user = '$idUserLike' AND id_video = '$video_likeURL'");
            $_SESSION['pesan'] = 'Anda telah unlike video.';
            echo "<script>window.location.href = 'index.php?page=watch&v=$video_likeURL';</script>";
            // header("Location: index.php?page=watch&v=$video_likeURL");
            exit;
        }
    }
}

// tangani video-dislike
if (isset($_GET['video-dislike'])) {
    // tangani jika kosongan
    if (!$_GET['video-dislike'] || !$_GET['v']) {
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['video-dislike'] == 'anonymous' || !$_GET['v']) {
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $video_dislikeURL = $_GET['v'];
        $idUserDislike = $_GET['video-dislike'];
        $cariVideoDislike = mysqli_query($conn, "SELECT * FROM video_dislike WHERE id_video = '$video_dislikeURL' AND id_user = '$idUserDislike'")->fetch_assoc();
        if (!$cariVideoDislike) {
            // user dislike video
            $dislike_true = mysqli_query($conn, "INSERT INTO video_dislike (id_video, id_user, video_dislike) VALUES ('$video_dislikeURL', '$idUserDislike', '1')");
            if ($dislike_true) {
                $_SESSION['pesan'] = 'Berhasil dislike video.';
                echo "<script>window.location.href = 'index.php?page=watch&v=$video_dislikeURL';</script>";
                // header("Location: index.php?page=watch&v=$video_dislikeURL");
                exit;
            } else {
                $_SESSION['pesan'] = 'Gagal dislike video.';
            }
        } else {
            // user undislike
            mysqli_query($conn, "DELETE FROM video_dislike WHERE id_user = '$idUserDislike' AND id_video = '$video_dislikeURL'");
            $_SESSION['pesan'] = 'Anda telah undislike video.';
            echo "<script>window.location.href = 'index.php?page=watch&v=$video_dislikeURL';</script>";
            // header("Location: index.php?page=watch&v=$video_dislikeURL");
            exit;
        }
    }
}

// tangani jika tbl_komen kosong, karena untuk mengambil ID terakhir
if (!empty($_GET['page']) && !empty($_GET['v'])) {
    $load_all_komen = mysqli_query($conn, "SELECT * FROM komentar");
    if (mysqli_num_rows($load_all_komen) === 0) {
        $insertKomenGhost = $conn->query("INSERT INTO komentar (id_user, id_video, komentar, tanggal) VALUES ('ghost', '', '', '')");
        $cariIdTerakhir = mysqli_num_rows($load_all_komen) - 1;
        $loadIdTerakhir = mysqli_query($conn, "SELECT * FROM komentar LIMIT $cariIdTerakhir, 1")->fetch_assoc();
        $idTerakhir = $loadIdTerakhir['id'];
        mysqli_query($conn, "DELETE FROM komentar id_user = 'ghost'");
    } elseif (mysqli_num_rows($load_all_komen) > 0) {
        $cariIdTerakhir = mysqli_num_rows($load_all_komen) - 1;
        $loadIdTerakhir = mysqli_query($conn, "SELECT * FROM komentar LIMIT $cariIdTerakhir, 1")->fetch_assoc();
        $idTerakhir = $loadIdTerakhir['id'];
    }
}

// tangani post komentar
if (isset($_POST['komen'])) {
    $id_user = $_POST['id_user'];
    $id_videoKomen = $_POST['id_video'];
    $komentar = nl2br(htmlspecialchars($_POST['isi_komen'], ENT_QUOTES), FALSE);
    $tanggal = $_POST['tanggal'];
    $post_komen = mysqli_query($conn, "INSERT INTO komentar (id_user, id_video, komentar, tanggal) VALUES ('$id_user', '$id_videoKomen', '$komentar', '$tanggal')");
    if ($post_komen) {
        $qLastId = mysqli_query($conn, "SELECT id FROM komentar ORDER BY id DESC LIMIT 1")->fetch_assoc();
        echo $resp = $qLastId['id'];
        $_SESSION['pesan'] = 'Komentar berhasil disimpan.';
        exit;
    } else {
        echo $resp = 'err';
        $_SESSION['pesan'] = 'Komentar gagal disimpan.';
        exit;
    }
}

// tangani hapus komentar
if (isset($_GET['delete-komen'])) {
    // tangani jika kosongan
    $resp = 'err';
    if (!$_GET['delete-komen'] || !$_GET['v']) {
        $resp = 'err';
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['delete-komen'] == 'anonymous' || !$_GET['v']) {
        $resp = 'err';
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $id_userKomen = $_GET['delete-komen'];
        $id_videoDeleteKomen = $_GET['v'];
        $hps_komen = mysqli_query($conn, "DELETE FROM komentar WHERE id = '$id_userKomen' AND id_video = '$id_videoDeleteKomen'");
        if ($hps_komen) {
            $resp = 'ok';
            $_SESSION['pesan'] = 'Berhasil menghapus komentar';
        } else {
            $resp = 'err';
            $_SESSION['pesan'] = 'Gagal menghapus komentar';
        }
    }
    echo $resp;
    exit;
}

// tangkap update viewers
if (!empty($_GET['page']) && !empty($_GET['v']) && !empty($_GET['update-viewers'])) {
    if ($_GET['page'] == 'watch') {
        $cari = $_GET['v'];
        $viewLama = mysqli_query($conn, "SELECT viewers FROM views WHERE id_video = '$cari'")->fetch_assoc();
        $newTotalViewers = $viewLama['viewers'] + 1;
        mysqli_query($conn, "UPDATE views SET viewers = '$newTotalViewers' WHERE id_video = '$cari'");
        exit;
    }
}

// load video lainnya di watch
if (isset($_POST['loadMore'])) {
    $output = '';
    $no_video = '';
    $lastNo = $_POST['lastNo'];
    $kategori = $_POST['kategori'];
    $webMode = $_POST['webMode'];
    $playNow = $_POST['playNow'];

    if ($kategori != 'All') {
        $loadMore = $conn->query("SELECT videos.no, videos.id_video, videos.id_user, videos.judul, videos.tanggal, views.viewers FROM videos, views WHERE videos.id_video != '$playNow' AND videos.id_video = views.id_video AND videos.no >" . $lastNo . " GROUP BY videos.id_video LIMIT 5");
        //  ORDER BY videos.no ASC
    } else {
        $loadMore = $conn->query("SELECT videos.no, videos.id_video, videos.id_user, videos.judul, videos.tanggal, views.viewers FROM videos, views WHERE videos.id_video != '$playNow' AND videos.id_video = views.id_video AND videos.kategori = '" . $kategori . "' AND videos.no >" . $lastNo . " GROUP BY videos.id_video LIMIT 5");
        //  ORDER BY videos.no ASC
    }

    if ($loadMore) {
        // buat output unutk darkMode
        if ($webMode == 'dark') {
            while ($lm = mysqli_fetch_assoc($loadMore)) {
                $no_video = $lm["no"];
                $output .= '<a href="' . base_url('index.php?page=watch&v=') . $lm["id_video"] . '"><div class="card mb-3 bg-dark text-white border-white">
                <div class="card-header">' . $lm["judul"] . '</div>
                <div class="card-body text-center">
                    <span class="badge badge-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                </svg>&nbsp;
                    ' . $lm["id_user"] . '
                    </span>&nbsp;
                    <span class="badge badge-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"></path>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"></path>
                        </svg>&nbsp;
                        ' . $lm["viewers"] . '&nbsp;
                    </span>
                </div>
            </div></a>';
            }
        } elseif ($webMode == 'light') {
            while ($lm = mysqli_fetch_assoc($loadMore)) {
                $no_video = $lm["no"];
                $output .= '<a href="' . base_url('index.php?page=watch&v=') . $lm["id_video"] . '"><div class="card mb-3">
                <div class="card-header">' . $lm["judul"] . '</div>
                <div class="card-body text-center"">
                    <span class="badge badge-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                        <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                        <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    </svg>&nbsp;' . $lm["id_user"] . '
                    </span>&nbsp;
                    <span class="badge badge-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                        </svg>&nbsp;
                        ' . $lm["viewers"] . '&nbsp;
                    </span>
                </div>
            </div></a>';
            }
        }
    }

    if (mysqli_num_rows($loadMore) > 5) {
        $output .= '<button class="btn btn-success w-100" data-lastNo="' . $no_video . '" data-kategori="' . $kategori . '" data-play="' . $playNow . '" id="loadMore">Load More</button>';
    }

    echo $output;
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $dataTitle; ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="My Video, Aplikasi berbagi video untuk dinikmati bersama anggota keluarga.">
    <meta name="keywords" content="<?php if (!empty($_GET['v'])) {
                                        echo 'Tonton ' . $dataTitle . ' hanya di My Video';
                                    } else {
                                        echo 'Tonton dan bagiakan video menarik Anda disini.';
                                    } ?>">
    <meta name="author" content="Yusuf Bahtiar @menpc3o">
    <link rel="icon" href="<?= base_url('assets/img/ico.png'); ?>" type="image/x-icon">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/video-js.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/videojs-upnext.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/videojs-mobile-ui.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/slick.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/slick-theme.css'); ?>" rel="stylesheet">
    <style>
        a.navbar-brand {
            margin-top: 10px;
            font-size: 1rem;
        }

        .navSearch {
            margin-top: 15px;
        }

        .card a,
        #next a {
            text-decoration: none;
        }

        .card:hover,
        #next a :hover {
            border: none;
        }

        /* .main_page {
            margin-top: 120px;
        } */

        #fbd {
            background-color: #343a40;
            color: #fff;
        }

        #card_komen {
            max-height: 350px;
            overflow: auto;
        }

        .touchToUnmute {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: transparent;
        }

        #btnUnmute {
            z-index: 999;
        }

        /* tablet */
        @media screen and (max-width: 991.98px) {
            /* .main_page {
                margin-top: 90px;
            } */

            .carousel-item img {
                height: 350px;
            }
        }

        /* mobile */
        @media screen and (max-width: 576px) {
            #q {
                margin-bottom: 5px;
            }

            .carousel-item img {
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-secondary">
        <a class="navbar-brand" href="<?php if (!isset($_SESSION["myvideo"])) {
                                            echo base_url('');
                                        } else {
                                            echo base_url('user.php?page=dashboard');
                                        }
                                        ?>">
            <img src="<?= base_url('assets/img/ico.png'); ?>" alt="" width="32" height="32" class="d-inline-block align-top"> <?php if (!isset($_SESSION["myvideo"])) {
                                                                                                                                    echo 'My Video';
                                                                                                                                } else {
                                                                                                                                    $getNameUser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$session'")->fetch_assoc();
                                                                                                                                    $nameUsername = $getNameUser['fullname'];
                                                                                                                                    echo $nameUsername;
                                                                                                                                } ?>
        </a>
        <a onclick="setDarkMode()" id="themeBtn" class="text-white my-auto" style="cursor: pointer;" data-toggle="tooltip" title="Klik untuk beralih tema">[Dark]</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ml-auto nav-item navSearch">
                <form action="index.php" class="row form-group" id="searchForm">
                    <div class="col-sm-8 col-md-8 col-xl-8">
                        <input type="text" class="form-control" id="q" name="q" style="width: 100%;" autocomplete="off" placeholder="Cari Video">
                    </div>
                    <div class="col-sm-4 col-md-4 col-xl-4">
                        <button type="submit" class="btn btn-light w-100">&#128269;<span class="sr-only">Search</span></button>
                    </div>
                </form>
            </div>
            <div class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION["myvideo"])) : ?>
                    <a class="nav-item btn btn-outline-warning" href="<?= base_url('login.php'); ?>">Login</a>
                <?php else : ?>
                    <a onclick="return confirm('Yakin mau keluar...?')" class="nav-item btn btn-outline-danger" href="user.php?page=logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- End Nav -->

    <div class="container mb-5 main_page">

        <?php
        if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
            echo '<div id="pesan" class="alert alert-warning" style="display:none;">' . $_SESSION['pesan'] . '</div>';
        }
        $_SESSION['pesan'] = '';
        ?>

        <section class="mt-3">
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];

                switch ($page) {
                    case 'watch':
                        include "watch.php";
                        break;

                    default:
                        echo '<div class="card"><span class="text-center p-5"><h4>404 Not Found</h4></span></div>';
                        break;
                }
            } else {
                include "home.php";
            }
            ?>
        </section>

    </div> <!-- end Container -->

    <script src="<?= base_url('assets/js/jquery-3.2.1.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/popper.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?= base_url('assets/ckeditor/ckeditor.js'); ?>"></script>
    <script src="<?= base_url('assets/js/video.js'); ?>"></script>
    <script src="<?= base_url('assets/js/videojs.hotkeys.js'); ?>"></script>
    <script src="<?= base_url('assets/js/videojs-mobile-ui.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/videojs-upnext.js'); ?>"></script>
    <script src="<?= base_url('assets/js/slick.min.js'); ?>"></script>
    <script>
        //check localstorage
        if (localStorage.getItem('preferredTheme') == 'dark') {
            setDarkMode();
            var webMode = 'dark';
        } else {
            var webMode = 'light';
        }

        function setDarkMode() {
            const themeBtn = document.getElementById('themeBtn')
            let toggle = ""
            let isDark = document.body.classList.toggle("bg-dark")
            if (isDark) {
                toggle = "[Light]"
                //tambahan localstorage
                localStorage.setItem('preferredTheme', 'dark');
                // ekse dark
                $('body').addClass('text-white');
                $('.navbar').removeClass('bg-secondary').addClass('bg-info');
                $('.card').addClass('bg-dark text-white border-white');
                $('.modal-content').addClass('bg-dark');
                $('hr').addClass('bg-light');
                $('a.card').addClass('text-white');
                $('.text-dark').removeClass('text-dark').addClass('text-white');
                $('.akun-nav').removeClass('border-dark').addClass('border-white');
                $('.dashboard-nav').removeClass('border-dark').addClass('border-white');
                $('.carousel-control-prev').removeClass('bg-dark');
                $('.carousel-control-next').removeClass('bg-dark');
            } else {
                toggle = "[Dark]"
                //tambahan localstorage
                localStorage.removeItem('preferredTheme');
                // ekse light
                $('body').removeClass('text-white');
                $('.navbar').removeClass('bg-info').addClass('bg-secondary');
                $('.card').removeClass('bg-dark text-white border-white');
                $('.modal-content').removeClass('bg-dark');
                $('hr').removeClass('bg-light');
                $('a').removeClass('text-white');
                $('.text-white').removeClass('text-white').addClass('text-dark');
                $('#themeBtn').removeClass('text-dark').addClass('text-white');
                $('.akun-nav').removeClass('border-white').addClass('border-dark');
                $('.dashboard-nav').removeClass('border-white').addClass('border-dark');
                $('.carousel-control-prev').addClass('bg-dark');
                $('.carousel-control-next').addClass('bg-dark');
            }
            themeBtn.innerHTML = toggle
        }

        // tocopylink
        function copyFunc() {
            var copyText = document.getElementById("url_video");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
        }

        <?php if (!empty($_GET['page']) && !empty($_GET['v'])) : ?>

            // remove fixed-top
            $('.navbar').removeClass('fixed-top');
            $('.main_page').css('margin-top', '40px');

            // videoplyaerjs
            var player = videojs('videoPlaying', {
                // autoplay: 'any',
                preload: 'auto',
                // controls: true,
                muted: true,
                fluid: true,
                playbackRates: [0.25, 0.5, 1, 1.5, 1.75],
                plugins: {
                    hotkeys: {
                        volumeStep: 0.1,
                        seekStep: 5,
                        enableMute: true,
                        enableFullscreen: true,
                        enableNumbers: false,
                        enableVolumeScroll: true,
                        enableHoverScroll: true
                    }
                }
            });

            // buat tampilan tap to unmute
            $('#videoPlaying').append(`<div class="touchToUnmute"><span id="btnUnmute" class="btn btn-light ml-2 mt-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-volume-mute" viewBox="0 0 16 16"><path d="M6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06zM6 5.04 4.312 6.39A.5.5 0 0 1 4 6.5H2v3h2a.5.5 0 0 1 .312.11L6 10.96V5.04zm7.854.606a.5.5 0 0 1 0 .708L12.207 8l1.647 1.646a.5.5 0 0 1-.708.708L11.5 8.707l-1.646 1.647a.5.5 0 0 1-.708-.708L10.793 8 9.146 6.354a.5.5 0 1 1 .708-.708L11.5 7.293l1.646-1.647a.5.5 0 0 1 .708 0z"/></svg>&nbsp;aktifkan suara</span></div>`);

            // fokuskan ke btn unmte
            $('#btnUnmute').attr('tabindex', -1).focus();

            // jika diklik aktifkan controlbar dan hilangkan div tapToUnmute
            $(document).on('click', '.touchToUnmute, #btnUnmute', function(e) {
                e.preventDefault();
                playThisVideo();
            });

            function playThisVideo() {
                player.muted(false);
                player.controls(true);
                player.play();
                // player.requestFullscreen();
                $('.touchToUnmute, #btnUnmute').remove();
            }

            <?php if ($nexTitle != [] && $nexUrl != []) :
                $randNext = array_rand($nexTitle);
            ?>
                // func upnext
                function getTitle() {
                    return "<?= $nexTitle[$randNext]; ?>";
                }

                // func upnext
                function next() {
                    window.location.href = "<?= $nexUrl[$randNext]; ?>";
                }

                // muat video lain setelah selesai
                player.upnext({
                    timeout: 5000,
                    getTitle: getTitle,
                    next: next
                });
            <?php endif; ?>

            // double tap to seek
            player.mobileUi({
                fullscreen: {
                    enterOnRotate: true,
                    exitOnRotate: true,
                    lockOnRotate: true
                },
                touchControls: {
                    seekSeconds: 10,
                    tapTimeout: 300,
                    disableOnEnd: false
                }
            });

            // to update viewers video
            var klikPlay = 0;

            // to notify komen
            var komenTotal <?= '= ' . mysqli_num_rows($komen) . ';'; ?>

            // to update komen
            var last_id <?= '=' . $idTerakhir . ';'; ?>

            player.on('play', function(event) {
                event.preventDefault();
                klikPlay++;
                if (klikPlay == 1) {
                    $.ajax({
                        url: "<?= base_url('index.php?page=watch&v=') . $cari . '&update-viewers=' . $session; ?>",
                        type: "get"
                    });
                }
            });

            $('#loadMore').click(function(e) {
                e.preventDefault();
                var lastNo = $(this).data('lastno');
                var kategori = $(this).data('kategori');
                var playNow = $(this).data('play');
                $(this).html('Loading...');
                $.ajax({
                    url: 'index.php',
                    type: 'post',
                    data: {
                        loadMore: '',
                        lastNo: lastNo,
                        kategori: kategori,
                        playNow: playNow,
                        webMode: webMode
                    },
                    success: function(resp) {
                        if (resp != '') {
                            $('#loadMore').remove();
                            $('#next').append(resp);
                        } else {
                            $('#loadMore').remove();
                            $('#next').append(`<p class="text-center">Halaman Terakhir.</p>`);
                        }
                    }
                })
            });

            $('#video-like').on('click', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "<?= base_url('index.php?page=watch&v=') . $q['id_video'] . '&video-like=' . $session; ?>",
                    type: "get"
                });
                var user_liked = "<?= $userLiked; ?>";
                var session = "<?= $session; ?>";
                if (session == "anonymous") {
                    $('#video-like').html(`Anda Belum Login`);
                } else if (user_liked == "Suka") {
                    if ($('#video-like').attr("id") == "video-like") {
                        $('#video-like').attr('id', 'unlike').html(`<span class="badge badge-light"><?= $totalLike + 1; ?></span> Batal Suka`);
                    } else if ($('#unlike').attr("id") == "unlike") {
                        $('#unlike').attr('id', 'video-like').html(`<span class="badge badge-light"><?= $totalLike + 1 - 1; ?></span> Suka`);
                    }
                } else if (user_liked == "Batal Suka") {
                    if ($('#video-like').attr("id") == "video-like") {
                        $('#video-like').attr('id', 'unlike').html(`<span class="badge badge-light"><?= $totalLike - 1; ?></span> Suka`);
                    } else if ($('#unlike').attr("id") == "unlike") {
                        $('#unlike').attr('id', 'video-like').html(`<span class="badge badge-light"><?= $totalLike - 1 + 1; ?></span> Batal Suka`);
                    }
                }
            })

            $('#video-dislike').on('click', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "<?= base_url('index.php?page=watch&v=') . $q['id_video'] . '&video-dislike=' . $session; ?>",
                    type: "get"
                });
                var user_disliked = "<?= $userDisliked ?>";
                var session = "<?= $session; ?>";
                if (session == "anonymous") {
                    $('#video-dislike').html(`Anda Belum Login`);
                } else if (user_disliked == "Tidak Suka") {
                    if ($('#video-dislike').attr("id") == "video-dislike") {
                        $('#video-dislike').attr('id', 'undislike').html(`<span class="badge badge-light"><?= $totalDislike + 1; ?></span> Batal Tidak Suka`);
                    } else if ($('#undislike').attr("id") == "undislike") {
                        $('#undislike').attr('id', 'video-dislike').html(`<span class="badge badge-light"><?= $totalDislike + 1 - 1; ?></span> Tidak Suka`);
                    }
                } else if (user_disliked == "Batal Tidak Suka") {
                    if ($('#video-dislike').attr("id") == "video-dislike") {
                        $('#video-dislike').attr('id', 'undislike').html(`<span class="badge badge-light"><?= $totalDislike - 1; ?></span> Tidak Suka`);
                    } else if ($('#undislike').attr("id") == "undislike") {
                        $('#undislike').attr('id', 'video-dislike').html(`<span class="badge badge-light"><?= $totalDislike - 1 + 1; ?></span> Batal Tidak Suka`);
                    }
                }
            });

            $('form#post_komentar').submit(function(e) {
                e.preventDefault();
                const usrKomen = $('#id_user_komen').val();
                const idVideo = $('#id_video_komen').val();
                const isiKomen = $('#isi_komen').val();
                var komenToShow = isiKomen.replace(/(?:\r\n|\r|\n)/g, '<br>');
                const tglKomen = $('#tgl_komen').val();
                var d = new Date();
                var months = [" Jan ", "  Feb ", "  Mar ", "  Apr ", "  Mei ", "  Jun ", "  Jul ", "  Agu ", "  Sep ", "  Okt ", "  Nov ", "  Des "];
                var tglShow = d.getDate() + months[d.getMonth()] + d.getFullYear();
                last_id++;
                $.ajax({
                    url: "index.php",
                    data: {
                        komen: "",
                        id_user: usrKomen,
                        id_video: idVideo,
                        isi_komen: isiKomen,
                        tanggal: tglKomen
                    },
                    type: "post",
                    success: function(response) {
                        if (response === 'err' || response === 'gagal konek :(') {
                            $('#komentar').modal('hide');
                            $('#card_komen').fadeIn('slow').append(`<div id="komen_id_` + last_id + `" data-komen="` + isiKomen + `"><div class="d-flex mb-2"><span class="font-weight-bold d-block">` + usrKomen + `</span></div>Terjadi kesalahan, Coba lagi nanti.</div>`);
                            $('form#post_komentar')[0].reset();
                            setTimeout(function() {
                                $('#komen_id_' + last_id).slideUp('slow');
                            }, 500);
                        } else {
                            komenTotal = komenTotal + 1;
                            if (komenTotal <= 1) {
                                $('#komentar').modal('hide');
                                $('#komen_header').html('(' + komenTotal + ') Komentar');
                                $('#blm_ada_komen').slideUp('slow', function() {
                                    $(this).remove();
                                });
                                $('#card_komen').fadeIn('slow').append(`<div id="komen_id_` + response + `"><div class="d-flex mb-2"><span class="font-weight-bold d-block">` + usrKomen + `</span><small class="ml-2">` + tglShow + `</small></div>` + komenToShow + `<div class="d-flex justify-content-end"><span onclick="return confirm('Yakin mau hapus ini...?')" data-id="` + response + `" data-url="` + idVideo + `" data-user="` + usrKomen + `" id="delete_komen" class="badge badge-danger" style="cursor: pointer;">Hapus</span></div><hr class="my-2"></div>`);
                                $('#card_komen').animate({
                                    scrollTop: $('div#komen_id_' + response).offset().top
                                }, 400);
                                $('form#post_komentar')[0].reset();
                            } else if (komenTotal > 1) {
                                $('#komentar').modal('hide');
                                $('#komen_header').html('(' + komenTotal + ') Komentar');
                                $('#card_komen').fadeIn('slow').append(`<div id="komen_id_` + response + `"><div class="d-flex mb-2"><span class="font-weight-bold d-block">` + usrKomen + `</span><small class="ml-2">` + tglShow + `</small></div>` + komenToShow + `<div class="d-flex justify-content-end"><span onclick="return confirm('Yakin mau hapus ini...?')" data-id="` + response + `" data-url="` + idVideo + `" data-user="` + usrKomen + `" id="delete_komen" class="badge badge-danger" style="cursor: pointer;">Hapus</span></div><hr class="my-2"></div>`);
                                $('#card_komen').animate({
                                    scrollTop: $('div#komen_id_' + response).offset().top
                                }, 400);
                                $('form#post_komentar')[0].reset();
                            }
                        }
                    },
                    error: function() {
                        $('#komentar').modal('hide');
                        $('#card_komen').fadeIn('slow').append(`<div id="komen_id_` + last_id + `" data-komen="` + isiKomen + `"><div class="d-flex mb-2"><span class="font-weight-bold d-block">` + usrKomen + `</span></div>Terjadi kesalahan, Coba lagi nanti.</div>`);
                        $('form#post_komentar')[0].reset();
                        setTimeout(function() {
                            $('#komen_id_' + last_id).slideUp('slow');
                        }, 500);
                    }
                });
            });

            $(document).on('click', '#delete_komen', function(e) {
                e.preventDefault();
                const komenId = $(this).data('id');
                const urlId = $(this).data('url');
                const userId = $(this).data('user');
                $.ajax({
                    url: "?page=watch&v=" + urlId + "&delete-komen=" + komenId + "&user=" + userId,
                    type: "get",
                    success: function(response) {
                        if (response === 'ok') {
                            komenTotal = komenTotal - 1;
                            $('#komen_id_' + komenId).html('Berhasil hapus komentar.');
                            $('#komen_header').html('(' + komenTotal + ') Komentar');
                            $('#komen_id_' + komenId).slideUp('slow');
                            if (komenTotal === 0) {
                                setTimeout(function() {
                                    $('#card_komen').slideDown('slow').append(`<h6 class="text-center p-1" id="blm_ada_komen">Belum ada komentar</h6>`);
                                }, 400);
                            }
                        } else {
                            $('#komen_id_' + komenId).html('Terjadi kesalahan, coba lagi nanti.');
                        }
                    },
                    error: function() {
                        $('#komen_id_' + komenId).html('Terjadi kesalahan, coba lagi nanti.');
                    }
                });
            });

        <?php else : ?>

            // kembaliken margin topnya
            $('.main_page').css('margin-top', '120px');

        <?php endif; ?>

        // if dcmnt ready
        $(document).ready(function() {

            $('#carouselExampleCaptions:nth-child(1)').addClass('active');
            $('.carousel-item:nth-child(1)').addClass('active');

            setTimeout(function() {
                $("#pesan").fadeIn('slow');
            }, 200);
            setTimeout(function() {
                $("#pesan").fadeOut('slow');
            }, 5000);

            $('form#searchForm').submit(function() {
                $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-dark" role="status"><span class="sr-only">Loading...</span></div>`);
            });

            $('.slider').slick({
                centerMode: true,
                centerPadding: '30px',
                slidesToShow: 3,
                arrows: false,
                responsive: [{
                        breakpoint: 768,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '20px',
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '10px',
                            slidesToShow: 1
                        }
                    }
                ]
            });

        });
    </script>
</body>

</html>