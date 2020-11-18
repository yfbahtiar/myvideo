<?php
// query search video
if (isset($_GET['q'])) :
    $cari = htmlspecialchars($_GET['q'], true);

    // pagination
    $p = (isset($_GET['p'])) ? (int) $_GET['p'] : 1;

    // Jumlah data per halaman
    $limit = 5;
    $limitStart = ($p - 1) * $limit;
    $query = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%') AND videos.url = views.url_video ORDER BY views.viewers DESC LIMIT $limitStart, $limit");
    $no = $limitStart + 1;
?>
    <div class="row">
        <div class="col">
            <?php if (mysqli_num_rows($query) < 1) : ?>
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center p-5">404 Not Found</h3>
                    </div>
                </div>
            <?php else : ?>
                <?php while ($q = mysqli_fetch_assoc($query)) : ?>
                    <div class="card mb-3">
                        <a href="index.php?page=watch&url=<?= $q['url']; ?>" class="card-body">
                            <h5 class="font-weight-bold my-1"><?= $q['judul']; ?></h5>
                            <hr class="my-2">
                            <div class="my-3 text-dark"><span class="badge badge-success"><?= $q['kategori']; ?></span>&nbsp;<span>Dipublikasikan tanggal <?= date('d M Y', $q['tanggal']); ?></span>
                            </div>
                            <div class="d-inline">
                                <span class="btn btn-primary mb-2">
                                    <span class="badge badge-light"><?= number_format($q['viewers']); ?></span>&nbsp;x ditonton
                                </span>&nbsp;
                                <span class="btn btn-secondary mb-2">
                                    &nbsp;<?= $q['id_user']; ?>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div> <!-- end row -->
    <nav aria-label="pagination for search" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php
            // Jika page = 1, maka LinkPrev disable
            if ($p == 1) {
            ?>
                <!-- link Previous Page disable -->
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
            <?php
            } else {
                $LinkPrev = ($p > 1) ? $p - 1 : 1;
            ?>
                <!-- link Previous Page -->
                <li class="page-item"><a href="?q=<?= $cari; ?>&p=<?= $LinkPrev; ?>" class="page-link">Previous</a></li>
            <?php
            }
            ?>

            <?php
            $SqlQuery = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%') AND videos.url = views.url_video");

            //Hitung semua jumlah data yang berada pada tabel
            $JumlahData = mysqli_num_rows($SqlQuery);

            // Hitung jumlah halaman yang tersedia
            $jumlahPage = ceil($JumlahData / $limit);

            // Jumlah link number 
            $jumlahNumber = 1;

            // Untuk awal link number
            $startNumber = ($p > $jumlahNumber) ? $p - $jumlahNumber : 1;

            // Untuk akhir link number
            $endNumber = ($p < ($jumlahPage - $jumlahNumber)) ? $p + $jumlahNumber : $jumlahPage;

            for ($i = $startNumber; $i <= $endNumber; $i++) {
                $linkActive = ($p == $i) ? ' class="page-item active"' : '';
            ?>
                <li<?= $linkActive; ?>><a class="page-link" href="?q=<?= $cari; ?>&p=<?= $i; ?>"><?= $i; ?></a></li>
                <?php
            }
                ?>

                <!-- link Next Page -->
                <?php
                if ($p == $jumlahPage) {
                ?>
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                <?php
                } else {
                    $linkNext = ($p < $jumlahPage) ? $p + 1 : $jumlahPage;
                ?>
                    <li class="page-item"><a href="?q=<?= $cari; ?>&p=<?= $linkNext; ?>" class="page-link">Next</a></li>
                <?php
                }
                ?>
        </ul>
        </div>
        <div class="text-center mb-3 text-dark" style="margin-top: -15px;">
            Ditemukan <strong><?= number_format($JumlahData); ?></strong> video
        </div>
    <?php else : ?>
        <?php
        // query load video
        $query = mysqli_query($conn, "SELECT videos.*, views.viewers, SUM(video_like.video_like) AS total_like, SUM(video_dislike.video_dislike) AS total_dislike FROM views, videos, video_like, video_dislike WHERE videos.url = views.url_video AND videos.url = video_like.url_video AND videos.url = video_dislike.url_video GROUP BY videos.url ORDER BY views.viewers DESC LIMIT 5");
        ?>
        <div class="row">
            <div class="col">
                <?php if (mysqli_num_rows($query) < 1) : ?>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-center p-5">404 Not Found</h3>
                        </div>
                    </div>
                <?php else : ?>
                    <?php while ($q = mysqli_fetch_assoc($query)) : ?>
                        <div class="card mb-3">
                            <a href="index.php?page=watch&url=<?= $q['url']; ?>" class="card-body">
                                <h5 class="font-weight-bold my-1"><?= $q['judul']; ?></h5>
                                <hr class="my-2">
                                <div class="my-2 text-dark"><span class="badge badge-success"><?= $q['kategori']; ?></span>&nbsp;<span>Dipublikasikan tanggal <?= date('d M Y', $q['tanggal']); ?></span>
                                </div>
                                <div class="d-inline-block">
                                    <span class="btn btn-info mb-2">
                                        <span class="badge badge-light"><?= number_format($q['total_like']); ?></span>&nbsp;Suka
                                    </span>&nbsp;
                                    <span class="btn btn-warning mb-2">
                                        <span class="badge badge-light"><?= number_format($q['total_dislike']); ?></span>
                                        &nbsp;Tidak Suka
                                    </span>&nbsp;
                                    <span class="btn btn-primary mb-2">
                                        <span class="badge badge-light"><?= number_format($q['viewers']); ?></span>
                                        &nbsp;x ditonton
                                    </span>&nbsp;
                                    <span class="btn btn-secondary mb-2">
                                        <span class="badge badge-light"></span>
                                        &nbsp;<?= $q['id_user']; ?>
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div> <!-- end row -->
    <?php endif; ?>