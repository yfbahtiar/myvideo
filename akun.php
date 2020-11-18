<?php
// cari data user
$editUser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$session'")->fetch_assoc();

// tangani edit akun
if (isset($_POST['profile'])) {
    $fullname = htmlspecialchars($_POST["fullname"]);

    // update data
    mysqli_query($conn, "UPDATE users SET fullname = '$fullname' WHERE username = '$session'");
    $_SESSION['pesan'] = 'Profil berhasil update';

    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php?page=akun");
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
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=akun");
        exit;
    } elseif ($pass2 != $pass1) {
        $_SESSION['pesan'] = 'Pengulangan password harus sama.';
        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=akun");
        exit;
    } else {
        // password ok
        $enkrip_pass = password_hash($pass2, PASSWORD_DEFAULT);
        // update passowrd
        mysqli_query($conn, "UPDATE users SET password = '$enkrip_pass' WHERE username = '$session'");
        $_SESSION['pesan'] = 'Password telah diganti.';
        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=akun");
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
                            <div class="input-group mb-3">
                                <label for="password" class="w-100">Masukkan Password Lama</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <span class="input-group-text input-group-prepend" id="showPass" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                                    <span id="toggle">Tampil</span>
                                </span>
                            </div>
                            <div class="input-group mb-3">
                                <label for="password1" class="w-100">Password Baru</label>
                                <input type="password" name="password1" id="password1" class="form-control" required>
                                <span class="input-group-text input-group-prepend" id="showPass1" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                                    <span id="toggle1">Tampil</span>
                                </span>
                            </div>
                            <div class="input-group mb-4">
                                <label for="password2" class="w-100">Ulangi Password Baru</label>
                                <input type="password" name="password2" id="password2" class="form-control" required>
                                <span class="input-group-text input-group-prepend" id="showPass2" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; cursor: pointer;">
                                    <span id="toggle2">Tampil</span>
                                </span>
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
<?php if ($_SESSION['role_id'] == 1) :
    $getUser = mysqli_query($conn, "SELECT * FROM users WHERE id != '1'");
?>
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    Daftar Akun
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-custom table-responsive" id="example">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Username</th>
                                <th scope="col">Fullname</th>
                                <th scope="col">Role</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $c = 1;
                            while ($row = mysqli_fetch_assoc($getUser)) : ?>
                                <tr>
                                    <th scope="row"><?= $c; ?></th>
                                    <td><?= $row['username']; ?></td>
                                    <td><?= $row['fullname']; ?></td>
                                    <td><span class="badge badge-pill badge-info"><?php if ($row['role_id'] == 2) {
                                                                                        echo 'Member';
                                                                                    } else {
                                                                                        echo 'Administrator';
                                                                                    } ?></span></td>
                                    <td><a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" href="?page=akun&delete-akun=<?= $row['id']; ?>">Hapus</a></td>
                                </tr>
                                <?php $c++; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>