<?php
$id_video = $_GET['v'];
$row = mysqli_query($conn, "SELECT * FROM videos WHERE id_video = '$id_video'")->fetch_assoc();
?>
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                Edit Video
            </div>
            <form action="" method="post" id="updateVid">
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul" class="col-form-label">Judul</label>
                        <input type="text" class="form-control " id="judul" name="judul" value="<?= $row['judul']; ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label d-block">Batasan Umur Video?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="umur" id="umur0" value="0" <?php if ($row['umur'] == 0) {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
                            <label class="form-check-label" for="umur0">Tidak, Dapat ditonton anak.</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="umur" id="umur1" value="1" <?php if ($row['umur'] == 1) {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
                            <label class="form-check-label" for="umur1">Ya, Anak tidak dapat menonton.</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kategori" class="col-form-label">Kategori</label>
                        <input type="text" name="kategori" id="kategori" class="form-control" list="kategoriList" value="<?= $row['kategori']; ?>">
                        <datalist id="kategoriList">
                            <?php while ($kategori = mysqli_fetch_assoc($dataKategori)) : ?>
                                <option value="<?= $kategori['judul']; ?>"><?= $kategori['judul']; ?></option>
                            <?php endwhile; ?>
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="col-form-label">Keterangan</label>
                        <textarea id="keterangan"><?= $row['keterangan']; ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="edit" value="edit video">
                    <input type="hidden" name="id_video" value="<?= $id_video; ?>">
                    <button type="submit" class="btn btn-primary">Edit</button>
                    <a href="?page=dashboard" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6 my-auto">
        <div class="card">
            <div class="card-body">
                <video class="w-100 video-js" id="videoPlayer">
                    <source src="<?= base_url('assets/file/') . $row['target']; ?>">
                    <p class=" vjs-no-js">
                        Untuk melihat video ini harap menyalakan JavaScript.
                    </p>
                </video>
            </div>
        </div>
    </div>
</div>