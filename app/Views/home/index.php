<?= $this->include('layout/header') ?>

<div class="row">
    <div class="col-12">
        <?php if (!empty($newsApiError)): ?>
            <div class="alert alert-danger" role="alert">
                Gagal memuat berita dari News API: <?= esc($newsApiError) ?>
            </div>
        <?php endif; ?>

        <h2>Artikel Unggulan (Featured)</h2>
        <?php if (!empty($featuredArticles)): ?>
            <?php $feat = $featuredArticles[0]; ?>
            <div class="card bg-dark text-white mb-4 featured-card">
                <img src="<?= $feat['image_url'] ?? base_url('img/placeholder.jpg') ?>" class="card-img" alt="<?= esc($feat['title']) ?>" style="height: 350px; object-fit: cover; opacity: 0.7;">
                <div class="card-img-overlay d-flex flex-column justify-content-end">
                    <span class="badge bg-danger mb-2 p-2">FEATURED | DILIHAT: <?= esc($feat['views']) ?></span>
                    <h5 class="card-title display-5 fw-bold"><?= esc($feat['title']) ?></h5>
                    <p class="card-text text-white-50"><?= esc($feat['excerpt']) ?></p>
                    <a href="<?= base_url('article/' . $feat['slug']) ?>" class="stretched-link text-warning fw-bold">Baca Sekarang</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Belum ada artikel unggulan lokal yang dipublikasikan.</div>
        <?php endif; ?>

        <hr>

        <h2>Berita Terbaru (Dari News API)</h2>
        <div class="row">
            <div class="col-md-8">
                <?php if (!empty($latestNews)): ?>
                    <?php foreach ($latestNews as $news): ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?= $news['urlToImage'] ?? 'placeholder-url.jpg' ?>" class="img-fluid rounded-start" alt="News Image" style="height: 100%; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($news['title']) ?></h5>
                                        <p class="card-text text-muted small">
                                            <?= esc($news['source']['name']) ?> | <?= date('d M Y', strtotime($news['publishedAt'])) ?>
                                        </p>
                                        <p class="card-text"><?= esc($news['description']) ?></p>
                                        <a href="<?= esc($news['url']) ?>" class="btn btn-sm btn-danger" target="_blank">Baca Selengkapnya <i class="fas fa-external-link-alt"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Tidak dapat mengambil berita terbaru.</div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <h4>Artikel Lokal Terbaru</h4>
                <ul class="list-group list-group-flush border rounded">
                    <?php if (!empty($localArticles)): ?>
                        <?php foreach ($localArticles as $local): ?>
                            <li class="list-group-item d-flex flex-column align-items-start">
                                <div class="ms-1 me-auto">
                                    <div class="fw-bold">
                                        <a href="<?= base_url('article/' . $local['slug']) ?>" class="text-decoration-none text-dark">
                                            <?= esc($local['title']) ?>
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        Dilihat: <?= esc($local['views'] ?? 0) ?> kali
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">Belum ada artikel lokal.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>