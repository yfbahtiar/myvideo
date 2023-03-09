<?php
// cari data user
$editUser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$session'")->fetch_assoc();

// tangani edit akun
if (isset($_POST['profile'])) {
    $fullname = htmlspecialchars($_POST['fullname']);
    $birthDate = strtotime($_POST['birthDate']);

    // update data
    mysqli_query($conn, "UPDATE users SET fullname = '$fullname', birthDate = '$birthDate' WHERE username = '$session'");
    $_SESSION['pesan'] = 'Profil berhasil update';

    // di website nya pake js
    echo "<script>window.location.href = 'user.php?page=akun';</script>";
    // header("Location: user.php?page=akun");
    exit;
}

// tangani ganti password
if (isset($_POST['passwd'])) {
    $pass = $_POST["password"];
    $pass1 = $_POST["password1"];
    $pass2 = $_POST["password2"];

    // cek apakah passwd sama dg yg lama
    if (!password_verify($pass, $editUser['password'])) {
        $_SESSION['pesan'] = 'Salah memasukkan password lama.';
        // di website nya pake js
        echo "<script>window.location.href = 'user.php?page=akun';</script>";
        // header("Location: user.php?page=akun");
        exit;
    } elseif ($pass2 != $pass1) {
        $_SESSION['pesan'] = 'Pengulangan password harus sama.';
        // di website nya pake js
        echo "<script>window.location.href = 'user.php?page=akun';</script>";
        // header("Location: user.php?page=akun");
        exit;
    } else {
        // password ok
        $enkrip_pass = password_hash($pass2, PASSWORD_DEFAULT);
        // update passowrd
        mysqli_query($conn, "UPDATE users SET password = '$enkrip_pass' WHERE username = '$session'");
        $_SESSION['pesan'] = 'Password telah diganti.';
        // di website nya pake js
        echo "<script>window.location.href = 'user.php?page=akun';</script>";
        // header("Location: user.php?page=akun");
        exit;
    }
}
?>
<div class="card">
    <div class="card-header">
        Edit Akun
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <form action="" method="post">
                        <div class="card-header bg-info text-white">
                            Data Umum
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control disabled" required value="<?= $editUser['username']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="fullname">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control" autocomplete="off" required value="<?= $editUser['fullname']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="birthDate">Tanggal Lahir</label>
                                <input type="date" name="birthDate" id="birthDate" class="form-control" required value="<?= date("Y-m-d", $editUser['birthDate']); ?>">
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" name="profile" class="btn btn-info w-50">Edit Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <form action="" method="post">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Ganti Password
                        </div>

                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="password">Masukkan Password Lama</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password1" id="password1" class="form-control" required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="password1">Ulangi Password</label>
                                <input type="password" name="password2" id="password2" class="form-control" required>
                            </div>
                            <div class="form-group form-check mb-3 ml-1">
                                <input type="checkbox" class="form-check-input" id="showPass">
                                <label class="form-check-label" for="showPass">Tampilkan Password</label>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" name="passwd" class="btn btn-secondary w-50">Ganti Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>