<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 text-white">Manajemen Komentar</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Artikel</th>
                        <th>Komentar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($comments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada komentar</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($comments as $c): ?>
                            <tr>
                                <td><?= date('d M Y H:i', strtotime($c['created_at'])) ?></td>
                                <td><?= esc($c['username']) ?></td>
                                <td><?= esc($c['article_title']) ?></td>
                                <td><?= esc($c['content']) ?></td>
                                <td>
                                    <?php if($c['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif($c['status'] == 'rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- Form Ubah Status -->
                                        <form action="<?= base_url('admin/comments/status/' . $c['id']) ?>" method="POST" class="d-flex gap-1">
                                            <?= csrf_field() ?>
                                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                <option value="pending" <?= $c['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="approved" <?= $c['status'] == 'approved' ? 'selected' : '' ?>>Approve</option>
                                                <option value="rejected" <?= $c['status'] == 'rejected' ? 'selected' : '' ?>>Reject</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                        </form>
                                        
                                        <!-- Form Hapus -->
                                        <form action="<?= base_url('admin/comments/delete/' . $c['id']) ?>" method="POST" onsubmit="return confirm('Hapus komentar ini?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('admin/layout/footer') ?>
