<?php
session_start();
require 'function.php';
// cek ketersediaan username
if (isset($_GET['newUser'])) {
    $usernameCheck = $_GET['newUser'];
    $cekusername = mysqli_query($conn, "SELECT * FROM users WHERE username = '$usernameCheck'");
    if (mysqli_fetch_assoc($cekusername)) {
        echo "stop";
        exit;
    } else {
        echo "lanjut";
        exit;
    }
}
if (isset($_POST['submit'])) {
    $uss = trim(htmlspecialchars($_POST['username']));
    $fullname = trim(htmlspecialchars($_POST['fullname']));
    $birthDate = strtotime($_POST['birthDate']);
    $pas = $_POST['password'];
    $pas1 =  $_POST['password1'];
    $role_id = 2;
    if ($pas1 != $pas) {
        $_SESSION['pesan'] = 'Pengulangan password harus sama.';
        echo "<script>window.location.href = 'registrasi.php';</script>";
        // header('location:registrasi.php');
        return false;
    }
    $password = password_hash($pas1, PASSWORD_DEFAULT);
    $sql = $conn->query("INSERT INTO users (username, fullname, password, role_id, birthDate) VALUES ('$uss', '$fullname', '$password', '$role_id', '$birthDate')");

    if (!$sql) {
        $_SESSION['pesan'] = 'Gagal membuat akun.';
        echo "<script>window.location.href = 'registrasi.php';</script>";
        // header('location:registrasi.php');
        return false;
    } else {
        $rdr = base_url('login.php');
        $_SESSION['pesan'] = 'Berhasil membuat akun.';
        echo "<script>window.location.href = '" . urldecode($rdr) . "';</script>";
        // header('location:' . urldecode($rdr)');
        return false;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Registrasi</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Silahkan buat akun untuk menikmati layanan My Video, gratis dan aman.">
    <meta name="keywords" content="My Video, tonton dan bagiakan video menarik Anda disini.">
    <meta name="author" content="Yusuf Bahtiar @menpc3o">
    <link rel="icon" href="<?= base_url('assets/img/ico.png'); ?>" type="image/x-icon">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f0f0f0;
        }

        .body-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>

<body>

    <form class="body-form" method="post" action="">
        <h3 class="mb-4 font-weight-normal text-center">Halaman Registrasi</h3>
        <?php
        if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
            echo '<div id="pesan" class="alert alert-warning" style="display:none;">' . $_SESSION['pesan'] . '</div>';
        }
        $_SESSION['pesan'] = '';
        ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" autocomplete="off" autofocus required>
            <div class="" id="inputFeedback"></div>
        </div>
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" class="form-control" autocomplete="off" required>
        </div>
        <div class="form-group">
            <label for="birthDate">Tanggal Lahir</label>
            <input type="date" name="birthDate" id="birthDate" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="password" class="w-100">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group mb-2">
            <label for="password1" class="w-100">Ulangi Password</label>
            <input type="password" name="password1" id="password1" class="form-control" required>
        </div>
        <div class="form-group form-check mb-3 ml-1">
            <input type="checkbox" class="form-check-input" id="showPass">
            <label class="form-check-label" for="showPass">Tampilkan Password</label>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-user" name="submit" disabled>Daftarkan Akun</button>
        <div class="text-center mt-3">
            <a href="<?= base_url('login.php'); ?>" class="text-center">Kembali ke Login</a>
        </div>
    </form>

    <script src="<?= base_url('assets/js/jquery-3.2.1.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/popper.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>

    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $("#pesan").fadeIn('slow');
            }, 200);
            setTimeout(function() {
                $("#pesan").fadeOut('slow');
            }, 5000);

            $('#username').keyup(function(e) {
                e.preventDefault();
                var newUSer = $('#username').val();
                if (newUSer != '') {
                    $.ajax({
                        url: 'registrasi.php?newUser=' + newUSer,
                        data: {
                            newUSer: newUSer
                        },
                        type: 'get',
                        success: function(resp) {
                            if (resp == 'lanjut') {
                                $('#username').removeClass(' is-invalid').addClass(' is-valid');
                                $('#inputFeedback').removeClass('invalid-feedback').addClass('valid-feedback').html('Username tersedia.');
                                $('button[name="submit"]').prop('disabled', false);
                            } else if (resp == 'stop') {
                                $('#username').removeClass(' is-valid').addClass(' is-invalid');
                                $('#inputFeedback').removeClass('valid-feedback').addClass('invalid-feedback').html('Username sudah terpakai.');
                                $('button[name="submit"]').prop('disabled', true);
                            }
                        }
                    });
                } else {
                    $('#username').removeClass(' is-invalid').removeClass(' is-valid').addClass(' is-invalid');
                    $('#inputFeedback').removeClass('invalid-feedback').removeClass('valid-feedback').addClass(' invalid-feedback').html('Username tidak boleh kosong.');
                    $('button[name="submit"]').prop('disabled', true);
                }
            })

            $('form').submit(function() {
                $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only">Loading...</span></div>`);
            })

            $('#showPass').on('change', function(event) {
                event.preventDefault();
                if ($('#password, #password1').attr("type") == "text") {
                    $('#password').attr('type', 'password');
                    $('#password1').attr('type', 'password');
                } else if ($('#password, #password1').attr("type") == "password") {
                    $('#password').attr('type', 'text');
                    $('#password1').attr('type', 'text');
                }
            });
        });
    </script>
</body>

</html>