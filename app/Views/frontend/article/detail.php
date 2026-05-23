<?= $this->include('layout/header') ?>

<div class="row">
    <div class="col-lg-8">
        
        <h1 class="fw-bold mb-3"><?= esc($article['title']) ?></h1>
        
        <p class="text-muted small border-bottom pb-2">
            Oleh: <span class="fw-bold text-dark"><?= esc($article['username']) ?></span> 
            
            | Kategori: <a href="<?= base_url('category/' . $article['category_slug']) ?>" class="text-danger text-decoration-none fw-bold"><?= esc($article['category_name']) ?></a>
            
            <span class="float-end">Dilihat: <?= esc($stats['views']) ?> kali</span>
        </p>
        <?php if (!empty($article['image_url'])): ?>
            <img src="<?= esc($article['image_url']) ?>" class="img-fluid rounded mb-4" alt="<?= esc($article['title']) ?>">
        <?php endif; ?>

        <div class="article-content mb-5">
            <?= $article['content'] ?>
        </div>

        <?php if (session()->get('isLoggedIn')): ?>
            <div class="d-flex justify-content-start align-items-center border-top pt-3 mb-5">
                <span class="me-3 text-muted">Beri Rating:</span>
                <button class="btn btn-outline-success btn-sm me-2 btn-rate" data-action="like" data-id="<?= $article['id'] ?>">
                    <i class="fas fa-thumbs-up"></i> Like (<span id="like-count"><?= esc($stats['likes']) ?></span>)
                </button>
                <button class="btn btn-outline-danger btn-sm btn-rate" data-action="dislike" data-id="<?= $article['id'] ?>">
                    <i class="fas fa-thumbs-down"></i> Dislike (<span id="dislike-count"><?= esc($stats['dislikes']) ?></span>)
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Silakan <a href="<?= base_url('login') ?>">Login</a> untuk memberi rating pada artikel ini.</div>
        <?php endif; ?>

        <div class="comments-section mb-5">
            <h3 class="mb-3">Komentar</h3>

            <?php if (session()->get('isLoggedIn')): ?>
                <?php if ($canComment): ?>
                    <div class="card p-3 mb-4">
                        <h5>Tinggalkan Komentar Anda:</h5>
                        <?= form_open(base_url('article/comment'), ['id' => 'commentForm']) ?>
                            <?= csrf_field() ?>
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <div class="mb-3">
                                <textarea name="content" id="commentContent" class="form-control" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">Kirim Komentar</button>
                        <?= form_close() ?>
                        <div id="commentMessage" class="mt-2"></div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Fitur komentar hanya tersedia untuk **Pengunjung Berlangganan**. Silakan <a href="<?= base_url('berlangganan') ?>">berlangganan di sini</a>.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                 <div class="alert alert-info">
                    Silakan <a href="<?= base_url('login') ?>">Login</a> untuk berkomentar atau berlangganan.
                </div>
            <?php endif; ?>

            <h4 class="mt-4 border-bottom pb-2">Komentar Pembaca (<?= count($comments) ?>)</h4>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="card-text"><?= esc($comment['content']) ?></p>
                            <p class="card-subtitle text-muted small mt-2">
                                Oleh: User ID <?= esc($comment['user_id']) ?> | <?= date('d M Y H:i', strtotime($comment['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Belum ada komentar disetujui untuk artikel ini.</p>
            <?php endif; ?>

        </div>
        </div>
    
    <div class="col-lg-4">
        <h4 class="mb-3">Berita Populer Lainnya</h4>
        </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Logika AJAX untuk Rating dan Komentar (seperti yang sudah kita finalkan)
    const csrfToken = '<?= csrf_hash() ?>';

    // ------------------------------------
    // 1. AJAX UNTUK LIKE/DISLIKE (Rating)
    // ------------------------------------
    $('.btn-rate').on('click', function() {
        if (!'<?= session()->get('isLoggedIn') ?>') {
            alert('Anda harus login untuk memberi rating.');
            return;
        }

        const button = $(this);
        const articleId = button.data('id');
        const action = button.data('action');
        const countSpan = $('#' + action + '-count');

        $.ajax({
            url: '<?= base_url('article/rate') ?>',
            type: 'POST',
            data: { 
                article_id: articleId, 
                action: action, 
                csrf_test_name: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let currentCount = parseInt(countSpan.text());
                    countSpan.text(currentCount + 1);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                 const response = xhr.responseJSON;
                 if (response && response.message) {
                     alert(response.message);
                 } else {
                     alert('Gagal memberi rating. Pastikan Anda memiliki koneksi yang stabil.');
                 }
            }
        });
    });

    // ------------------------------------
    // 2. AJAX UNTUK KOMENTAR
    // ------------------------------------
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const messageDiv = $('#commentMessage');
        const submitBtn = form.find('button[type="submit"]');

        messageDiv.removeClass('alert alert-success alert-danger').text('');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    messageDiv.addClass('alert alert-success').text(response.message);
                    $('#commentContent').val(''); 
                } else {
                    messageDiv.addClass('alert alert-danger').text('Gagal mengirim: ' + (response.message || 'Data tidak valid.'));
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                const errorMsg = (response && response.message) ? response.message : 'Kesalahan Server. Cek status langganan.';
                messageDiv.addClass('alert alert-danger').text(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });
</script>

<?= $this->include('layout/footer') ?>