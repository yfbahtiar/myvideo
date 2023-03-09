<?php
// query search video
if (isset($_GET['q'])) :
    $cari = htmlspecialchars($_GET['q'], true);
    // pagination
    $p = (isset($_GET['p'])) ? (int) $_GET['p'] : 1;
    // Jumlah data per halaman
    $limit = 10;
    $limitStart = ($p - 1) * $limit;
    $query = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%' OR videos.id_user LIKE '%$cari%' OR videos.id_video LIKE '%$cari%') AND videos.id_video = views.id_video ORDER BY views.viewers DESC LIMIT $limitStart, $limit");
    $no = $limitStart + 1;
?>

    <?php if (mysqli_num_rows($query) < 1) : ?>

        <!-- search result null -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-center p-5">404 Not Found</h3>
            </div>
        </div> <!-- end search result null -->

    <?php else : ?>

        <!-- search result -->
        <?php while ($q = mysqli_fetch_assoc($query)) : ?>
            <div class="card mb-3">
                <a href="index.php?page=watch&v=<?= $q['id_video']; ?>" class="card-body">
                    <h5 class="font-weight-bold my-1"><?= $q['judul']; ?></h5>
                    <hr class="my-2">
                    <div class="my-3 text-dark">
                        <span class="badge badge-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                                <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                            </svg>&nbsp;
                            <?= $q['kategori']; ?>
                        </span>&nbsp;
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16">
                                <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                            </svg>&nbsp;
                            <?= date('d', $q['tanggal']) . ' ' . month(date('n', $q['tanggal'])) . ' ' . date('Y', $q['tanggal']); ?>
                        </span>
                    </div>
                    <div class="d-inline">
                        <span class="btn btn-primary mb-2">
                            <span class="badge badge-light"><?= number_format($q['viewers']); ?></span>&nbsp;x ditonton
                        </span>&nbsp;
                        <span class="btn btn-secondary mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                                <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>&nbsp;
                            <?= $q['id_user']; ?>
                        </span>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
        <!-- end search result -->
    <?php endif; ?>

    <!-- pagination for search -->
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
            <?php } ?>

            <?php
            $SqlQuery = mysqli_query($conn, "SELECT videos.*, views.viewers FROM views, videos WHERE (videos.judul LIKE '%$cari%' OR videos.keterangan LIKE '%$cari%' OR videos.kategori LIKE '%$cari%' OR videos.id_user LIKE '%$cari%' OR videos.id_video LIKE '%$cari%') AND videos.id_video = views.id_video");
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
    </nav>

    <div class="text-center mb-3 text-dark" style="margin-top: -15px;">
        Ditemukan <strong><?= number_format($JumlahData); ?></strong> video
    </div> <!-- ende of pagination for search -->


<?php else :

    // prepare banner img
    $bannerImg = ["banner_left.png", "banner_right.png"];
    // prepare warna
    $kodeWarna = ["btn btn-outline-primary", "btn btn-outline-success", "btn btn-outline-danger", "btn btn-outline-warning", "btn btn-outline-info"];
    // cari semua kategori
    $katgr = $conn->query("SELECT kategori FROM videos GROUP BY kategori");
    // paling hits
    $hits = mysqli_query($conn, "SELECT videos.*, views.viewers, SUM(video_like.video_like) AS total_like, SUM(video_dislike.video_dislike) AS total_dislike FROM views, videos, video_like, video_dislike WHERE videos.id_video = views.id_video AND videos.id_video = video_like.id_video AND videos.id_video = video_dislike.id_video GROUP BY videos.id_video ORDER BY views.viewers DESC LIMIT 5");
    // data upload
    $upl = mysqli_query($conn, "SELECT videos.*, views.viewers, SUM(video_like.video_like) AS total_like, SUM(video_dislike.video_dislike) AS total_dislike FROM views, videos, video_like, video_dislike WHERE videos.id_video = views.id_video AND videos.id_video = video_like.id_video AND videos.id_video = video_dislike.id_video GROUP BY videos.id_video ORDER BY videos.tanggal DESC LIMIT 5");
    $JumlahData = mysqli_num_rows($upl);
