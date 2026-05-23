<?php
/**
 * @var string $title
 * @var array $articles
 */
?>
<?= $this->include('admin/layout/header') ?>

<h1 class="mt-4"><?= esc($title) ?></h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (has_permission('article-create')): ?>
    <a href="<?= base_url('admin/articles/new') ?>" class="btn btn-danger mb-3">
        <i class="fas fa-plus"></i> Buat Artikel Baru
    </a>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Status</th>
                <th>Views</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($articles as $article): ?>
                <?php if ($article === null) continue; ?> <!-- TAMBAHKAN BARIS INI -->
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <?= esc($article['title']) ?>
                        <br><small class="text-muted"><?= esc($article['category_name'] ?? 'N/A') ?></small>
                    </td>
                    <td><?= esc($article['author_name'] ?? 'N/A') ?></td>
                    <td><span class="badge bg-<?= ($article['status'] == 'published' ? 'success' : ($article['status'] == 'pending' ? 'warning' : 'secondary')) ?>"><?= esc(strtoupper($article['status'])) ?></span></td>
                    <td><?= esc($article['views'] ?? 0) ?></td>
                    <td>
                        <?php if (has_permission('article-edit-all') || (has_permission('article-edit-own') && $article['user_id'] == session()->get('user_id'))): ?>
                            <a href="<?= base_url('admin/articles/' . $article['id'] . '/edit') ?>" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i> Edit</a>
                        <?php endif; ?>
                        
                        <?php if (has_permission('article-review') && $article['status'] == 'pending'): ?>
                            <button class="btn btn-sm btn-success me-1" onclick="publishArticle(<?= $article['id'] ?>, this)"><i class="fas fa-check"></i> Setujui</button>
                        <?php endif; ?>
                        
                        <?php if (has_permission('article-edit-all')): ?>
                            <button class="btn btn-sm btn-danger" onclick="deleteArticle(<?= $article['id'] ?>, this)"><i class="fas fa-trash"></i> Hapus</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->include('admin/layout/footer') ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const csrfToken = '<?= csrf_hash() ?>';

    // ------------------------------------
    // Fungsi Publish/Approve (MODIFIED)
    // ------------------------------------
    function publishArticle(articleId, buttonElement) {
        if (!confirm('Anda yakin ingin mempublikasikan artikel ini?')) {
            return;
        }

        const row = $(buttonElement).closest('tr'); // Temukan baris tabel artikel
        const csrfToken = '<?= csrf_hash() ?>';

        $.ajax({
            url: '<?= base_url('admin/articles/publish') ?>/' + articleId,
            type: 'POST',
            dataType: 'json',
            data: { csrf_test_name: csrfToken },
            success: function(response) {
                if (response.success) {
                    // Hapus tombol Approve/Reject
                    $(buttonElement).remove(); 
                    
                    // Cari dan update kolom Status
                    const statusCell = row.find('td:eq(3)'); // Asumsi kolom status adalah kolom ke-4 (index 3)
                    
                    // Update badge status dan teksnya
                    statusCell.html('<span class="badge bg-success">PUBLISHED</span>');
                    
                    alert(response.message);
                    
                } else {
                    alert('Gagal mempublikasikan: ' + response.message);
                }
            },
            error: function() {
                alert('Akses ditolak atau terjadi kesalahan server.');
            }
        });
    }

    // ------------------------------------
    // Fungsi Hapus (MODIFIED)
    // ------------------------------------
    function deleteArticle(articleId, buttonElement) {
        if (!confirm('ANDA YAKIN INGIN MENGHAPUS ARTIKEL INI SECARA PERMANEN?')) {
            return;
        }
        
        const row = $(buttonElement).closest('tr'); // Temukan baris tabel artikel
        const csrfToken = '<?= csrf_hash() ?>';

        $.ajax({
            url: '<?= base_url('admin/articles/delete') ?>/' + articleId,
            type: 'POST',
            dataType: 'json',
            data: { csrf_test_name: csrfToken }, 
            success: function(response) {
                if (response.success) {
                    // Hapus baris dari tabel secara instan (tanpa reload)
                    row.fadeOut(500, function() {
                        $(this).remove();
                    });
                    alert(response.message);
                    
                } else {
                    alert('Gagal menghapus: ' + response.message);
                }
            },
            error: function() {
                alert('Akses ditolak atau terjadi kesalahan server.');
            }
        });
    }
</script>

