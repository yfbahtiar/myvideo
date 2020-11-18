<?php
$url = $_GET['video'];
$row = mysqli_query($conn, "SELECT * FROM videos WHERE url = '$url'")->fetch_assoc();

if (isset($_POST['edit'])) {
    $judul = htmlspecialchars($_POST['judul'], true);
    $kategori = $_POST['kategori'];
    $target = htmlspecialchars($_POST['target'], true);
    $keterangan = htmlspecialchars($_POST['keterangan'], true);

    // update data
    mysqli_query($conn, "UPDATE videos SET judul = '$judul', kategori = '$kategori', target = '$target', keterangan = '$keterangan' WHERE url = '$url'");

    $_SESSION['pesan'] = 'Video berhasil update';
    // di website nya pake js
    //echo "<script>window.location.href = 'login.php';</script>";
    header("Location: user.php");
    exit;
}
?>
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                Edit Video
            </div>
            <form action="" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul" class="col-form-label">Judul</label>
                        <input type="text" class="form-control " id="judul" name="judul" value="<?= $row['judul']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="target" class="col-form-label">Target File</label>
                        <input type="text" class="form-control " id="target" name="target" value="<?= $row['target']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="col-form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= $row['keterangan']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="kategori" class="col-form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-control">
                            <?php while ($kategori = mysqli_fetch_assoc($dataKategori)) : ?>
                                <option value="<?= $kategori['judul']; ?>" <?php if ($row['kategori'] == $kategori['judul']) {
                                                                                echo 'selected';
                                                                            } ?>><?= $kategori['judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                    <a href="?page=dashboard" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6 my-auto">
        <div class="card">
            <div class="card-body">
                <video src="<?= base_url('assets/file/') . $row['target']; ?>" class="w-100" <?php if ($_SESSION['role_id'] == 1) {
                                                                                                    echo 'controls';
                                                                                                } ?>></video>
            </div>
        </div>
    </div>
</div>