<?php
session_start();

require 'function.php';

// reset tampilan error 
error_reporting(0);

// tampilkan pesan jika url tidak ada saat watch
if (isset($_GET['page'])) {
    if ($_GET['page'] == 'watch' && !$_GET['url']) {
        // jika didapati user jail menghapus url saat watch
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
        $dataTitle = 'My Video';
    } elseif ($_GET['page'] == 'watch' && $_GET['url']) {
        $cari = $_GET['url'];
        // cari videonya
        $cariTitle = mysqli_query($conn, "SELECT videos.judul FROM videos WHERE videos.url = '$cari'")->fetch_assoc();
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
    if (!$_GET['video-like'] || !$_GET['url']) {
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['video-like'] == 'anonymous' || !$_GET['url']) {
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $video_likeURL = $_GET['url'];
        $idUserLike = $_GET['video-like'];
        $cariVideoLike = mysqli_query($conn, "SELECT * FROM video_like WHERE url_video = '$video_likeURL' AND id_user = '$idUserLike'")->fetch_assoc();
        if (!$cariVideoLike) {
            // user like video
            mysqli_query($conn, "INSERT INTO video_like VALUES ('', '$video_likeURL', '$idUserLike', '1')");
            $_SESSION['pesan'] = 'Berhasil like video.';
            // di website nya pake js
            //echo "<script>window.location.href = 'login.php';</script>";
            header("Location: index.php?page=watch&url=$video_likeURL");
            exit;
        } else {
            // user unlike
            mysqli_query($conn, "DELETE FROM video_like WHERE id_user = '$idUserLike' AND url_video = '$video_likeURL'");
            $_SESSION['pesan'] = 'Anda telah unlike video.';
            // di website nya pake js
            //echo "<script>window.location.href = 'login.php';</script>";
            header("Location: index.php?page=watch&url=$video_likeURL");
            exit;
        }
    }
}

// tangani video-dislike
if (isset($_GET['video-dislike'])) {
    // tangani jika kosongan
    if (!$_GET['video-dislike'] || !$_GET['url']) {
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['video-dislike'] == 'anonymous' || !$_GET['url']) {
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $video_dislikeURL = $_GET['url'];
        $idUserDislike = $_GET['video-dislike'];
        $cariVideoDislike = mysqli_query($conn, "SELECT * FROM video_dislike WHERE url_video = '$video_dislikeURL' AND id_user = '$idUserDislike'")->fetch_assoc();
        if (!$cariVideoDislike) {
            // user dislike video
            mysqli_query($conn, "INSERT INTO video_dislike VALUES ('', '$video_dislikeURL', '$idUserDislike', '1')");
            $_SESSION['pesan'] = 'Berhasil dislike video.';
            // di website nya pake js
            //echo "<script>window.location.href = 'login.php';</script>";
            header("Location: index.php?page=watch&url=$video_dislikeURL");
            exit;
        } else {
            // user undislike
            mysqli_query($conn, "DELETE FROM video_dislike WHERE id_user = '$idUserDislike' AND url_video = '$video_dislikeURL'");
            $_SESSION['pesan'] = 'Anda telah undislike video.';
            // di website nya pake js
            //echo "<script>window.location.href = 'login.php';</script>";
            header("Location: index.php?page=watch&url=$video_dislikeURL");
            exit;
        }
    }
}

// tangani komentar
if (isset($_POST['komen'])) {
    $id_user = $_POST['id_user'];
    $url_videoKomen = $_POST['url_video'];
    $komentar = htmlspecialchars($_POST['isi_komen']);
    $tanggal = time();

    mysqli_query($conn, "INSERT INTO komentar VALUES ('', '$id_user', '$url_videoKomen', '$komentar', '$tanggal')");
    $_SESSION['pesan'] = 'Komentar berhasil disimpan.';
    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: index.php?page=watch&url=$url_videoKomen&data-komen=$id_user");
    exit;
}

// tangani hapus komentar
if (isset($_GET['delete-komen'])) {
    // tangani jika kosongan
    if (!$_GET['delete-komen'] || !$_GET['url']) {
        $_SESSION['pesan'] = 'URL video tidak ditemukan.';
    } elseif ($_GET['delete-komen'] == 'anonymous' || !$_GET['url']) {
        $_SESSION['pesan'] = 'Anda belum login.';
    } else {
        $id_userKomen = $_GET['delete-komen'];
        $url_videoDeleteKomen = $_GET['url'];

        mysqli_query($conn, "DELETE FROM komentar WHERE id = '$id_userKomen' AND url_video = '$url_videoDeleteKomen'");
        $_SESSION['pesan'] = 'Komentar berhasil dihapus.';
        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: index.php?page=watch&url=$url_videoDeleteKomen&data-komen=$id_userKomen");
        exit;
    }
}

// tangkap update viewers
if (isset($_GET['page'])) {
    if ($_GET['page'] == 'watch' && $_GET['url'] && $_GET['update-viewers']) {
        $cari = $_GET['url'];
        // biar gak diacak sama viewers, harus sesuai dengan session
        $id_viewer = ($_GET['update-viewers'] != 'anonymous') ? $id_viewer = $_SESSION['username'] : $id_viewer = $_GET['update-viewers'];
        // siapkan untuk update viewers jika session ada
        if ($id_viewer != 'anonymous') {
            mysqli_query($conn, "INSERT INTO tmp_viewers VALUES ('', '$id_viewer', '$cari')");
        }
        // atasi jika viewers komen dulu baru liat video
        $cekView = mysqli_query($conn, "SELECT * FROM tmp_viewers WHERE url_video = '$cari' AND id_user = '$id_viewer'");
        // update viewers
        if ($id_viewer == 'anonymous' || mysqli_num_rows($cekView) <= 1) {
            $viewLama = mysqli_query($conn, "SELECT * FROM views WHERE url_video = '$cari'")->fetch_assoc();
            $newTotalViewers = $viewLama['viewers'] + 1;
            mysqli_query($conn, "UPDATE views SET viewers = '$newTotalViewers' WHERE url_video = '$cari'");
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php if (!$dataTitle) {
                echo 'My Video';
            } else {
                echo $dataTitle;
            } ?></title>
    <link rel="icon" href="<?= base_url('assets/img/ico.svg'); ?>" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dataTables.bootstrap4.min.css'); ?>">
    <style>
        a.navbar-brand {
            margin-top: 10px;
            font-size: 1rem;
        }

        .navSearch {
            margin-top: 15px;
        }

        .card a {
            text-decoration: none;
        }

        .container {
            margin-top: 110px;
        }

        /* tablet */
        @media screen and (max-width: 991.98px) {
            .container {
                margin-top: 80px;
            }
        }

        /* mobile */
        @media screen and (max-width: 576px) {
            #q {
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-secondary">
        <a class="navbar-brand" href="<?php if (!isset($_SESSION["login"])) {
                                            echo base_url('');
                                        } else {
                                            echo base_url('user.php');
                                        }
                                        ?>">
            <img src="<?= base_url('assets/img/ico.svg'); ?>" alt="" width="32" height="32" class="d-inline-block align-top"> <?php if (!isset($_SESSION["login"])) {
                                                                                                                                    echo 'My Video';
                                                                                                                                } else {
                                                                                                                                    $session = $_SESSION['username'];
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
                <form action="index.php" class="row form-group">
                    <div class="col-sm-8 col-md-8 col-xl-8">
                        <input type="text" class="form-control" id="q" name="q" style="width: 100%;" placeholder="Cari video">
                    </div>
                    <div class="col-sm-4 col-md-4 col-xl-4">
                        <button type="submit" class="btn btn-light w-100">&#128269;<span class="sr-only">Search</span></button>
                    </div>
                </form>
            </div>
            <div class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION["login"])) : ?>
                    <a class="nav-item btn btn-outline-warning" href="login.php">Login</a>
                <?php else : ?>
                    <a onclick="return confirm('Yakin mau keluar...?')" class="nav-item btn btn-outline-danger" href="user.php?page=logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- End Nav -->

    <div class="container mb-3">

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
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js'); ?>"></script>
    <script>
        //check localstorage
        if (localStorage.getItem('preferredTheme') == 'dark') {
            setDarkMode()
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
            }
            themeBtn.innerHTML = toggle
        }

        $(document).ready(function() {
            $('#example').DataTable();

            setTimeout(function() {
                $("#pesan").fadeIn('slow');
            }, 200);
            setTimeout(function() {
                $("#pesan").fadeOut('slow');
            }, 5000);

            $('form').submit(function() {
                $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-dark" role="status"><span class="sr-only">Loading...</span></div>`);
            })

            $('#videoPlaying').on('play', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "<?= base_url('index.php?page=watch&url=') . $cari . '&data-komen=' . isset($_GET['data-komen']) . '&update-viewers=' . $session; ?>",
                    type: "get"
                });
                $('video#videoPlaying').unbind();
            })

            $('#video-like').on('click', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "<?= base_url('index.php?page=watch&url=') . $q['url'] . '&video-like=' . $session; ?>",
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
                    url: "<?= base_url('index.php?page=watch&url=') . $q['url'] . '&video-dislike=' . $session; ?>",
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
            })
        });
    </script>
</body>

</html>
