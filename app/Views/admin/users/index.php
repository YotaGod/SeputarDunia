<?php $this->extend('admin/layout/header'); ?>

<?= $this->include('admin/layout/header') ?>

<h1 class="mt-4"><?= esc($title) ?></h1>

<?php if (has_permission('user-manage')): ?>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-success mb-3">
        <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
    </a>
<?php endif; ?>

<p class="lead">Kelola peran dan status akun pengguna di portal Seputar Dunia.</p>

<div id="userStatusMessage"></div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username / Email</th>
                <th>Peran Saat Ini</th>
                <th>Ubah Peran</th>
                <th>Status</th>
                <th>Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr id="user-<?= $user['id'] ?>">
                    <td><?= esc($user['id']) ?></td>
                    <td>
                        <strong><?= esc($user['username']) ?></strong>
                        <br><small class="text-muted"><?= esc($user['email']) ?></small>
                    </td>
                    <td><?= esc($user['role_name']) ?></td>
                    
                    <td>
                        <select class="form-select user-role-change" data-user-id="<?= $user['id'] ?>" <?= $user['id'] == session()->get('user_id') ? 'disabled' : '' ?>>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                    <?= esc($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : ($user['status'] == 'banned' ? 'danger' : 'secondary') ?>">
                            <?= esc(strtoupper($user['status'])) ?>
                        </span>
                    </td>
                    
                    <td>
                        <select class="form-select user-status-change" data-user-id="<?= $user['id'] ?>" <?= $user['id'] == session()->get('user_id') ? 'disabled' : '' ?>>
                            <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="banned" <?= $user['status'] == 'banned' ? 'selected' : '' ?>>Blokir (Banned)</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const csrfToken = '<?= csrf_hash() ?>';

    // Fungsi universal untuk mengirim update via AJAX
    function sendUpdate(userId, field, value) {
        let data = { csrf_test_name: csrfToken };
        data[field] = value;
        
        $.ajax({
            url: '<?= base_url('admin/users/update') ?>/' + userId,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#userStatusMessage').html(`<div class="alert alert-success">${response.message}</div>`).fadeIn().delay(3000).fadeOut();
                    
                    // Untuk status, update badge secara manual
                    if (field === 'status') {
                        const badge = $(`#user-${userId}`).find('.badge');
                        badge.text(value.toUpperCase());
                        
                        // Update warna badge
                        badge.removeClass('bg-success bg-danger bg-secondary');
                        if (value === 'active') badge.addClass('bg-success');
                        else if (value === 'banned') badge.addClass('bg-danger');
                        else badge.addClass('bg-secondary');
                    }
                    
                    // Jika role diubah, reload agar nama role di kolom kedua update
                    if (field === 'role_id') {
                         window.location.reload(); 
                    }
                } else {
                    alert('Gagal memperbarui: ' + response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON ? xhr.responseJSON.message : 'Kesalahan Server. Cek hak akses.';
                alert(message);
                window.location.reload(); // Refresh untuk mengembalikan nilai dropdown yang gagal
            }
        });
    }

    // Event listener untuk perubahan Peran
    $('.user-role-change').on('change', function() {
        const userId = $(this).data('user-id');
        const roleId = $(this).val();
        sendUpdate(userId, 'role_id', roleId);
    });

    // Event listener untuk perubahan Status
    $('.user-status-change').on('change', function() {
        const userId = $(this).data('user-id');
        const status = $(this).val();
        sendUpdate(userId, 'status', status);
    });
});
</script>

<?= $this->include('admin/layout/footer') ?>