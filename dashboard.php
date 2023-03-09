<!-- button tambah video, kategori, tutor admin -->
<div class="row mt-3">
    <?php if ($_SESSION['role_id'] == 1) : ?>
        <div class="col-md-4 mt-1">
            <button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#tambahVideo">
                Tambah Video
            </button>
        </div>
        <div class="col-md-4 mt-1">
            <button type="button" class="btn btn-secondary w-100" data-toggle="modal" data-target="#tambahKategori">
                Tambah Kategori
            </button>
        </div>
        <div class="col-md-4 mt-1">
            <button type="button" id="btn_tutor" class="btn btn-info w-100" data-toggle="modal" data-target="#tutorTambahVideo">
                Tutorial Tambah Video
            </button>
        </div>
    <?php else : ?>
        <!-- button tambah video, kategori, tutor member -->
        <div class="col-md-6 mt-1">
            <button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#tambahVideo">
                Tambah Video
            </button>
        </div>
        <div class="col-md-6 mt-1">
            <button type="button" id="btn_tutor" class="btn btn-info w-100" data-toggle="modal" data-target="#tutorTambahVideo">
                Tutorial Tambah Video
            </button>
        </div>
    <?php endif; ?>
</div>
<!-- end button tambah video, kategori, tutor -->
<!-- Modal tutorial-->
<div class="modal fade" id="tutorTambahVideo" tabindex="-1" role="dialog" aria-labelledby="tutorTambahVideoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tutorTambahVideoLabel">Tutorial Tambah Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body body-tutor" id="body_tutor">
                <!-- end of preloader -->
                <div class="container mt-0" id="container_tutor">
                    <p class="mb-1"><strong>MyVideo</strong> merupakan website yang digunakan untuk berbagi video. Pada dasarnya ini mirip seperti platform berbagi video YouTube, Vidio, Vimeo, NetFlix.</p>
                    <!-- start tutor -->
                    <ul>
                        <li>Klik <strong>Tambah Video</strong></li>
                        <li>Isi semua keterangan tentang video</li>
                        <li>Cari lokasi video berada, tidak mensupport drag n drop</li>
                        <li>Perhatikan maksimal upload size video</li>
                    </ul>
                    <!-- end tutor -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of modal tutorial -->
<!-- main -->
<?php if ($_SESSION['role_id'] == 1) :
    // buat grafik untuk admin
    $titleGrafik = $conn->query("SELECT judul FROM videos, views WHERE videos.id_video = views.id_video AND views.viewers !=0 ORDER BY views.viewers DESC LIMIT 3");
    $valueGrafik = $conn->query("SELECT viewers FROM videos, views WHERE videos.id_video = views.id_video AND views.viewers !=0 ORDER BY views.viewers DESC LIMIT 3");
