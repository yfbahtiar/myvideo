<?php
// query search video
$cari = $_GET['url'];

// cari video rekomendasi berdasarkan kategori
$videoPlay = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views JOIN videos WHERE videos.url = views.url_video AND videos.url = '$cari'")->fetch_assoc();
$kategoryURL = $videoPlay['kategori'];
$listVideo = mysqli_query($conn, "SELECT * FROM videos WHERE kategori = '$kategoryURL' AND url != '$cari'");
if (mysqli_num_rows($listVideo) < 1) {
    $rekomendasi = mysqli_query($conn, "SELECT * FROM videos WHERE url != '$cari' LIMIT 5");
} else {
    $rekomendasi = mysqli_query($conn, "SELECT * FROM videos WHERE kategori = '$kategoryURL' AND url != '$cari'  ORDER BY tanggal DESC LIMIT 5");
}

// cari video untuk diputar
$q = mysqli_query($conn, "SELECT videos.*,views. viewers FROM views JOIN videos WHERE videos.url = views.url_video AND videos.url = '$cari'")->fetch_assoc();

// cari total like video
$cariTotalLike = mysqli_query($conn, "SELECT video_like.url_video, SUM(video_like.video_like) AS total_like FROM video_like WHERE url_video = '$cari'")->fetch_assoc();
$totalLike = $cariTotalLike['total_like'];

// cari total dislike video
$cariTotalDislike = mysqli_query($conn, "SELECT video_dislike.url_video, SUM(video_dislike.video_dislike) AS total_dislike FROM video_dislike WHERE url_video = '$cari'")->fetch_assoc();
$totalDislike = $cariTotalDislike['total_dislike'];

// cari session untuk like, disslike
if (isset($_SESSION['login'])) {
    $session = $_SESSION['username'];
} else {
    $session = 'anonymous';
}

// muat komentar
$komen = mysqli_query($conn, "SELECT * FROM komentar WHERE url_video = '$cari'");

// cek apakah user udah like
$liked = mysqli_query($conn, "SELECT * FROM video_like WHERE url_video = '$cari' AND id_user = '$session'")->fetch_assoc();
if (!$liked) {
    // user belum like
    $userLiked = 'Suka';
} else {
    $userLiked = 'Batal Suka';
}

// cek apakah user udah dislike
$disliked = mysqli_query($conn, "SELECT * FROM video_dislike WHERE url_video = '$cari' AND id_user = '$session'")->fetch_assoc();
if (!$disliked) {
    // user belum dislike
    $userDisliked = 'Tidak Suka';
} else {
    $userDisliked = 'Batal Tidak Suka';
}
?>
<div class="row">
    <div class="col-md-9 mb-3">
        <?php if (!$q) : ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center p-5">404 Not Found</h3>
                </div>
            </div>
        <?php else : ?>
            <div class="card mb-3">
                <div class="card-body">
                    <video src="<?= base_url('assets/file/') . $q['target']; ?>" controls class="w-100" id="videoPlaying"></video>
                    <h5 class="font-weight-bold my-2"><?= $q['judul']; ?></h5>
                    <div class="my-3"><span class="badge badge-pill badge-info"><?= $q['kategori']; ?></span>&nbsp;<span>Dipublikasikan tanggal <?= date('d M Y', $q['tanggal']); ?></span>
                    </div>
                    <div class="my-3"><span class="font-weight-bold"><?= $q['id_user']; ?></span>&nbsp;<span><?= number_format($q['viewers']); ?> x ditonton</span></div>
                    <hr class="my-2">
                    <div class="text-center d-flex justify-content-center">
                        <span class="btn btn-success" id="video-like" data-toggle="tooltip" title="Klik untuk <?= $userLiked; ?>">
                            <span class="badge badge-light"><?= number_format($totalLike); ?></span>&nbsp;<?= $userLiked; ?></span>&nbsp;
                        <span class="btn btn-danger" id="video-dislike" data-toggle="tooltip" title="Klik untuk <?= $userDisliked; ?>">
                            <span class="badge badge-light"><?= number_format($totalDislike); ?></span>&nbsp;<?= $userDisliked; ?></span>
                    </div>
                    <hr class="my-2">
                    <p class="mb-2"><?= $q['keterangan']; ?></p>
                    <div class="card">
                        <div class="card-header">Komentar</div>
                        <div class="card-body">
                            <div class="card">
                                <?php if (mysqli_num_rows($komen) < 1) : ?>
                                    <div class="card-body">
                                        <h6 class="text-center p-1">Belum ada komentar</h6>
                                    </div>
                                <?php else : ?>
                                    <?php while ($k = mysqli_fetch_assoc($komen)) : ?>
                                        <div class="card-body">
                                            <div class="d-flex mb-2">
                                                <span class="font-weight-bold d-block"><?= $k['id_user']; ?></span>
                                                <small class="ml-2"><?= date('d M Y', $k['tanggal']); ?></small>
                                            </div>
                                            </>
                                            <?= $k['komentar']; ?>
                                            <?php if (isset($_SESSION['login'])) : ?>
                                                <?php if ($nameUsername == $k['id_user'] || $_SESSION['role_id'] == 1 || $_SESSION['username'] == $q['id_user']) : ?>
                                                    <div class="d-flex justify-content-end">
                                                        <a onclick="return confirm('Yakin mau hapus ini...?')" href="?page=watch&url=<?= $cari; ?>&delete-komen=<?php if (!$_SESSION['login']) {
                                                                                                                                                                    echo $session;
                                                                                                                                                                } else {
                                                                                                                                                                    echo $k['id_user'];
                                                                                                                                                                } ?>" class="badge badge-danger">Hapus</a>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <?php if (!isset($_SESSION['login'])) : ?>
                                <a href="login.php?rdr=<?= urlencode(base_url('index.php?page=watch&url=' . $cari)); ?>" class="btn btn-outline-warning w-50">Login</a>
                            <?php else : ?>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-secondary w-50 text-center" data-toggle="modal" data-target="#komentar">
                                    Beri Komentar
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="komentar" tabindex="-1" role="dialog" aria-labelledby="komentarLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="komentarLabel">Komentar</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="index.php" method="post">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <span class="font-weight-bold"><?= $q['judul']; ?></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="id_user" id="id_user" value="<?= $nameUsername; ?>">
                                                        <input type="hidden" name="url_video" id="url_video" value="<?= $cari; ?>">
                                                        <textarea name="isi_komen" id="isi_komen" class="form-control" autocomplete="off" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" name="komen">Kirim</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-3">
        <?php if (mysqli_num_rows($rekomendasi) < 1) : ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center p-5">404 Not Found</h3>
                </div>
            </div>
        <?php else : ?>
            <?php while ($r = mysqli_fetch_assoc($rekomendasi)) : ?>
                <div class="card mb-3">
                    <a href="?page=watch&url=<?= $r['url']; ?>" class="card-body">
                        <h5 class="font-weight-bold my-1"><?= $r['judul']; ?></h5>
                        <hr class="my-2">
                        <span class="font-weight-bold text-dark"><?= $r['id_user']; ?></span>&nbsp;<span class="badge badge-pill badge-info"><?= $r['kategori']; ?></span>
                        <hr class="my-2">
                        <span class="text-dark">Dipublikasikan tanggal <?= date('d M Y', $r['tanggal']); ?></span>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div> <!-- end row -->