<?= $this->include('layout/header') ?>

<style>
    /* Premium Futuristic Bento Layout */
    .bento-container {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }
    
    .glass-card {
        background: var(--glass-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0, 0.5);
    }
    
    .glass-card img {
        transition: transform 0.5s ease;
    }
    
    .glass-card:hover img {
        transform: scale(1.05);
    }
    
    .feat-card {
        grid-column: span 12;
        min-height: 400px;
    }
    
    @media (min-width: 992px) {
        .feat-card {
            grid-column: span 8;
        }
    }
    
    .feat-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem;
        background: linear-gradient(to top, rgba(11, 15, 25, 1) 0%, rgba(11, 15, 25, 0) 100%);
        z-index: 2;
    }
    
    .feat-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        opacity: 0.8;
    }

    .local-card {
        grid-column: span 12;
    }
    
    @media (min-width: 992px) {
        .local-card {
            grid-column: span 4;
        }
    }
    
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .news-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .news-img-wrapper {
        height: 200px;
        overflow: hidden;
    }
    
    .news-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .section-title {
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
        padding-bottom: 0.5rem;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background-color: var(--accent-color);
        border-radius: 2px;
    }
    
    .badge-custom {
        background-color: var(--accent-color);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .local-list-item {
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 0;
        transition: padding-left 0.3s ease;
    }
    .local-list-item:last-child {
        border-bottom: none;
    }
    .local-list-item:hover {
        padding-left: 0.5rem;
    }
    .local-list-link {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    .local-list-link:hover {
        color: var(--accent-hover);
    }
</style>

<?php if (!empty($newsApiError)): ?>
    <div class="alert alert-danger border-0 glass-card mb-4" role="alert">
        Gagal memuat berita dari API: <?= esc($newsApiError) ?>
    </div>
<?php endif; ?>

<div class="bento-container mb-5">
    <!-- Featured Article -->
    <div class="feat-card glass-card">
        <?php if (!empty($featuredArticles)): ?>
            <?php $feat = $featuredArticles[0]; ?>
            <img src="<?= $feat['image_url'] ?? base_url('img/placeholder.jpg') ?>" class="feat-img" alt="<?= esc($feat['title']) ?>">
            <div class="feat-overlay">
                <span class="badge-custom mb-3 d-inline-block">FEATURED</span>
                <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.5rem, 4vw, 2.5rem); line-height: 1.2;">
                    <?= esc($feat['title']) ?>
                </h2>
                <p class="text-secondary mb-3 d-none d-md-block" style="max-width: 80%;"><?= esc($feat['excerpt']) ?></p>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('article/' . $feat['slug']) ?>" class="btn-primary-custom text-decoration-none">Read Article</a>
                    <span class="text-secondary text-sm"><i class="fas fa-eye me-1"></i> <?= esc($feat['views']) ?> views</span>
                </div>
            </div>
        <?php else: ?>
            <div class="p-5 d-flex align-items-center justify-content-center h-100">
                <p class="text-secondary">Belum ada artikel unggulan lokal.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Local Articles Side Panel -->
    <div class="local-card glass-card p-4">
        <h4 class="section-title fs-5">Lokal Update</h4>
        <div class="local-list">
            <?php if (!empty($localArticles)): ?>
                <?php foreach ($localArticles as $local): ?>
                    <div class="local-list-item">
                        <a href="<?= base_url('article/' . $local['slug']) ?>" class="local-list-link d-block mb-1">
                            <?= esc($local['title']) ?>
                        </a>
                        <div class="d-flex justify-content-between text-secondary" style="font-size: 0.8rem;">
                            <span><?= esc($local['author_name'] ?? 'Admin') ?></span>
                            <span><i class="fas fa-eye me-1"></i> <?= esc($local['views'] ?? 0) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary small mt-3">Belum ada artikel lokal.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Global News API Grid -->
<div class="mt-5">
    <h3 class="section-title mb-4">Berita Global</h3>
    <div class="news-grid">
        <?php if (!empty($latestNews)): ?>
            <?php foreach ($latestNews as $news): ?>
                <div class="glass-card news-card">
                    <div class="news-img-wrapper">
                        <img src="<?= $news['urlToImage'] ?? 'https://via.placeholder.com/400x200?text=No+Image' ?>" class="news-img" alt="News Image">
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <span class="text-secondary small mb-2 d-flex justify-content-between">
                            <span><?= esc($news['source']['name']) ?></span>
                            <span><?= date('d M', strtotime($news['publishedAt'])) ?></span>
                        </span>
                        <h5 class="fw-bold mb-3 fs-6 flex-grow-1">
                            <a href="<?= esc($news['url']) ?>" target="_blank" class="text-decoration-none text-primary-hover" style="color: var(--text-primary);">
                                <?= esc($news['title']) ?>
                            </a>
                        </h5>
                        <a href="<?= esc($news['url']) ?>" class="btn-accent text-center text-decoration-none w-100 mt-auto" target="_blank">
                            Read on Source <i class="fas fa-external-link-alt ms-1" style="font-size: 0.8em;"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning border-0 glass-card col-span-full">Tidak dapat mengambil berita global saat ini.</div>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('layout/footer') ?>