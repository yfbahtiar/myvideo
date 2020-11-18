<!-- Button trigger modal -->
<button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#tambahVideo">
    Tambah Video
</button>

<?php if ($_SESSION['role_id'] == 1) : ?>
    <?php
    if (isset($_POST['add-kategori'])) {
        $jdl_kategori = htmlspecialchars($_POST['jdl_kategori'], true);
        mysqli_query($conn, "INSERT INTO categories VALUES ('', '$jdl_kategori')");
        $_SESSION['pesan'] = 'Kategori berhasil ditambahkan';
        // di website nya pake js
        //echo "<script>window.location.href = 'login.php';</script>";
        header("Location: user.php?page=dashboard");
        exit;
    }
    ?>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary w-100 mt-3" data-toggle="modal" data-target="#tambahKategori">
        Tambah Kategori
    </button>

    <!-- Modal -->
    <div class="modal fade" id="tambahKategori" tabindex="-1" role="dialog" aria-labelledby="tambahKategoriLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahKategoriLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="jdl_kategori">Judul Kategori</label>
                            <input type="text" name="jdl_kategori" id="jdl_kategori" class="form-control" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add-kategori">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            Statistik
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            Akun
                        </div>
                        <div class="card-body text-center">
                            <?php
                            $cariTotalAkun = mysqli_query($conn, "SELECT * FROM users");
                            $totalAkun = mysqli_num_rows($cariTotalAkun);
                            ?>
                            <h5><?= number_format($totalAkun); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            Video
                        </div>
                        <div class="card-body text-center">
                            <?php
                            // list video
                            $listVideo = mysqli_query($conn, "SELECT videos.*,views.viewers FROM views JOIN videos WHERE videos.url = views.url_video");
                            // hitung jumlah video
                            $totalVideo = mysqli_num_rows($listVideo);
                            ?>
                            <h5><?= number_format($totalVideo); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            Kategori
                        </div>
                        <div class="card-body text-center">
                            <?php
                            $cariTotalKategori = mysqli_query($conn, "SELECT * FROM categories");
                            $totalKategori = mysqli_num_rows($cariTotalKategori);
                            ?>
                            <h5><?= number_format($totalKategori); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    Daftar Kategori
                </div>
                <div class="card-body">
                    <?php
                    $viewKategori = mysqli_query($conn, "SELECT * FROM categories");
                    ?>
                    <table class="table table-striped table-hover table-custom w-100 table-responsive" id="example1">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <?php
                        $d = 1;
                        while ($ktg = mysqli_fetch_assoc($viewKategori)) : ?>
                            <tr>
                                <td><?= $d; ?></td>
                                <td><?= $ktg['judul']; ?></td>
                                <td>
                                    <a class="badge badge-warning" href="?page=dashboard&edit-kategori=<?= $ktg['id']; ?>">Edit</a>
                                    <a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" href="?page=dashboard&delete-kategori=<?= $ktg['id']; ?>">Hapus</a>
                                </td>
                            </tr>
                            <?php $d++; ?>
                        <?php endwhile; ?>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            Daftar Video
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-custom w-100 table-responsive" id="example">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Pemilik</th>
                        <th scope="col">Target</th>
                        <th scope="col">Tayangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b = 1;
                    while ($result = mysqli_fetch_assoc($listVideo)) : ?>
                        <tr>
                            <td><?= $b; ?></td>
                            <td><?= $result['judul']; ?></td>
                            <td>
                                <span class="d-block mb-2"><?= $result['keterangan']; ?></span>
                                <span class="badge badge-pill badge-info"><?= $result['kategori']; ?></span>
                                <span class="badge badge-pill badge-secondary"><?= date('d M Y', $result['tanggal']); ?></span>
                            </td>
                            <td><?= $result['id_user']; ?></td>
                            <td><?= $result['target']; ?></td>
                            <td>
                                <span class="font-weigh-normal"><?= number_format($result['viewers']); ?> x ditonton</span>
                            </td>
                            <td>
                                <a class="badge badge-warning" href="?page=edit&video=<?= $result['url']; ?>">Edit</a>
                                <a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" href="?page=dashboard&delete-video=<?= $result['url']; ?>">Hapus</a>
                                <a class="badge badge-success" href="index.php?page=watch&url=<?= $result['url']; ?>">Lihat</a>
                            </td>
                        </tr>
                        <?php $b++; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div> <!-- end card-->
