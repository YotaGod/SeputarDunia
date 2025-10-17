<?= $this->include('admin/layout/header') ?>

<div class="row">
    <div class="col-12">
        <h1 class="mt-4">Dashboard Admin</h1>
        <hr>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Artikel Dipublikasikan</h5>
                        <p class="card-text fs-1"><?= esc($stats['published']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Menunggu Review</h5>
                        <p class="card-text fs-1"><?= esc($stats['pending']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pengunjung Berlangganan</h5>
                        <p class="card-text fs-1"><?= esc($stats['subscribers']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/layout/footer') ?>