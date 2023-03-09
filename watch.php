<?php

// query search video
$cari = $_GET['v'];

// utuk video dg batasan umur
$umurUser = (isset($_SESSION['umur'])) ? (int) $_SESSION['umur'] : 0;

// cari video untuk diputar
$q = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views JOIN videos WHERE videos.id_video = views.id_video AND videos.id_video = '$cari'")->fetch_assoc();

// cari total like video
$cariTotalLike = mysqli_query($conn, "SELECT video_like.id_video, SUM(video_like.video_like) AS total_like FROM video_like WHERE id_video = '$cari'")->fetch_assoc();
$totalLike = $cariTotalLike['total_like'];

// cari total dislike video
$cariTotalDislike = mysqli_query($conn, "SELECT video_dislike.id_video, SUM(video_dislike.video_dislike) AS total_dislike FROM video_dislike WHERE id_video = '$cari'")->fetch_assoc();
$totalDislike = $cariTotalDislike['total_dislike'];

// cari session untuk like, disslike
if (isset($_SESSION['myvideo'])) {
    $session = $_SESSION['username'];
} else {
    $session = 'anonymous';
}

// muat komentar
$komen = mysqli_query($conn, "SELECT * FROM komentar WHERE id_video = '$cari'");

// cek apakah user udah like
$liked = mysqli_query($conn, "SELECT * FROM video_like WHERE id_video = '$cari' AND id_user = '$session'")->fetch_assoc();
if (!$liked) {
    // user belum like
    $userLiked = 'Suka';
} else {
    $userLiked = 'Batal Suka';
}

// cek apakah user udah dislike
$disliked = mysqli_query($conn, "SELECT * FROM video_dislike WHERE id_video = '$cari' AND id_user = '$session'")->fetch_assoc();
if (!$disliked) {
    // user belum dislike
    $userDisliked = 'Tidak Suka';
} else {
    $userDisliked = 'Batal Tidak Suka';
}