?>

    <!-- home page -->
    <?php if ($JumlahData < 1) : ?>

        <!-- if home null -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-center p-5">404 Not Found</h3>
            </div>
        </div> <!-- end if home null -->

    <?php else : ?>

        <!-- carousel hits -->
        <div class="mb-5">
            <h5>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z" />
                </svg>&nbsp;
                Trending
            </h5>
            <div class="bd-example">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php for ($i = 0; $i < 5; $i++) : ?>
                            <li data-target="#carouselExampleCaptions" data-slide-to="<?= $i; ?>"></li>
                        <?php endfor; ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php while ($ht = mysqli_fetch_assoc($hits)) : ?>
                            <div class="carousel-item">
                                <a href="<?= base_url('index.php?page=watch&v=') . $ht['id_video']; ?>">
                                    <img src="<?= base_url('assets/img/') . $bannerImg[rand(0, 1)]; ?>" alt="banner" height="500" class="w-100" title="<?= $ht['judul'] . ' - ' . $ht['id_user']; ?>">
                                    <div class="carousel-caption">
                                        <h5><?= $ht['judul']; ?></h5>
                                        <div>
                                            <span class="badge badge-warning text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                                    <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                                                    <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                                                </svg>&nbsp;
                                                <?= $ht['kategori']; ?>
                                            </span>
                                            <span class="badge badge-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16">
                                                    <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                                </svg>&nbsp;
                                                <?= date('d', $ht['tanggal']) . ' ' . month(date('n', $ht['tanggal'])) . ' ' . date('Y', $ht['tanggal']); ?>
                                            </span>
                                            <span class="badge badge-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                                    <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2.144 2.144 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a9.84 9.84 0 0 0-.443.05 9.365 9.365 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111L8.864.046zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a8.908 8.908 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.224 2.224 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.866.866 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                                </svg>&nbsp;
                                                <?= number_format($ht['total_like']); ?>
                                            </span>
                                            <span class="badge badge-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                                    <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856 0 .289-.036.586-.113.856-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a9.877 9.877 0 0 1-.443-.05 9.364 9.364 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964l-.261.065zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a8.912 8.912 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581 0-.211-.027-.414-.075-.581-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.224 2.224 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.866.866 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1z" />
                                                </svg>&nbsp;
                                                <?= number_format($ht['total_dislike']); ?>
                                            </span>
                                            <span class="badge badge-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                                </svg>&nbsp;
                                                <?= number_format($ht['viewers']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div> <!-- end carousel hits -->

        <!-- slick kategori -->
        <div class="mb-5">
            <h5>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                    <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                    <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                </svg>&nbsp;
                Kategori
            </h5>
            <div class="slider">
                <?php while ($kt = mysqli_fetch_assoc($katgr)) : ?>
                    <div><a class="<?= $kodeWarna[rand(0, 4)]; ?>" href="<?= base_url('index.php?q=') . $kt['kategori']; ?>" role="button"><?= $kt['kategori']; ?></a></div>
                <?php endwhile; ?>
            </div>
        </div> <!-- end of slick kategori -->

        <!-- new uplod -->
        <div class="mb-5">
            <h5>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                </svg>&nbsp;
                Video Terbaru
            </h5>
            <?php while ($up = mysqli_fetch_assoc($upl)) : ?>
                <div class="card mb-3">
                    <a href="index.php?page=watch&v=<?= $up['id_video']; ?>" class="card-body">
                        <h5 class="font-weight-bold my-1"><?= $up['judul']; ?></h5>
                        <hr class="my-2">
                        <div class="my-2 text-dark">
                            <span class="badge badge-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                    <path d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z" />
                                    <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z" />
                                </svg>&nbsp;
                                <?= $up['kategori']; ?>
                            </span>&nbsp;
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16">
                                    <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                </svg>&nbsp;
                                <?= date('d', $up['tanggal']) . ' ' . month(date('n', $up['tanggal'])) . ' ' . date('Y', $up['tanggal']); ?>
                            </span>
                        </div>
                        <div class="d-inline-block">
                            <span class="btn btn-info mb-2">
                                <span class="badge badge-light"><?= number_format($up['total_like']); ?></span>&nbsp;Suka
                            </span>&nbsp;
                            <span class="btn btn-warning mb-2">
                                <span class="badge badge-light"><?= number_format($up['total_dislike']); ?></span>
                                &nbsp;Tidak Suka
                            </span>&nbsp;
                            <span class="btn btn-primary mb-2">
                                <span class="badge badge-light"><?= number_format($up['viewers']); ?></span>
                                &nbsp;x ditonton
                            </span>&nbsp;
                            <span class="btn btn-secondary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-person" viewBox="0 0 16 16">
                                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                </svg>
                                &nbsp;<?= $up['id_user']; ?>
                            </span>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div> <!-- end of new uplod -->
    <?php endif; ?>
    <!-- end of home page -->

<?php endif; ?>
<!-- end of home page -->