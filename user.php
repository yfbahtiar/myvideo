<?php
session_start();
error_reporting(0);
require 'function.php';
if (!isset($_SESSION['myvideo'])) {
  echo "<script>window.location.href = 'login.php';</script>";
  // header('Location:login.php');
  exit;
}
$session = $_SESSION['username'];

// mencari url jika tambah video
$a = rand(4, 9);
$urlVideo = generateVideoLink($a);
// cek ketersediaan url di db
$urlCek = mysqli_query($conn, "SELECT * FROM videos WHERE url = '$urlVideo'");
if (mysqli_num_rows($urlCek) === 1) {
  $urlVideo = generateVideoLink($a);
}

// query kategori untuk form
$dataKategori = mysqli_query($conn, "SELECT judul FROM categories");
// var_dump($dataKategori);

// tangani upload video
if (isset($_POST['upload'])) {
  $id_video = $_POST['id_video'];
  $id_user = $_POST['id_user'];
  $judul = trim(htmlspecialchars($_POST['judul'], true));
  $kategori = $_POST['kategori'];
  $keterangan = $_POST['keterangan'];
  $tanggal = $_POST['tanggal'];
  $umur = $_POST['umur']; //1 untuk dewasa, 0 untuk umum
  // menangani file upload
  $name = $_FILES['file']['name'];
  $type = $_FILES['file']['type'];
  $size = $_FILES['file']['size'];
  // ubah nama file
  $fileExt = substr($name, strrpos($name, '.'));
  $fileExt = str_replace('.', '', $fileExt);
  $namaFile = str_replace($name, $id_user . '_' . $tanggal . '.' . $fileExt, $name);
  // prepare move file to folder assets
  $namaFolder = 'assets/file/';
  $tmpName = $_FILES['file']['tmp_name'];
  $fileBaru = $namaFolder . basename($namaFile);
  // post_max_size=512M
  // upload_max_filesize=512M
  // maks file 512 MB
  if ((($type == "video/mp4") || ($type == "video/3gpp") || ($type = "video/webm")) && ($size < 4096000000)) {
    $statusUpload = move_uploaded_file($tmpName, $fileBaru);
  } else {
    $resp = 'Error, file terlalu besar atau format file tidak didukunng.';
  }
  // insert to db jika memnuhi kriteria
  if ($statusUpload) {
    $video = mysqli_query($conn, "INSERT INTO videos (id_video, id_user, kategori, judul, target, keterangan, tanggal, umur) VALUES ('$id_video', '$id_user', '$kategori', '$judul', '$namaFile', '$keterangan', '$tanggal', '$umur')");
    $view = mysqli_query($conn, "INSERT INTO views (id_video, viewers) VALUES ('$id_video', '0')");
    $like = mysqli_query($conn, "INSERT INTO video_like (id_video, id_user, video_like) VALUES ('$id_video', '', '0')");
    $dislike = mysqli_query($conn, "INSERT INTO video_dislike (id_video, id_user, video_dislike) VALUES ('$id_video', '', '0')");
    // tangani jika user menambhkan kategori baru;
    $ktgExist = array();
    while ($ktg = mysqli_fetch_assoc($dataKategori)) {
      $ktgExist[] = $ktg['judul'];
    }
    if (!in_array($kategori, $ktgExist)) {
      $conn->query("INSERT INTO categories (judul) VALUES ('$kategori')");
    }
    // cek semua parameter bernilai true
    if ($video && $view && $like && $dislike) {
      $resp = 'ok';
    } else {
      $resp = 'Error, tidak dapat menyimpan database.';
    }
  } else {
    $resp = 'Error, tidak dapat memindahkan file.';
  }
  echo $resp;
  exit;
}

// tangani add kategori
if (isset($_POST['add-kategori'])) {
  $jdl_kategori = htmlspecialchars($_POST['jdl_kategori'], true);
  $addKtg = mysqli_query($conn, "INSERT INTO categories (judul) VALUES ('$jdl_kategori')");
  if ($addKtg) {
    $_SESSION['pesan'] = 'Kategori berhasil ditambahkan.';
    echo "<script>window.location.href = 'user.php?page=dashboard';</script>";
    // header("Location: user.php?page=dashboard");
    exit;
  } else {
    $_SESSION['pesan'] = 'Gagal untuk input kategori, coba lagi nanti.';
  }
}

