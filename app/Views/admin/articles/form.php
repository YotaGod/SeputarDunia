<?php
/**
 * @var string $title
 * @var array $article
 * @var array $categories
 * @var string $articleTags
 */
?>
<?= $this->include('admin/layout/header') ?>

<h1 class="mt-4"><?= esc($title) ?></h1>

<?php // Menggunakan logika dasar untuk form edit vs create
    $isEdit = isset($article);
    $url = $isEdit ? 'admin/articles/' . $article['id'] : 'admin/articles';
?>

<?= form_open($url, ['id' => 'articleForm']) ?>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT"> 
    <?php endif; ?>
    
    <div class="mb-3">
        <label for="title" class="form-label">Judul Artikel</label>
        <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $article['title'] ?? '') ?>" required>
        <div class="invalid-feedback"></div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Pilih Kategori</option>
                <?php $selectedCat = old('category_id', $article['category_id'] ?? ''); ?>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $selectedCat == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"></div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="image_url" class="form-label">URL Gambar Utama</label>
            <input type="text" class="form-control" id="image_url" name="image_url" value="<?= old('image_url', $article['image_url'] ?? '') ?>">
        </div>
    </div>
    
    <div class="mb-3">
        <label for="excerpt" class="form-label">Cuplikan (Maks. 255 Karakter)</label>
        <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="255"><?= old('excerpt', $article['excerpt'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Konten Artikel</label>
        <textarea class="form-control" id="content" name="content" rows="10" required><?= old('content', $article['content'] ?? '') ?></textarea>
        <div class="invalid-feedback"></div>
        <small class="form-text text-muted">Akan diintegrasikan dengan Rich Text Editor seperti CKEditor.</small>
    </div>
    
    <div class="mb-3">
        <label for="tags" class="form-label">Tags (Pisahkan dengan koma)</label>
        <?php 
            // Untuk form edit, tampilkan tags yang sudah ada (Anda perlu mengimplementasikan logika pengambilan tags di Controller)
            $currentTags = old('tags', $articleTags ?? ''); 
        ?>
        <input type="text" class="form-control" id="tags" name="tags" value="<?= esc($currentTags) ?>">
        <small class="form-text text-muted">Pisahkan setiap tag dengan tanda koma (contoh: politik, ekonomi).</small>
    </div>
    
    <button type="button" class="btn btn-secondary me-2 btn-save" data-status="draft">Simpan sebagai Draft</button>
    <?php if (has_permission('article-review') || has_permission('article-create')): ?>
        <button type="button" class="btn btn-danger btn-save" data-status="pending">Ajukan untuk Review</button>
        <?php endif; ?>
        <?php if (has_permission('article-publish')): // Hanya Admin/Editor yang punya hak publish langsung ?>
            <button type="button" class="btn btn-success btn-save" data-status="published">Publish Langsung</button>
            <?php endif; ?>
            
            
            <?= form_close() ?>
            
            <?= $this->include('admin/layout/footer') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Simpan teks asli tombol sebelum dinonaktifkan
    $('.btn-save').each(function() {
        const status = $(this).data('status');
        $(this).data('original-text', $(this).text());
    });
    
    $('.btn-save').on('click', function(e) {
        e.preventDefault();
        
        const form = $('form');
        const actionStatus = $(this).data('status');
        const url = form.attr('action');
        
        // Cek apakah ini mode Edit (mengandung input hidden _method) atau Create (POST)
        const requestType = form.find('input[name=_method]').val() || 'POST'; 

        let dataToSend;
        let contentTypeValue;
        let processDataValue = true;
        
        // Reset validasi visual
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');
        
        // 1. Logika Pengemasan Data
        // Selalu gunakan FormData, CodeIgniter akan menangani spoofing via input hidden _method=PUT
        dataToSend = new FormData(form[0]);
        dataToSend.append('action', actionStatus);
        contentTypeValue = false; // Wajib false untuk FormData
        processDataValue = false; // Wajib false untuk FormData
        
        // Ambil CSRF token dari input
        const csrfToken = form.find('input[name=csrf_test_name]').val();
        
        // 2. Kirim Permintaan AJAX
        $.ajax({
            url: url,
            type: 'POST', // Selalu POST, spoofing di-handle via dataToSend (_method)
            data: dataToSend,
            processData: processDataValue,
            contentType: contentTypeValue,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken, // Kirim CSRF di header
            },
            beforeSend: function() {
                $('.btn-save').prop('disabled', true).text('Memproses...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Redirect ke daftar artikel setelah sukses
                    window.location.href = '<?= base_url('admin/articles') ?>'; 
                } else {
                    alert('Gagal: ' + (response.message || 'Data tidak valid.'));
                }
            },
            error: function(xhr) {
                // Kembalikan tombol ke keadaan semula
                $('.btn-save').prop('disabled', false).each(function() {
                    $(this).text($(this).data('original-text'));
                });

                const response = xhr.responseJSON;
                if (xhr.status === 400 && response && response.errors) {
                    // Tampilkan error validasi
                    $.each(response.errors, function(key, value) {
                        // Hanya tampilkan jika field benar-benar ada di form
                        if ($(`#${key}`).length) {
                             $(`#${key}`).addClass('is-invalid').after(`<div class="invalid-feedback">${value}</div>`);
                        }
                    });
                } else if (xhr.status === 403) {
                    alert('Akses ditolak: Anda tidak memiliki izin.');
                } else {
                    // Ini menangani error 500 (Internal Server Error)
                    alert('Terjadi kesalahan server saat menyimpan data.');
                }
            }
        });
    });
});
</script>
