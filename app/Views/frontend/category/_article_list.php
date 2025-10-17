<h1 class="mb-4" id="category-title">Kategori: <?= esc($currentCategory['name']) ?></h1>
    
<div class="row" id="article-container">
    <?php if (!empty($articles)): ?>
        <?php foreach ($articles as $article): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?= esc($article['image_url']) ?>" class="card-img-top" alt="<?= esc($article['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= esc($article['title']) ?></h5>
                        <p class="card-text text-muted small">
                            Oleh: <?= esc($article['username']) ?> | <?= date('d M Y', strtotime($article['published_at'])) ?>
                        </p>
                        <p class="card-text flex-grow-1"><?= esc($article['excerpt']) ?></p>
                        <a href="<?= base_url('article/' . $article['slug']) ?>" class="btn btn-sm btn-danger mt-auto">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Belum ada artikel yang dipublikasikan di kategori ini.
        </div>
    <?php endif; ?>
</div>
