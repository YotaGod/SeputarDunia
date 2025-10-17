<?= $this->include('admin/layout/header') ?>

<h1 class="mt-4"><?= esc($title) ?></h1>
<p class="lead">Kelola hak akses untuk setiap peran pengguna</p>

<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">
  <i class="fas fa-plus"></i> Tambah Peran Baru
</button>

<div id="statusMessage"></div>

<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addRoleModalLabel">Tambahkan Peran Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addRoleForm">
        <div class="modal-body">
            <div id="roleValidationErrors" class="alert alert-danger d-none"></div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Peran</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSaveRole">Simpan Peran</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="statusMessage"></div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th style="width: 20%;">Peran (Role)</th>
                <th>Hak Akses (Permissions)</th>
                <th style="width: 10%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
                <tr id="role-<?= $role['id'] ?>">
                    <td>
                        <h5 class="mb-0"><?= esc($role['name']) ?></h5>
                        <small class="text-muted"><?= esc($role['description']) ?></small>
                    </td>
                    <td>
                        <?php if ($role['id'] == 1): ?>
                            <!-- Administrator selalu Full Access -->
                            <span class="badge bg-danger p-2">Akses Penuh Administrator</span>
                        <?php else: ?>
                            <!-- Accordion untuk mengelompokkan Permissions -->
                            <div class="accordion accordion-flush" id="accordionPerms-<?= $role['id'] ?>">
                                <?php $groupIndex = 0; ?>
                                <?php foreach ($permissionGroups as $groupKey => $groupName): ?>
                                    <?php if (!empty($groupedPermissions[$groupKey])): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-<?= $role['id'] ?>-<?= $groupKey ?>">
                                                <button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $role['id'] ?>-<?= $groupKey ?>" aria-expanded="false" aria-controls="collapse-<?= $role['id'] ?>-<?= $groupKey ?>">
                                                    <?= esc($groupName) ?>
                                                </button>
                                            </h2>
                                            <div id="collapse-<?= $role['id'] ?>-<?= $groupKey ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $role['id'] ?>-<?= $groupKey ?>" data-bs-parent="#accordionPerms-<?= $role['id'] ?>">
                                                <div class="accordion-body p-3">
                                                    <div class="row">
                                                        <?php foreach ($groupedPermissions[$groupKey] as $perm): ?>
                                                            <div class="col-md-6 col-lg-4 mb-2">
                                                                <div class="form-check">
                                                                    <?php 
                                                                        $isChecked = in_array($perm['id'], $rolePermissionsMap[$role['id']] ?? []);
                                                                    ?>
                                                                    <input class="form-check-input permission-checkbox" 
                                                                           type="checkbox" 
                                                                           value="<?= $perm['id'] ?>" 
                                                                           id="perm-<?= $role['id'] ?>-<?= $perm['id'] ?>"
                                                                           data-role-id="<?= $role['id'] ?>"
                                                                           <?= $isChecked ? 'checked' : '' ?>>
                                                                    <label class="form-check-label fw-bold" for="perm-<?= $role['id'] ?>-<?= $perm['id'] ?>">
                                                                        <?= esc($perm['key']) ?>
                                                                    </label>
                                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">— <?= esc($perm['description']) ?></small>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $groupIndex++; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="d-flex gap-2 me-1">
                        <?php if ($role['id'] != 1): ?>
                            <button class="btn btn-sm btn-primary btn-save-permissions" data-role-id="<?= $role['id'] ?>" disabled>
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            
                            <button class="btn btn-sm btn-danger btn-delete-role" data-role-id="<?= $role['id'] ?>">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const csrfToken = '<?= csrf_hash() ?>';

    // Aktifkan tombol 'Simpan' saat ada perubahan checkbox
    $('.permission-checkbox').on('change', function() {
        const roleId = $(this).data('role-id');
        // Aktifkan tombol simpan di baris yang sama
        $(`#role-${roleId} .btn-save-permissions`).prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    });

    // Handle klik tombol 'Simpan' via AJAX
    $('.btn-save-permissions').on('click', function() {
        const button = $(this);
        const roleId = button.data('role-id');
        const row = $(`#role-${roleId}`);
        
        // Kumpulkan semua ID permission yang DICENTANG untuk peran ini
        const selectedPermissions = [];
        row.find('.permission-checkbox:checked').each(function() {
            selectedPermissions.push($(this).val());
        });
        
        button.prop('disabled', true).text('Menyimpan...').removeClass('btn-primary').addClass('btn-secondary');

        $.ajax({
            url: '<?= base_url('admin/roles/save-permissions') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                role_id: roleId,
                permissions: selectedPermissions,
                csrf_test_name: csrfToken
            },
            success: function(response) {
                if (response.success) {
                    $('#statusMessage').html(`<div class="alert alert-success">${response.message}</div>`).fadeIn().delay(3000).fadeOut();
                } else {
                    $('#statusMessage').html(`<div class="alert alert-danger">Gagal: ${response.message}</div>`).fadeIn();
                }
            },
            error: function() {
                $('#statusMessage').html(`<div class="alert alert-danger">Terjadi kesalahan server saat menyimpan.</div>`).fadeIn();
            },
            complete: function() {
                button.text('Simpan');
                // Tombol tetap nonaktif setelah sukses disimpan
            }
        });
    });
});

    // ===============================================
    // 2. LOGIKA TAMBAH PERAN BARU (BARU)
    // ===============================================

    $('#addRoleForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const button = $('#btnSaveRole');
        const originalText = button.text();
        const errorsDiv = $('#roleValidationErrors');

        button.prop('disabled', true).text('Memproses...');
        errorsDiv.empty().addClass('d-none');

        $.ajax({
            url: '<?= base_url('admin/roles/store') ?>',
            type: 'POST',
            data: {
                name: $('#name').val(),
                description: $('#description').val(),
                csrf_test_name: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addRoleModal').modal('hide');
                    $('#statusMessage').html(`<div class="alert alert-success">Peran baru berhasil ditambahkan</div>`).fadeIn();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    errorsDiv.html(`<div class="alert alert-danger">${response.message}</div>`).removeClass('d-none');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    Object.values(response.errors).forEach(error => {
                        errorHtml += `<li>${error}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    errorsDiv.html(errorHtml).removeClass('d-none');
                } else {
                    errorsDiv.html('<div class="alert alert-danger">Terjadi kesalahan server</div>').removeClass('d-none');
                }
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // ===============================================
    // 3. LOGIKA HAPUS PERAN (DELETE)
    // ===============================================

    $('.btn-delete-role').on('click', function() {
        const button = $(this);
        const roleId = button.data('role-id');
        const row = $(`#role-${roleId}`);

        if (!confirm('Anda yakin ingin menghapus peran ini? Aksi ini tidak dapat dibatalkan.')) {
            return;
        }

        button.prop('disabled', true).html('<i class="fas fa-sync-alt fa-spin"></i>');

        $.ajax({
            url: '<?= base_url('admin/roles/delete') ?>/' + roleId,
            type: 'POST',
            dataType: 'json',
            data: { csrf_test_name: '<?= csrf_hash() ?>' },
            success: function(response) {
                if (response.success) {
                    $('#statusMessage').html(`<div class="alert alert-success">${response.message}</div>`).fadeIn().delay(3000).fadeOut();
                    
                    // Hapus baris dari tabel secara instan
                    row.fadeOut(500, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Gagal menghapus: ' + response.message);
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan server saat menghapus.';
                alert(message);
                button.prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus');
            }
        });
    });
</script>

<?= $this->include('admin/layout/footer') ?>
