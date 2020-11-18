<?php
session_start();

if (!isset($_SESSION['login'])) {
  header('Location:login.php');
  exit;
}

require 'function.php';

// mencari url jika tambah video
$a = rand(4, 9);
$urlVideo = generateVideoLink($a);
// cek ketersediaan url di db
$urlCek = mysqli_query($conn, "SELECT * FROM videos WHERE url = '$urlVideo'");
if (mysqli_num_rows($urlCek) === 1) {
  $urlVideo = generateVideoLink($a);
}

// query kategori untuk form
$dataKategori = mysqli_query($conn, "SELECT * FROM categories");

// tangani upload video
if (isset($_POST["upload"])) {
  $url = $_POST['url'];
  $id_user = $_POST['id_user'];
  $judul = trim(htmlspecialchars($_POST['judul'], true));
  $kategori = $_POST['kategori'];
  $keterangan = trim(htmlspecialchars($_POST['keterangan'], true));
  $tanggal = time();
  // menangani file upload
  $name = $_FILES['file']['name'];
  $type = $_FILES['file']['type'];
  $size = $_FILES['file']['size'];
  $namaFolder = 'assets/file/';
  if (file_exists($namaFolder . str_replace(" ", "_", $name))) {
    $namaFile = $id_user . '-' . str_replace(" ", "_", $name);
  } else {
    $namaFile = str_replace(" ", "_", $name);
  }
  $tmpName = $_FILES['file']['tmp_name'];
  $fileBaru = $namaFolder . basename($namaFile);
  // maks file 19Mb
  if ((($type == "video/mp4") || ($type == "video/3gpp")) && ($size < 19000000)) {
    move_uploaded_file($tmpName, $fileBaru);
    $statusUpload = true;
  } else {
    $statusUpload = false;
  }

  if ($statusUpload == true) {
    mysqli_query($conn, "INSERT INTO videos VALUES ('', '$url', '$id_user', '$kategori', '$judul', '$namaFile', '$keterangan', '$tanggal')");
    mysqli_query($conn, "INSERT INTO views VALUES ('', '$url', '0')");
    mysqli_query($conn, "INSERT INTO video_like VALUES ('', '$url', '', '0')");
    mysqli_query($conn, "INSERT INTO video_dislike VALUES ('', '$url', '', '0')");
    $_SESSION['pesan'] = 'Upload video ' . $namaFile . ' berhasil';
  } else {
    $_SESSION['pesan'] = 'File video terlalu besar atau format video salah';
  }
}

// tangani hapus kategori
if (isset($_GET['delete-kategori'])) {
  if ($_SESSION['role_id'] != 1) {
    $_SESSION['pesan'] = 'Anda bukan Admin!';

    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php?page=dashboard");
    exit;
  } else {
    $deleteIdKategori = $_GET['delete-kategori'];
    mysqli_query($conn, "DELETE FROM categories WHERE id = '$deleteIdKategori'");
    $_SESSION['pesan'] = 'Kategori berhasil dihapus';
    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php?page=dashboard");
    exit;
  }
}