// tangani hapus kategori
if (isset($_GET['delete-kategori'])) {
  if ($_SESSION['role_id'] != 1) {
    $resp = '[403] Access Forbidden, Anda bukan Admin!';
  } else {
    $deleteIdKategori = $_GET['delete-kategori'];
    mysqli_query($conn, "DELETE FROM categories WHERE id = '$deleteIdKategori'");
    $resp = 'ok';
  }
  echo $resp;
  exit;
}

// tangani delete akun
if (isset($_GET['delete-akun'])) {
  if ($_SESSION['role_id'] != 1) {
    $_SESSION['pesan'] = 'Anda bukan Admin!';
    echo "<script>window.location.href = 'user.php?page=dashboard';</script>";
    // header("Location: user.php?page=dashboard");
    exit;
  } else {
    $idDeleteAkun = $_GET['delete-akun'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$idDeleteAkun'");
    $_SESSION['pesan'] = 'User berhasil dihapus';
    echo "<script>window.location.href = 'user.php?page=dashboard';</script>";
    // header("Location: user.php?page=akun");
    exit;
  }
}

// tangani hapus video
if (isset($_GET['delete-video'])) {
  $deleteIdVideo = $_GET['delete-video'];
  $direktori = 'assets/file/';
  $cariFile = mysqli_query($conn, "SELECT target FROM videos WHERE id_video = '$deleteIdVideo'")->fetch_assoc();
  $file = $cariFile['target'];
  // unlink direktori lokal
  $hapusMas = unlink($direktori . $file);
  if ($hapusMas) {
    $hps1 = mysqli_query($conn, "DELETE FROM videos WHERE id_video = '$deleteIdVideo'");
    $hps2 = mysqli_query($conn, "DELETE FROM views WHERE id_video = '$deleteIdVideo'");
    $hps3 = mysqli_query($conn, "DELETE FROM video_like WHERE id_video = '$deleteIdVideo'");
    $hps4 = mysqli_query($conn, "DELETE FROM video_dislike WHERE id_video = '$deleteIdVideo'");
    $hps5 = mysqli_query($conn, "DELETE FROM komentar WHERE id_video = '$deleteIdVideo'");
    if ($hps1 && $hps2 && $hps3 && $hps4 && $hps5) {
      $resp = 'ok';
    } else {
      $resp = 'Error, tidak dapat menghapus database.';
    }
  } else {
    $resp = 'Error, tidak dapat menghapus file.';
  }
  echo $resp;
  exit;
}

// tangani update video
if (isset($_POST['edit'])) {
  $judul = htmlspecialchars($_POST['judul'], true);
  $umur = $_POST['umur'];
  $kategori = $_POST['kategori'];
  $keterangan = $_POST['keterangan'];
  $id_video = $_POST['id_video'];
  // update data
  $updateVideo = $conn->query("UPDATE videos SET judul = '$judul', umur = '$umur', kategori = '$kategori', keterangan = '$keterangan' WHERE videos.id_video = '$id_video'");
  $ktgExist = array();
  while ($ktg = mysqli_fetch_assoc($dataKategori)) {
    $ktgExist[] = $ktg['judul'];
  }
  if (!in_array($kategori, $ktgExist)) {
    $conn->query("INSERT INTO categories (judul) VALUES ('$kategori')");
  }
  if ($updateVideo) {
    $resp = 'ok';
  } else {
    $resp = 'Error, tidak dapat update database.';
  }
  echo $resp;
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <title><?= $session; ?> | My video</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="My Video, Aplikasi berbagi video untuk dinikmati bersama anggota keluarga.">
  <meta name="keywords" content="Tonton dan bagiakan video menarik Anda disini.">
  <meta name="author" content="Yusuf Bahtiar @menpc3o">
  <link rel="icon" href="<?= base_url('assets/img/ico.png'); ?>" type="image/x-icon">
  <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/responsive.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/video-js.css'); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/videojs-mobile-ui.css'); ?>" rel="stylesheet">
  <style>
    a.navbar-brand {
      margin-top: 10px;
      font-size: 1rem;
    }

    .navSearch {
      margin-top: 15px;
    }

    a.dashboard-nav,
    a.akun-nav {
      text-decoration: none;
    }

    a.dashboard-nav:hover,
    a.akun-nav:hover {
      border-color: #ffc107 !important;
    }

    #body_tutor {
      height: 500px;
      max-height: 500px;
      overflow: auto;
    }

    .container {
      margin-top: 110px;
    }

    .chart-container {
      position: relative;
      margin: auto;
      height: 80vh;
      width: 80vw;
    }

    .blog-footer {
      padding: 2rem 0;
      color: #f8f9fa;
      text-align: center;
      border-top: 0.3rem solid #17a2b8;
    }

    /* tablet */
    @media screen and (max-width: 991.98px) {
      .container {
        margin-top: 80px;
      }

      .chart-container {
        display: none;
      }
    }

    /* mobile */
    @media screen and (max-width: 576px) {
      .chart-container {
        display: none;
      }

      #q {
        margin-bottom: 5px;
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-secondary">
    <a class="navbar-brand" href="<?= base_url(''); ?>">
      <img src="<?= base_url('assets/img/ico.png'); ?>" alt="" width="32" height="32" class="d-inline-block align-top">
      <?php
      $getNameUser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$session'")->fetch_assoc();
      echo $getNameUser['fullname'];
      ?>
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
        <a onclick="return confirm('Yakin mau keluar...?')" class="nav-item btn btn-outline-danger" href="?page=logout">Logout</a>
      </div>
    </div>
  </nav> <!-- End Nav -->
  <!-- main -->
  <div class="container mb-3">
    <?php
    if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
      echo '<div id="pesan" class="alert alert-warning" style="display:none;">' . $_SESSION['pesan'] . '</div>';
    }
    $_SESSION['pesan'] = '';
    ?>
    <!-- munculkan edit kategori -->
    <?php if (isset($_GET['edit-kategori'])) : ?>
      <?php
      if ($_SESSION['role_id'] != 1) {
        $_SESSION['pesan'] = 'Anda bukan Admin!';

        // di website nya pake js
        echo "<script>window.location.href = 'user.php?page=dashboard';</script>";
        // header("Location: user.php?page=dashboard");
        exit;
      }
      // tangani edit
      $editKategori = mysqli_query($conn, "SELECT * FROM categories WHERE judul = '" . $_GET['edit-kategori'] . "'")->fetch_assoc();

      // tangani edit kategori
      if (isset($_POST['editKategori'])) {
        $jdl_kategori = trim(htmlspecialchars($_POST['jdl_kategori']));
        $id_kategori = trim(htmlspecialchars($_POST['id_kategori']));

        // update data
        mysqli_query($conn, "UPDATE categories SET judul = '$jdl_kategori' WHERE id = '$id_kategori'");
        $_SESSION['pesan'] = 'Kategori berhasil diupdate';
        // di website nya pake js
        echo "<script>window.location.href = 'user.php?page=dashboard';</script>";
        // header("Location: user.php?page=dashboard");
        exit;
      }
      ?>
      <div class="card mb-3 mx-auto">
        <div class="card-header">
          Edit Kategori
        </div>
        <form action="" method="post">
          <div class="card-body">
            <div class="form-group">
              <label for="jdl_kategori">Judul Kategori</label>
              <input type="text" name="jdl_kategori" id="jdl_kategori" class="form-control" autocomplete="off" required value="<?= $editKategori['judul']; ?>">
              <div class="ml-1" id="inputFeedback"></div>
            </div>
          </div>
          <div class="card-footer">
            <input type="hidden" name="id_kategori" value="<?= $editKategori['id']; ?>">
            <button type="submit" name="editKategori" class="btn btn-primary" disabled>Edit</button>
            <a href="?page=dashboard" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    <?php endif; ?>
    <!-- end munculkan edit kategori -->
    <div class="row text-center">
      <div class="col-sm-6 mb-1">
        <a href="?page=dashboard" class="border-dark w-100 h-100 btn dashboard-nav <?php if (!$_GET['page'] || $_GET['page'] == "dashboard" || $_GET['page'] == "edit") {
                                                                                      echo 'btn-warning';
                                                                                    } ?>">Dashboard</a>
      </div>
      <div class="col-sm-6 mb-1">
        <a href="?page=akun" class="border-dark w-100 h-100 btn akun-nav <?php if ($_GET['page'] == "akun") {
                                                                            echo 'btn-warning';
                                                                          } ?>">Akun</a>
      </div>
    </div> <!-- end row menu atas -->

    <section class="mt-3">
      <?php
      if (isset($_GET['page'])) {
        $page = $_GET['page'];

        switch ($page) {
          case 'dashboard':
            include "dashboard.php";
            break;

          case 'logout':
            include "logout.php";
            break;

          case 'edit':
            include "editVideo.php";
            break;

          case 'akun':
            include "akun.php";
            break;

          default:
            echo '<div class="card"><span class="text-center p-5"><h4>404 Not Found</h4></span></div>';
            break;
        }
      } else {
        include "dashboard.php";
      }

      ?>
    </section>
  </div> <!-- end Container -->

  <!-- Modal Tambah Video -->
  <div class="modal fade" id="tambahVideo" tabindex="-1" role="dialog" aria-labelledby="tambahVideoTitle" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahVideoTitle">Form Tambah Video</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="file" class="col-form-label">Pilih File <span class="text-danger">* Maks: 512 MB | File: mp4</span></label>
                  <input type="file" class="form-control-file" id="file" name="file">
                </div>
                <div class="form-group">
                  <label for="judul" class="col-form-label">Judul <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="judul" name="judul" autocomplete="off" required>
                </div>
              </div> <!-- end of col-md-6 -->
              <div class="col-md-6">
                <div class="form-group">
                  <label class="col-form-label d-block">Batasan Umur Video? <span class="text-danger">*</span></label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="umur" id="umur0" value="0" checked>
                    <label class="form-check-label" for="umur0">Tidak, Dapat ditonton anak.</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="umur" id="umur1" value="1">
                    <label class="form-check-label" for="umur1">Ya, Anak tidak dapat menonton.</label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="kategori" class="col-form-label">Kategori <span class="text-danger">*</span></label>
                  <input type="text" name="kategori" id="kategori" class="form-control" list="kategoriList" required>
                  <datalist id="kategoriList">
                    <?php while ($kategori = mysqli_fetch_assoc($dataKategori)) : ?>
                      <option value="<?= $kategori['judul']; ?>"><?= $kategori['judul']; ?></option>
                    <?php endwhile; ?>
                  </datalist>
                  <small class="text-danger ml-1">*) Ketik lalu tunggu rekomendasi muncul, jika ada.</small>
                </div>
              </div>
            </div>
            <div class="input-group">
              <label for="url_video" class="col-form-label w-100">URL Video</label>
              <input type="text" id="url_video" class="form-control disabled" value="<?= base_url('index.php?page=watch&v=') . $urlVideo; ?>" style="border-top-left-radius: .25rem !important; border-bottom-left-radius: .25rem !important;" readonly>
              <span class="input-group-text input-group-prepend" id="copyBtn" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;" onclick="copyFunc()">
                <span id="toggle">copy</span>
              </span>
            </div>
            <div class="form-group mb-4">
              <label for="keterangan" class="col-form-label">Deskripsi Video</label>
              <textarea id="keterangan"></textarea>
            </div>
            <div class="form-group">
              <div class="progress mb-3" style="display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div id="uploadStatus"></div>
            <input type="hidden" name="id_user" value="<?= $_SESSION['username']; ?>">
            <input type="hidden" name="id_video" value="<?= $urlVideo; ?>">
            <input type="hidden" name="tanggal" value="<?= time(); ?>">
            <input type="hidden" name="upload" value="video_baru">
            <button type="submit" class="btn btn-primary" id="uploadBtn">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div> <!-- end modal tambah video -->

  <footer class="blog-footer bg-secondary">
    <span class="text-center">&copy; <?= date('Y'); ?> Made With &hearts; <a class="text-info" href="<?= base_url(''); ?>">My Video</a></span>
  </footer>

  <script src="<?= base_url('assets/js/jquery-3.2.1.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/popper.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?= base_url('assets/ckeditor/ckeditor.js'); ?>"></script>
  <script src="<?= base_url('assets/js/video.js'); ?>"></script>
  <script src="<?= base_url('assets/js/videojs.hotkeys.js'); ?>"></script>
  <script src="<?= base_url('assets/js/videojs-mobile-ui.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/chart.min.js'); ?>"></script>
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
        $('#themeBtn').removeClass('text-dark').addClass('text-white');
        $('.akun-nav').removeClass('border-white').addClass('border-dark');
        $('.dashboard-nav').removeClass('border-white').addClass('border-dark');
      }
      themeBtn.innerHTML = toggle
    }

    // copy URL Video
    function copyFunc() {
      var copyText = document.getElementById("url_video");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
      document.getElementById("toggle").innerHTML = "copied";
    }

    $('#tbl_kategori_kosong, #tbl_kategori, #tbl_user, #tbl_video, #example').DataTable({
      responsive: true,
      columnDefs: [{
        responsivePriority: 1,
        targets: 0
      }],
      language: {
        "emptyTable": "Tidak ada data",
        "info": "_START_ sampai _END_ dari _TOTAL_ ",
        "infoEmpty": "0 sampai 0 dari 0 ",
        "infoFiltered": "(disaring dari _MAX_)",
        "infoThousands": "'",
        "lengthMenu": "Tampilkan _MENU_ data",
        "loadingRecords": "Sedang memuat...",
        "processing": "Sedang memproses...",
        "search": "Cari:",
        "zeroRecords": "Tidak ditemukan data",
        "thousands": "'",
        "paginate": {
          "first": "Pertama",
          "last": "Terakhir",
          "next": "Lanjut",
          "previous": "Mundur"
        }
      }
    });

    // if dcmn ready
    $(document).ready(function() {
      // grafik
      var data = {
        labels: [<?php while ($tg = mysqli_fetch_assoc($titleGrafik)) {
                    echo '"' . $tg['judul'] . '",';
                  } ?>],
        datasets: [{
          label: "Video Teratas",
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(46, 139, 87, 0.2)',
            'rgba(0, 0, 128, 0.2)',
            'rgba(253, 215, 3, 0.2)',
            'rgba(128, 128, 128, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(186, 85, 211, 0.2)',
            'rgba(54, 162, 235, 0.2)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(46, 139, 87, 1)',
            'rgba(0, 0, 128, 1)',
            'rgba(253, 215, 3, 1)',
            'rgba(128, 128, 128, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(186, 85, 211, 1)',
            'rgba(54, 162, 235, 1)'
          ],
          borderWidth: 1,
          hoverBackgroundColor: [
            'rgba(255, 99, 132, 0.4)',
            'rgba(46, 139, 87, 0.4)',
            'rgba(0, 0, 128, 0.4)',
            'rgba(253, 215, 3, 0.4)',
            'rgba(128, 128, 128, 0.4)',
            'rgba(255, 159, 64, 0.4)',
            'rgba(186, 85, 211, 0.4)',
            'rgba(54, 162, 235, 0.4)'
          ],
          hoverBorderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(46, 139, 87, 1)',
            'rgba(0, 0, 128, 1)',
            'rgba(253, 215, 3, 1)',
            'rgba(128, 128, 128, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(186, 85, 211, 1)',
            'rgba(54, 162, 235, 1)'
          ],
          data: [<?php while ($vg = mysqli_fetch_assoc($valueGrafik)) {
                    echo $vg['viewers'] . ',';
                  } ?>],
        }]
      };

      var options = {
        maintainAspectRatio: false,
        scales: {
          yAxes: [{
            stacked: true,
            gridLines: {
              display: true,
              color: "rgba(160, 82, 45, 0.2)"
            }
          }],
          xAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      Chart.Bar('chart', {
        options: options,
        data: data
      });


      <?php if (mysqli_num_rows($titleGrafik) == 0) : ?>
        $('.chart-container').css('display', 'none');
      <?php endif; ?>

      CKEDITOR.replace('keterangan', {
        enterMode: Number(1),
        toolbarGroups: [{
            name: 'basicstyles',
            groups: ['basicstyles']
          },
          {
            name: 'paragraph',
            groups: ['list', 'blocks']
          }
        ]
      });

      <?php if ($dataKategori) : ?>
        var dk = [<?php foreach ($dataKategori as $dk) {
                    echo '"' . $dk['judul'] . '",';
                  } ?>];

        $('input[name="jdl_kategori"]').keyup(function(e) {
          e.preventDefault();
          var inp = $(this).val().trim();
          if (inp != '') {
            if (!dk.includes(inp)) {
              $(this).removeClass(' is-invalid').addClass(' is-valid');
              $('#inputFeedback').removeClass('invalid-feedback').addClass('valid-feedback').html('Kategori aman');
              $('button[name="editKategori"]').prop('disabled', false);
            } else {
              $(this).removeClass(' is-valid').addClass(' is-invalid');
              $('#inputFeedback').removeClass('valid-feedback').addClass('invalid-feedback').html('Kategori sudah sudah ada');
              $('button[name="editKategori"]').prop('disabled', true);
            }
          } else {
            $(this).removeClass(' is-valid').addClass(' is-invalid');
            $('#inputFeedback').removeClass('valid-feedback').addClass('invalid-feedback').html('Judul kategori tidak boleh kosong');
            $('button[name="editKategori"]').prop('disabled', true);
          }
        });
      <?php endif; ?>

      <?php if ($_GET['page'] == 'edit') : ?>
        // videoplyaerjs
        var player = videojs('videoPlayer', {
          controls: true,
          fluid: true,
          playbackRates: [0.25, 0.5, 1, 1.5, 1.75],
          plugins: {
            hotkeys: {
              volumeStep: 0.1,
              seekStep: 10,
              enableMute: true,
              enableFullscreen: true,
              enableNumbers: false,
              enableVolumeScroll: true,
              enableHoverScroll: true
            }
          }
        });
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
      <?php endif; ?>

      $("#uploadForm").on('submit', function(e) {
        e.preventDefault();
        $(".progress").show();
        $('#uploadBtn').prop('disabled', true);
        // deklarasi formdata obj
        var data = new FormData(this);
        // masukkan textarea value ke obj
        data.append('keterangan', CKEDITOR.instances['keterangan'].getData());
        $.ajax({
          xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                $(".progress-bar").width(percentComplete + '%');
                $(".progress-bar").html(percentComplete + '%');
              }
            }, false);
            return xhr;
          },
          type: 'POST',
          url: 'user.php',
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
            $(".progress-bar").width('0%');
            $('#uploadStatus').html('loading');
          },
          error: function() {
            $('#uploadStatus').html('<p style="color:#EA4335;">Upload file gagal, silakan coba lagi.</p>');
          },
          success: function(resp) {
            // console.log(resp);
            if (resp == 'ok') {
              $('#uploadForm')[0].reset();
              $('#uploadStatus').html('<p style="color:#28A74B;">File berhasil diupload!</p>');
              $(this).unbind();
              $('#uploadBtn').html('Menyelesaikan...');
              setTimeout(function() {
                window.location.href = "<?= base_url('user.php?page=dashboard'); ?>";
              }, 500);
            } else {
              $('#uploadStatus').html('<p style="color:#EA4335;">' + resp + '</p>');
            }
          }
        });
      });

      $("#file").change(function() {
        var allowedTypes = ['video/mp4', 'video/3gp', 'video/webm'];
        var allowedSize = ['4096000000']; //512 MB limit
        var file = this.files[0];
        var fileType = file.type;
        var fileSize = file.size;
        var fileName = file.name;
        if (!allowedTypes.includes(fileType)) {
          alert('Silakan pilih file yang valid (.mp4) \nFile Anda ' + fileType);
          $("#file").val('');
          return false;
        } else if (fileSize > allowedSize) {
          alert('Ukuran file terlalu besar.');
          $("#file").val('');
          return false;
        } else {
          $('input[name="judul"]').val(fileName.substring(0, fileName.length - 4));
        }
      });

      $('#updateVid').on('submit', function(e) {
        e.preventDefault();
        var data = new FormData(this);
        // masukkan textarea value ke obj
        data.append('keterangan', CKEDITOR.instances['keterangan'].getData());
        $.ajax({
          url: 'user.php',
          type: 'POST',
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          success: function(resp) {
            if (resp == 'ok') {
              $(this).unbind();
              alert('Berhasil mengupdate data.')
              window.location.href = "<?= base_url('user.php?page=dashboard'); ?>";
            } else {
              alert(resp);
            }
          }
        });
      })

      setTimeout(function() {
        $("#pesan").fadeIn('slow');
      }, 200);
      setTimeout(function() {
        $("#pesan").fadeOut('slow');
      }, 5000);

      $('#showPass').on('change', function(event) {
        event.preventDefault();
        if ($('#password, #password1, #password2').attr("type") == "text") {
          $('#password').attr('type', 'password');
          $('#password1').attr('type', 'password');
          $('#password2').attr('type', 'password');
        } else if ($('#password, #password1, #password2').attr("type") == "password") {
          $('#password').attr('type', 'text');
          $('#password1').attr('type', 'text');
          $('#password2').attr('type', 'text');
        }
      });

      $('form#searchForm').submit(function() {
        $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-dark" role="status"><span class="sr-only">Loading...</span></div>`);
      });

      // prepare var to delete vid and ktg
      var videoTotal <?php if ($_GET['page'] == 'dashboard') {
                        echo '=' . number_format(mysqli_num_rows($listVideo)) . ';';
                      } else {
                        echo ';';
                      } ?>
      var ktgTotal <?php if ($_GET['page'] == 'dashboard' && $_SESSION['role_id'] == 1) {
                      echo '=' .  number_format(mysqli_num_rows($cariTotalKategori)) . ';';
                    } else {
                      echo ';';
                    } ?>

      // haps video dashboard
      $(document).on('click', '#delete_video', function(e) {
        e.preventDefault();
        const id_video = $(this).data('delete');
        const deleteId = $(this).data('id');
        videoTotal--;
        $.ajax({
          url: "user.php?page=dashboard&delete-video=" + id_video,
          type: "get",
          success: function(resp) {
            // console.log(resp);
            if (resp == 'ok') {
              $('#video_id_' + deleteId).html('Berhasil hapus video.');
              $('h5#total_video').html(videoTotal);
              $('#video_id_' + deleteId).slideUp('slow');
            } else {
              alert(resp);
            }
          },
          error: function() {
            alert('Terjadi kesalahan, coba lagi nanti.');
          }
        });
      });

      // haps ktg dashboard
      $(document).on('click', '#delete_kategori', function(e) {
        e.preventDefault();
        const ktgId = $(this).data('id');
        ktgTotal--;
        $.ajax({
          url: "user.php?page=dashboard&delete-kategori=" + ktgId,
          type: "get",
          success: function(resp) {
            // console.log(resp);
            if (resp == 'ok') {
              $('#ktg_id_' + ktgId).html('Berhasil hapus kategori.');
              $('h5#total_kategori').html(ktgTotal);
              $('#ktg_id_' + ktgId).slideUp('slow');
            } else {
              alert(resp);
            }
          },
          error: function() {
            alert('Terjadi kesalahan, coba lagi nanti.');
          }
        });
      });

    });
  </script>
</body>

</html>