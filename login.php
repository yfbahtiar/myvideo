<?php
session_start();

if (isset($_SESSION["login"])) {
    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header('location:user.php');
}

require 'function.php';


if (isset($_POST["submit"])) {
    $uss = trim(htmlspecialchars($_POST["username"]));
    $pas = $_POST["password"];

    $cekuser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$uss'");

    if (mysqli_num_rows($cekuser) === 1) {
        $row = mysqli_fetch_assoc($cekuser);
        if (password_verify($pas, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['login'] = true;

            $_SESSION['pesan'] = 'Selamat datang ' . $_SESSION['username'];
            // di website nya pake js
            //echo "<script>window.location.href = 'login.php';</script>";
            if (isset($_GET['rdr'])) {
                header('location:' . urldecode($_GET['rdr']));
                exit;
            } else {
                header('location: user.php?page=dashboard');
                exit;
            }
        }
    }
    $_SESSION['pesan'] = 'Username / Password salah';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Login | My Video</title>
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
        <h3 class="mb-4 font-weight-normal text-center">Halaman Login</h3>
        <?php
        if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
            echo '<div id="pesan" class="alert alert-warning" style="display:none;">' . $_SESSION['pesan'] . '</div>';
        }
        $_SESSION['pesan'] = '';
        ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" autocomplete="off" autofocus required>
        </div>
        <div class="input-group mb-3">
            <label for="password" class="w-100">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <span class="input-group-text input-group-prepend" id="showPass" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                <span id="toggle">Tampil</span>
            </span>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-user" name="submit">Login</button>
        <div class="text-center mt-3">
            <a href="registrasi.php" class="text-center">Belum punya akun? Daftar</a>
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
        });
    </script>
</body>

</html>