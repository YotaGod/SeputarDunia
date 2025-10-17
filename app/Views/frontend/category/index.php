<?= $this->include('layout/header') ?>

<div class="container my-5" id="main-content-area">
    <!-- Konten di-include dari partial view -->
    <?= $this->include('frontend/category/_article_list') ?>
</div>

<?= $this->include('layout/footer') ?>