?>
    <!-- Modal Tambah Kategori-->
    <div class="modal fade" id="tambahKategori" tabindex="-1" role="dialog" aria-labelledby="tambahKategoriLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
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
                        <button type="submit" class="btn btn-primary" name="add-kategori">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of modal tambah kategori -->
    <!-- card statistik -->
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
                            ?>
                            <h5><?= number_format(mysqli_num_rows($cariTotalAkun)); ?></h5>
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
                            $listVideo = mysqli_query($conn, "SELECT videos.*,views.viewers FROM views JOIN videos WHERE videos.id_video = views.id_video");
                            ?>
                            <h5 id="total_video"><?= number_format(mysqli_num_rows($listVideo)); ?></h5>
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
                            ?>
                            <h5 id="total_kategori"><?= number_format(mysqli_num_rows($cariTotalKategori)); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- tambahan grafik -->
        <div class="chart-container" style="margin-bottom: 20px;">
            <canvas id="chart"></canvas>
        </div>
        <!-- end of grafik -->
    </div>
    <!-- end of statistik -->
    <div class="row">
        <!-- list kategori kosong -->
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    Kategori Kosong
                </div>
                <div class="card-body">
                    <?php
                    $viewKategori = mysqli_query($conn, "SELECT categories.id, categories.judul FROM categories WHERE NOT EXISTS ( SELECT videos.kategori FROM videos WHERE categories.judul = videos.kategori)");
                    ?>
                    <table class="table table-striped table-hover table-responsive-sm" id="tbl_kategori_kosong">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <?php
                        $d = 1;
                        while ($ktg = mysqli_fetch_assoc($viewKategori)) : ?>
                            <tr id="ktg_id_<?= $ktg['id']; ?>">
                                <td><?= $d; ?></td>
                                <td><?= $ktg['judul']; ?></td>
                                <td>
                                    <a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" data-id="<?= $ktg['id']; ?>" style="cursor: pointer;" id="delete_kategori">Hapus</a>
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
        <!-- end of list kategori -->
        <!-- list kategori -->
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    Daftar Kategori
                </div>
                <div class="card-body">
                    <?php
                    $vieKategori = mysqli_query($conn, "SELECT kategori, COUNT(kategori) AS total FROM videos GROUP BY kategori");
                    ?>
                    <table class="table table-striped table-hover table-responsive-sm" id="tbl_kategori">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Video</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <?php
                        $d = 1;
                        while ($ktr = mysqli_fetch_assoc($vieKategori)) : ?>
                            <tr>
                                <td><?= $d; ?></td>
                                <td><?= $ktr['kategori']; ?></td>
                                <td><?= $ktr['total']; ?></td>
                                <td>
                                    <a class="badge badge-warning" href="?page=dashboard&edit-kategori=<?= $ktr['kategori']; ?>">Edit</a>
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
        <!-- end of list kategori -->
        <!-- list member -->
        <div class="col-md-12">
            <div class="card mt-3">
                <div class="card-header">
                    Daftar Akun
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-responsive-md" id="tbl_user">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Username</th>
                                <th scope="col">Fullname</th>
                                <th scope="col">Aktivitas</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // cari tabel user untuk ditampilkan di admin
                            $getUser = mysqli_query($conn, "SELECT users.id, users.username, users.fullname, users.role_id, users.birthDate, COUNT(videos.id_user) AS videos, SUM(views.viewers) AS viewers, SUM(video_like.video_like) AS likes, SUM(video_dislike.video_dislike) AS dislikes FROM users, videos, views, video_like, video_dislike WHERE users.username = videos.id_user AND videos.id_video = views.id_video AND videos.id_video = video_like.id_video AND videos.id_video = video_dislike.id_video GROUP BY users.username ORDER BY views.viewers DESC");
                            $c = 1;
                            while ($row = mysqli_fetch_assoc($getUser)) : ?>
                                <tr>
                                    <th scope="row"><?= $c; ?></th>
                                    <td>
                                        <p><?= $row['username']; ?></p>
                                        <span class="badge badge-pill badge-secondary d-block"><?php if ($row['role_id'] == 2) {
                                                                                                    echo 'Member';
                                                                                                } else {
                                                                                                    echo 'Administrator';
                                                                                                } ?>
                                        </span>
                                    </td>
                                    <td>
                                        <p><?= $row['fullname']; ?></p>
                                        <span class="badge badge-pill badge-light d-block"><?php if (cekUmur($row['birthDate']) <= 17) {
                                                                                                echo 'Bawah Umur (' . cekUmur($row['birthDate']) . ' thn)';
                                                                                            } else {
                                                                                                echo 'Dewasa (' . cekUmur($row['birthDate']) . ' thn)';
                                                                                            } ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">
                                            Videos <span class="badge badge-light"><?= number_format($row['videos']); ?></span>
                                            <span class="sr-only">Videos</span>
                                        </span>&nbsp;
                                        <span class="badge badge-info">
                                            Viewers <span class="badge badge-light"><?= number_format($row['viewers']); ?></span>
                                            <span class="sr-only">Viewers</span>
                                        </span>&nbsp;
                                        <span class="badge badge-success">
                                            Suka <span class="badge badge-light"><?= number_format($row['likes']); ?></span>
                                            <span class="sr-only">Suka</span>
                                        </span>&nbsp;
                                        <span class="badge badge-warning">
                                            Tidak Suka <span class="badge badge-light"><?= number_format($row['dislikes']); ?></span>
                                            <span class="sr-only">Tidak Suka</span>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['role_id'] == 2) : ?>
                                            <a onclick="return confirm('Yakin mau hapus ini...?')" class="badge badge-danger" href="?page=akun&delete-akun=<?= $row['id']; ?>">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $c++; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end of member -->
    </div> <!-- end row -->
    <!-- list video -->
    <div class="card mt-3">
        <div class="card-header">
            Daftar Video
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-responsive" id="tbl_video">
                <thead>
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Target</th>
                        <th scope="col">Tayangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b = 1;
                    while ($result = mysqli_fetch_assoc($listVideo)) : ?>
                        <tr id="video_id_<?= $result['id_video']; ?>">
                            <td><?= $b; ?></td>
                            <td><?= $result['judul']; ?></td>
                            <td>
                                <span class="badge badge-pill badge-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                                        <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                        <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                    </svg>&nbsp;
                                    <?= $result['id_user']; ?>
                                </span>
                                <span class="badge badge-pill badge-info"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                        <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                                        <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                                    </svg>&nbsp;
                                    <?= $result['kategori']; ?>
                                </span>
                                <span class="badge badge-pill badge-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16">
                                        <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                    </svg>&nbsp;
                                    <?= date('d', $result['tanggal']) . ' ' . month(date('n', $result['tanggal'])) . ' ' . date('Y', $result['tanggal']); ?>
                                </span>
                                <?php if ($result['umur'] == 0) : ?>
                                    <span class="badge badge-pill badge-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>&nbsp;Video Aman Untuk Anak
                                    </span>
                                <?php else : ?>
                                    <span class="badge badge-pill badge-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>&nbsp;Batasan Umur Berlaku
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= $result['target']; ?></td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                </svg>
                                <span class="font-weigh-normal"><?= number_format($result['viewers']); ?> x</span>
                            </td>
                            <td>
                                <a class="badge badge-warning" href="?page=edit&v=<?= $result['id_video']; ?>">Edit</a>
                                <a onclick="return confirm('Yakin mau hapus ini...?')" data-delete="<?= $result['id_video']; ?>" data-id="<?= $result['id_video']; ?>" class="badge badge-danger" id="delete_video" style="cursor: pointer;">Hapus</a>
                                <a class="badge badge-success" href="index.php?page=watch&v=<?= $result['id_video']; ?>">Lihat</a>
                            </td>
                        </tr>
                        <?php $b++; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div> <!-- end card-->