// tangani delete akun
if (isset($_GET['delete-akun'])) {
  if ($_SESSION['role_id'] != 1) {
    $_SESSION['pesan'] = 'Anda bukan Admin!';

    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php?page=dashboard");
    exit;
  } else {
    $idDeleteAkun = $_GET['delete-akun'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$idDeleteAkun'");
    $_SESSION['pesan'] = 'User berhasil dihapus';
    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php?page=akun");
    exit;
  }
}

// tangani hapus video
if (isset($_GET['delete-video'])) {
  $deleteUrlVideo = $_GET['delete-video'];
  $direktori = 'assets/file/';
  $cariFile = mysqli_query($conn, "SELECT * FROM videos WHERE url = '$deleteUrlVideo'")->fetch_assoc();
  $file = $carFile['target'];

  // unlink direktori lokal
  unlink($direktori . $file);
  // hapus video dari db
  mysqli_query($conn, "DELETE FROM videos WHERE url = '$deleteUrlVideo'");
  // hapus viewers
  mysqli_query($conn, "DELETE FROM views WHERE url_video = '$deleteUrlVideo'");
  // hapus video_like
  mysqli_query($conn, "DELETE FROM video_like WHERE url_video = '$deleteUrlVideo'");
  // hapus video_dislike
  mysqli_query($conn, "DELETE FROM video_dislike WHERE url_video = '$deleteUrlVideo'");
  // hapus komentar
  mysqli_query($conn, "DELETE FROM komentar WHERE url_video = '$deleteUrlVideo'");

  $_SESSION['pesan'] = 'Video berhasil dihapus';

  // di website nya pake js
  //echo "<script>window.location.href = 'login.php';</script>";
  header("Location: user.php?page=dashboard");
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>User | My video</title>
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

    a.dashboard-nav,
    a.akun-nav {
      text-decoration: none;
    }

    a.dashboard-nav:hover,
    a.akun-nav:hover {
      border-color: #ffc107 !important;
    }

    .container {
      margin-top: 110px;
    }

    .blog-footer {
      padding: 2rem 0;
      color: #f8f9fa;
      text-align: center;
      border-top: 0.3rem solid #17a2b8;
    }

    .input-group input[type="password"] {
      border-top-left-radius: .25rem !important;
      border-bottom-left-radius: .25rem !important;
    }

    /* tablet */
    @media screen and (max-width: 991.98px) {
      .container {
        margin-top: 80px;
      }

      /* mobile */
      @media screen and (max-width: 576px) {
        #q {
          margin-bottom: 5px;
        }
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-secondary">
    <a class="navbar-brand" href="<?= base_url(''); ?>">
      <img src="<?= base_url('assets/img/ico.svg'); ?>" alt="" width="32" height="32" class="d-inline-block align-top">
      <?php
      $session = $_SESSION['username'];
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
        <a onclick="return confirm('Yakin mau keluar...?')" class="nav-item btn btn-outline-danger" href="?page=logout">Logout</a>
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

    <?php if (isset($_GET['edit-kategori'])) : ?>
      <?php
      if ($_SESSION['role_id'] != 1) {
        $_SESSION['pesan'] = 'Anda bukan Admin!';

        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=dashboard");
        exit;
      }
      // tangani edit
      $editIdKategori = $_GET['edit-kategori'];
      $editKategori = mysqli_query($conn, "SELECT * FROM categories WHERE id = '$editIdKategori'")->fetch_assoc();

      // tangani edit kategori
      if (isset($_POST['editKategori'])) {
        $jdl_kategori = trim(htmlspecialchars($_POST['jdl_kategori']));

        // update data
        mysqli_query($conn, "UPDATE categories SET judul = '$jdl_kategori' WHERE id = '$editIdKategori'");
        $_SESSION['pesan'] = 'Kategori berhasil diupdate';
        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=dashboard");
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
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="editKategori" class="btn btn-primary">Edit</button>
            <a href="?page=dashboard" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    <?php endif; ?>

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
  <div class="modal fade" id="tambahVideo" tabindex="-1" role="dialog" aria-labelledby="tambahVideoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahVideoTitle">Form Tambah Video</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="id_user" class="col-form-label">Uploader</label>
              <input type="text" class="form-control disabled" id="id_user" name="id_user" value="<?= $_SESSION['username']; ?>" readonly>
            </div>
            <div class="form-group">
              <label for="url" class="col-form-label">URL</label>
              <input type="text" class="form-control disabled" id="url" name="url" value="<?= $urlVideo; ?>" readonly>
            </div>
            <div class="form-group">
              <label for="judul" class="col-form-label">Judul <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="judul" name="judul" required>
            </div>
            <div class="form-group">
              <label for="kategori" class="col-form-label">Kategori</label>
              <select name="kategori" id="kategori" class="form-control">
                <?php while ($kategori = mysqli_fetch_assoc($dataKategori)) : ?>
                  <option value="<?= $kategori['judul']; ?>"><?= $kategori['judul']; ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="file" class="col-form-label">Pilih File</label>
              <input type="file" class="form-control-file" id="file" name="file">
              <small class="text-danger ml-1">Maks: 19Mb | File: mp4/3gp</small>
            </div>
            <div class="form-group">
              <label for="keterangan" class="col-form-label">Keterangan</label>
              <textarea type="text" class="form-control" id="keterangan" name="keterangan"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="upload">Upload</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div> <!-- end modal tambah video -->

  <footer class="blog-footer bg-secondary">
    <span class="text-center">&copy; <?= date('Y'); ?> <a class="text-info" href="<?= base_url(''); ?>">My Video</a></span>
  </footer>

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

    $(document).ready(function() {
      // memvalidasi file
      $("#file").change(function() {
        var allowedTypes = ['video/mp4', 'video/3gp'];
        var allowedSize = ['19000000'];
        var file = this.files[0];
        var fileType = file.type;
        var fileSize = file.size;
        if (!allowedTypes.includes(fileType)) {
          alert('Silakan pilih file yang valid (MP4/3GP).');
          $("#file").val('');
          return false;
        } else if (fileSize > allowedSize) {
          alert('Ukuran file terlalu besar.');
          $("#file").val('');
          return false;
        }
      });

      $('#example').DataTable();

      $('#example1').DataTable();

      setTimeout(function() {
        $("#pesan").fadeIn('slow');
      }, 200);
      setTimeout(function() {
        $("#pesan").fadeOut('slow');
      }, 5000);

      $('#showPass').on('click', function(event) {
        event.preventDefault();
        if ($('#password').attr("type") == "text") {
          $('#password').attr('type', 'password');
          $('#toggle').html('Tampil');
        } else if ($('#password').attr("type") == "password") {
          $('#password').attr('type', 'text');
          $('#toggle').html('Tutup');
        }
      })

      $('#showPass1').on('click', function(event) {
        event.preventDefault();
        if ($('#password1').attr("type") == "text") {
          $('#password1').attr('type', 'password');
          $('#toggle1').html('Tampil');
        } else if ($('#password1').attr("type") == "password") {
          $('#password1').attr('type', 'text');
          $('#toggle1').html('Tutup');
        }
      })

      $('#showPass2').on('click', function(event) {
        event.preventDefault();
        if ($('#password2').attr("type") == "text") {
          $('#password2').attr('type', 'password');
          $('#toggle2').html('Tampil');
        } else if ($('#password2').attr("type") == "password") {
          $('#password2').attr('type', 'text');
          $('#toggle2').html('Tutup');
        }
      })

      $('form').submit(function() {
        $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-dark" role="status"><span class="sr-only">Loading...</span></div>`);
      })
    });
  </script>
</body>

</html>