// untuk load video berdasarakna
$testKategori = $conn->query("SELECT videos.no, videos.id_video, videos.id_user, videos.judul, videos.tanggal, views.viewers FROM videos, views WHERE videos.id_video != '$cari' AND videos.id_video = views.id_video AND videos.kategori = '" . $q['kategori'] . "' GROUP BY videos.id_video");
$jmlVideo = mysqli_num_rows($testKategori);
// prepare acak
$arr = ['views.viewers', 'videos.no', 'videos.tanggal'];
$arr1 = ['ASC', 'DESC'];
$ack = rand(0, 2);
$ack1 = rand(0, 1);
$orderBy = $arr[$ack];
$ascDesc = $arr1[$ack1];
if ($jmlVideo < 1) {
    $loadWatch = $conn->query("SELECT no, videos.id_video, videos.id_user, videos.judul, videos.tanggal, views.viewers FROM videos, views WHERE videos.id_video != '$cari' AND videos.id_video = views.id_video GROUP BY videos.id_video ORDER BY $orderBy $ascDesc LIMIT 5");
    // ORDER BY videos.no ASC 
    $kategoriNext = 'All';
} else {
    $loadWatch = $testKategori = $conn->query("SELECT videos.no, videos.id_video, videos.id_user, videos.judul, videos.tanggal, views.viewers FROM videos, views WHERE videos.id_video != '$cari' AND videos.id_video = views.id_video AND videos.kategori = '" . $q['kategori'] . "' GROUP BY videos.id_video ORDER BY $orderBy $ascDesc LIMIT 5");
    // ORDER BY videos.no ASC 
    $kategoriNext =  $q['kategori'];
}
$no_video = '';
$nexTitle = array();
$nexUrl = array();
?>
<div class="row">
    <div class="col-md-9 mb-3">
        <?php if (!$q) : ?>
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center p-5">404 Not Found</h3>
                </div>
            </div>
        <?php elseif ($q['umur'] == 1 && ($umurUser < 17 || !isset($_SESSION['username']))) : ?>
            <div class="card" id="fbd">
                <div class="card-body my-5">
                    <div class="row">
                        <div class="col-md-1 mb-3 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-exclamation-octagon text-muted" viewBox="0 0 16 16">
                                <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353L4.54.146zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1H5.1z" />
                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                        </div>
                        <div class="col-md-11 p2">
                            <?php if (!isset($_SESSION['username'])) : ?>
                                <span>Video ini tidak untuk konsumsi publik, silahkan login dulu.</span>
                            <?php else : ?>
                                <span>Maaf, Anda tidak bisa memutar video ini.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="watch mb-3">
                <!-- <div id="klik"></div> -->
                <video class="w-100 video-js vjs-big-play-centered" id="videoPlaying" playsinline="playsinline" muted="muted" preload="yes" autoplay="autoplay" data-setup='{"autoplay":"any"}'>
                    <source src="<?= base_url('assets/file/') . $q['target']; ?>">
                </video>
                <h5 class="font-weight-bold my-2"><?= $q['judul']; ?></h5>
                <div class="my-3">
                    <a href="index.php?q=<?= $q['kategori']; ?>" class="badge badge-pill badge-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                            <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                            <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                        </svg>&nbsp;
                        <?= $q['kategori']; ?>
                    </a>&nbsp;
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="20" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16">
                            <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                        </svg>
                        <?= date('d', $q['tanggal']) . ' ' . month(date('n', $q['tanggal']), 'mmm') . ' ' . date('Y', $q['tanggal']); ?>
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="my-3">
                            <a href="index.php?q=<?= $q['id_user']; ?>" class="font-weight-bold" style="text-decoration: none;"><?= $q['id_user']; ?></a>&nbsp;
                            <span><?= number_format($q['viewers']); ?> x ditonton</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="text-center btnLikeShare">
                            <span class="btn btn-success my-1" id="video-like" data-toggle="tooltip" title="Klik untuk <?= $userLiked; ?>">
                                <span class="badge badge-light"><?= number_format($totalLike); ?></span>&nbsp;<?= $userLiked; ?>
                            </span>&nbsp;
                            <span class="btn btn-danger my-1" id="video-dislike" data-toggle="tooltip" title="Klik untuk <?= $userDisliked; ?>">
                                <span class="badge badge-light"><?= number_format($totalDislike); ?></span>&nbsp;<?= $userDisliked; ?>
                            </span>&nbsp;
                            <span class="dropdownShare">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownShare" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Share
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownShare">
                                    <input type="text" class="form-control disabled" id="url_video" value="<?= base_url('index.php?page=watch&v=') . $q['id_video']; ?>" style="border-top-left-radius: .25rem !important;" readonly>
                                    <a class=" dropdown-item" onclick="copyFunc()" style="cursor: pointer;">Copy Link</a>
                                    <a class="dropdown-item" href="https://api.whatsapp.com/send?text=<?= base_url('index.php?page=watch&v=') . $q['id_video']; ?>">WhatsApp</a>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="mb-3" style="overflow-x: hidden; padding: 5px; display: block;">
                    <?= $q['keterangan']; ?>
                </div>
                <div class="card-header" id="komen_header">(<?= mysqli_num_rows($komen); ?>) Komentar</div>
                <div class="card-body" id="card_komen">
                    <?php if (mysqli_num_rows($komen) === 0) : ?>
                        <h6 class="text-center p-1" id="blm_ada_komen">Belum ada komentar</h6>
                    <?php else : ?>
                        <?php while ($k = mysqli_fetch_assoc($komen)) : ?>
                            <div id="komen_id_<?= $k['id']; ?>">
                                <div class="d-flex mb-2">
                                    <span class="font-weight-bold d-block"><?= $k['id_user']; ?></span>
                                    <small class="ml-2"><?= date('d', $k['tanggal']) . ' ' . month(date('n', $k['tanggal']), 'mmm') . ' ' . date('Y', $k['tanggal']); ?></small>
                                </div>
                                <?= $k['komentar']; ?>
                                <?php if (isset($_SESSION['myvideo'])) : ?>
                                    <?php if ($nameUsername === $k['id_user'] || $_SESSION['role_id'] === 1 || $_SESSION['username'] === $q['id_user']) : ?>
                                        <div class="d-flex justify-content-end">
                                            <span onclick="return confirm('Yakin mau hapus ini...?')" data-id="<?= $k['id']; ?>" data-url="<?= $cari; ?>" data-user="<?= $k['id_user']; ?>" data-komen="<?= $k['komentar']; ?>" id="delete_komen" class="badge badge-danger" style="cursor: pointer;">Hapus</span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <hr class="my-2">
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <?php if (!isset($_SESSION['myvideo'])) : ?>
                        <a href="login.php?rdr=<?= urlencode(base_url('index.php?page=watch&v=' . $cari)); ?>" class="btn btn-outline-warning w-50">Login</a>
                    <?php else : ?>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-secondary w-50 text-center" id="btn_komen" data-toggle="modal" data-target="#komentar">
                            Beri Komentar
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="komentar" tabindex="-1" role="dialog" aria-labelledby="komentarLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="komentarLabel">Komentar</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="index.php" method="post" id="post_komentar">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <span class="font-weight-bold"><?= $q['judul']; ?></span>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="id_user" id="id_user_komen" value="<?= $nameUsername; ?>">
                                                <input type="hidden" name="id_video" id="id_video_komen" value="<?= $cari; ?>">
                                                <input type="hidden" name="tanggal" id="tgl_komen" value="<?= time(); ?>">
                                                <textarea name="isi_komen" id="isi_komen" class="form-control" autocomplete="off" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" name="komen">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- kolom videoterkait -->
    <?php if ($q['umur'] != 1 || ($umurUser > 17 || isset($_SESSION['username']))) :  ?>
        <div class="col-md-3" id="next">
            <?php while ($wn = mysqli_fetch_assoc($loadWatch)) :
                $no_video = $wn['no'];
                $nexTitle[] = $wn['judul'];
                $nexUrl[] = base_url('index.php?page=watch&v=') . $wn['id_video'];
            ?>
                <a href="<?= base_url('index.php?page=watch&v=') . $wn['id_video']; ?>">
                    <div class="card mb-3">
                        <div class="card-header"><?= $wn['judul']; ?></div>
                        <div class="card-body text-center">
                            <span class="badge badge-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                </svg>&nbsp;
                                <?= $wn['id_user']; ?>
                            </span>&nbsp;
                            <span class="badge badge-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                </svg>&nbsp;
                                <?= $wn['viewers']; ?>&nbsp;
                            </span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
            <?php if ($jmlVideo > 5) : ?>
                <button class="btn btn-success w-100" data-lastno="<?= $no_video; ?>" data-kategori="<?= $kategoriNext; ?>" data-play="<?= $q['id_video']; ?>" id="loadMore">Load More</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div> <!-- end row -->