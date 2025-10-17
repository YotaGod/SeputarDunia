<?php $this->extend('admin/layout/header'); ?>

<?= $this->include('admin/layout/header') ?>

<h1 class="mt-4"><?= esc($title) ?></h1>
<p class="lead">Isi detail akun baru yang akan dibuat.</p>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
        <?= session()->getFlashdata('error') ?>
        <ul>
        <?php foreach ($validation->getErrors() as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card p-4">
    <form action="<?= base_url('admin/users/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Peran (Role)</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="">Pilih Peran</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                        <?= esc($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
    </form>
</div>

<?= $this->include('admin/layout/footer') ?>