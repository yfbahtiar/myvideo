<?php
session_start();

require 'function.php';

if (isset($_POST["submit"])) {
    $uss = trim(htmlspecialchars($_POST["username"]));
    $fullname = trim(htmlspecialchars($_POST["fullname"]));
    $pas = $_POST["password"];
    $pas1 =  $_POST["password1"];

    // cek ketersediaan username
    $cekusername = mysqli_query($conn, "SELECT * FROM users WHERE username = '$uss'");
    if (mysqli_fetch_assoc($cekusername)) {
        $_SESSION['pesan'] = 'Username tidak tersedia.';
        // echo "<script>window.location.href = 'registrasi.php';</script>";
        header('location:registrasi.php');
        return false;
    }

    if ($pas1 != $pas) {
        $_SESSION['pesan'] = 'Pengulangan password harus sama.';
        echo "<script>window.location.href = 'registrasi.php';</script>";
        return false;
    }

    $password = password_hash($pas1, PASSWORD_DEFAULT);
    $registrasi = mysqli_query($conn, "INSERT INTO users VALUES ('', '$uss', '$fullname', '$password', '2' ) ");
    if ($registrasi) {
        $_SESSION['pesan'] = 'Silahkan login.';
        // echo "<script>window.location.href = 'login.php';</script>";
        header('location:login.php');
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Registrasi | My Video</title>
    <link rel="icon" href="<?= base_url('assets/img/ico.svg'); ?>" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
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

        .input-group input[type="password"] {
            border-top-left-radius: .25rem !important;
            border-bottom-left-radius: .25rem !important;
        }
    </style>
</head>

<body>

    <form class="body-form" method="post">
        <h3 class="mb-4 font-weight-normal text-center">Silahkan Membuat Akun!</h3>
        <?php
        if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
            echo '<div id="pesan" class="alert alert-warning" style="display:none;">' . $_SESSION['pesan'] . '</div>';
        }
        $_SESSION['pesan'] = '';
        ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" max="15" autocomplete="off" autofocus required>
        </div>
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" class="form-control" max="15" autocomplete="off" required>
        </div>
        <div class="input-group mb-3">
            <label for="password" class="w-100">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <span class="input-group-text input-group-prepend" id="showPass" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                <span id="toggle">Tampil</span>
            </span>
        </div>
        <div class="input-group mb-4">
            <label for="password1" class="w-100">Ulangi Password</label>
            <input type="password" name="password1" id="password1" class="form-control" required>
            <span class="input-group-text input-group-prepend" id="showPass1" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                <span id="toggle1">Tampil</span>
            </span>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-user" name="submit">Daftarkan Akun</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-center">Login</a>
        </div>
    </form>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $("#pesan").fadeIn('slow');
            }, 200);
            setTimeout(function() {
                $("#pesan").fadeOut('slow');
            }, 5000);


            $('form').submit(function() {
                $('button[type="submit"]').addClass('disabled').html(`<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only">Loading...</span></div>`);
            })

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
        });
    </script>
</body>

</html>