<?php else : ?>
    <div class="card mt-3">
        <div class="card-header">
            Statistik Akun
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-header">
                            Videos
                        </div>
                        <div class="card-body text-center">
                            <?php
                            $session = $_SESSION['username'];
                            // list video
                            $listVideo = mysqli_query($conn, "SELECT videos.*, views.viewers, SUM(video_like.video_like) AS total_like, SUM(video_dislike.video_dislike) AS total_dislike FROM views, videos, video_like, video_dislike WHERE videos.url = views.url_video AND videos.url = video_like.url_video AND videos.url = video_dislike.url_video AND videos.id_user = '$session' GROUP BY videos.url ORDER BY videos.tanggal ASC");
                            // hitung jumlah video user
                            $videoUser = mysqli_num_rows($listVideo);
                            ?>
                            <h5><?= number_format($videoUser); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-header">
                            Viewer
                        </div>
                        <div class="card-body text-center">
                            <?php
                            if ($videoUser == 0) {
                                $totalViewers = 0;
                            } else {
                                $getHitungView = mysqli_query($conn, "SELECT videos.id_user, SUM(views.viewers) AS total_viewers FROM videos, views WHERE videos.url = views.url_video AND videos.id_user = '$session'")->fetch_assoc();
                                $totalViewers = $getHitungView['total_viewers'];
                            }
                            ?>
                            <h5><?= number_format($totalViewers); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-header">
                            Likers
                        </div>
                        <div class="card-body text-center">
                            <?php
                            if ($videoUser == 0) {
                                $totalVideoLike = 0;
                            } else {
                                $getHitungLike = mysqli_query($conn, "SELECT videos.id_user, SUM(video_like.video_like) AS total_likes FROM videos, video_like WHERE videos.url = video_like.url_video AND videos.id_user = '$session'")->fetch_assoc();
                                if (!$getHitungLike['total_likes']) {
                                    $totalVideoLike = 0;
                                } else {
                                    $totalVideoLike = $getHitungLike['total_likes'];
                                }
                            }
                            ?>
                            <h5><?= number_format($totalVideoLike); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            Dislikers
                        </div>
                        <div class="card-body text-center">
                            <?php
                            if ($videoUser == 0) {
                                $totalVideoDislike = 0;
                            } else {
                                $getHitungDislike = mysqli_query($conn, "SELECT videos.id_user, SUM(video_dislike.video_dislike) AS total_dislikes FROM videos, video_dislike WHERE videos.url = video_dislike.url_video AND videos.id_user = '$session'")->fetch_assoc();
                                if (!$getHitungDislike['total_dislikes']) {
                                    $totalVideoDislike = 0;
                                } else {
                                    $totalVideoDislike = $getHitungDislike['total_dislikes'];
                                }
                            }
                            ?>
                            <h5><?= number_format($totalVideoDislike); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            Video Anda
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-custom w-100 table-responsive" id="example">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Caption</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Date</th>
                        <th scope="col">Tayangan</th>
                        <th scope="col">Reaction</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b = 1;
                    while ($result = mysqli_fetch_assoc($listVideo)) : ?>
                        <tr>
                            <td><?= $b; ?></td>
                            <td><?= $result['judul']; ?></td>
                            <td><?= $result['keterangan']; ?></td>
                            <td><span class="badge badge-pill badge-info"><?= $result['kategori']; ?></span></td>
                            <td><span class=" d-block"><?= date('d M Y', $result['tanggal']); ?></span></td>
                            <td><span class="font-weigh-normal"><?= number_format($result['viewers']); ?> x ditonton</span></td>
                            <td class="d-flex justify-content-center">
                                <span type="button" class="btn btn-primary">
                                    Suka <span class="badge badge-light"><?= number_format($result['total_like']); ?></span>
                                    <span class="sr-only">Suka</span>
                                </span>&nbsp;
                                <span type="button" class="btn btn-secondary">
                                    Tidak Suka <span class="badge badge-light"><?= number_format($result['total_dislike']); ?></span>
                                    <span class="sr-only">Tidak Suka</span>
                                </span>
                            </td>
                            <td>
                                <a class="badge badge-warning" href="?page=edit&video=<?= $result['url']; ?>">Edit</a>
                                <a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" href="?page=dashboard&delete-video=<?= $result['url']; ?>">Hapus</a>
                                <a class="badge badge-success" href="index.php?page=watch&url=<?= $result['url']; ?>">Lihat</a>
                            </td>
                        </tr>
                        <?php $b++; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div> <!-- end card-->
<?php endif; ?>