<?php else :
    // buat grafik untuk user session
    $titleGrafik = $conn->query("SELECT judul FROM videos, views WHERE (videos.id_video = views.id_video AND videos.id_user = '$session') AND views.viewers !=0 ORDER BY views.viewers DESC LIMIT 3");
    $valueGrafik = $conn->query("SELECT viewers FROM videos, views WHERE (videos.id_video = views.id_video AND videos.id_user = '$session') AND views.viewers !=0 ORDER BY views.viewers DESC LIMIT 3");
?>
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
                            // list video
                            $listVideo = mysqli_query($conn, "SELECT videos.*, views.viewers, SUM(video_like.video_like) AS total_like, SUM(video_dislike.video_dislike) AS total_dislike FROM views, videos, video_like, video_dislike WHERE videos.id_video = views.id_video AND videos.id_video = video_like.id_video AND videos.id_video = video_dislike.id_video AND videos.id_user = '$session' GROUP BY videos.id_video ORDER BY videos.tanggal ASC");
                            // hitung jumlah video user
                            $totalVideo = mysqli_num_rows($listVideo);
                            ?>
                            <h5 id="total_video"><?= number_format($totalVideo); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-header">
                            Viewers
                        </div>
                        <div class="card-body text-center">
                            <?php
                            if ($totalVideo == 0) {
                                $totalViewers = 0;
                            } else {
                                $getHitungView = mysqli_query($conn, "SELECT SUM(views.viewers) AS total_viewers FROM videos, views WHERE videos.id_video = views.id_video AND videos.id_user = '$session'")->fetch_assoc();
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
                            if ($totalVideo == 0) {
                                $totalVideoLike = 0;
                            } else {
                                $getHitungLike = mysqli_query($conn, "SELECT SUM(video_like.video_like) AS total_likes FROM videos, video_like WHERE videos.id_video = video_like.id_video AND videos.id_user = '$session'")->fetch_assoc();
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
                            if ($totalVideo == 0) {
                                $totalVideoDislike = 0;
                            } else {
                                $getHitungDislike = mysqli_query($conn, "SELECT SUM(video_dislike.video_dislike) AS total_dislikes FROM videos, video_dislike WHERE videos.id_video = video_dislike.id_video AND videos.id_user = '$session'")->fetch_assoc();
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
        <div class="chart-container" style="margin-bottom: 20px;">
            <canvas id="chart"></canvas>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            Video Anda
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-responsive" id="example">
                <thead>
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Tayangan</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Date</th>
                        <th scope="col">Reaction</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b = 1;
                    while ($result = mysqli_fetch_assoc($listVideo)) : ?>
                        <tr id="video_id_<?= $result['id_video']; ?>">
                            <td><?= $b; ?></td>
                            <td><?= $result['judul']; ?></td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                </svg>
                                <span class="font-weigh-normal"><?= number_format($result['viewers']); ?> x</span>
                            </td>
                            <td>
                                <span class="badge badge-pill badge-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                        <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                                        <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                                    </svg>&nbsp;
                                    <?= $result['kategori']; ?>
                                </span>
                                <?php if ($result['umur'] == 0) : ?>
                                    <span class="badge badge-pill badge-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>&nbsp;Video Aman Untuk Anak
                                    </span>
                                <?php else : ?>
                                    <span class="badge badge-pill badge-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>&nbsp;Batasan Umur Berlaku
                                    </span>
                                <?php endif; ?>
                                </span>
                            </td>
                            <td><span class=" d-block"><?= date('d', $result['tanggal']) . ' ' . month(date('n', $result['tanggal'])) . ' ' . date('Y', $result['tanggal']); ?></span></td>
                            <td>
                                <span class="badge badge-primary">
                                    Suka <span class="badge badge-light"><?= number_format($result['total_like']); ?></span>
                                    <span class="sr-only">Suka</span>
                                </span>&nbsp;
                                <span class="badge badge-secondary">
                                    Tidak Suka <span class="badge badge-light"><?= number_format($result['total_dislike']); ?></span>
                                    <span class="sr-only">Tidak Suka</span>
                                </span>
                            </td>
                            <td>
                                <a class="badge badge-warning" href="?page=edit&v=<?= $result['id_video']; ?>">Edit</a>
                                <a onclick="return confirm('Yakin mau hapus ini...?')" data-delete="<?= $result['id_video']; ?>" data-id="<?= $result['id_video']; ?>" class="badge badge-danger" id="delete_video" style="cursor: pointer;">Hapus</a>
                                <a class="badge badge-success" href="index.php?page=watch&v=<?= $result['id_video']; ?>">Lihat</a>
                            </td>
                        </tr>
                        <?php $b++; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div> <!-- end card-->
<?php endif; ?>