<!-- app/Views/admin/user_roles.php -->
<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<!-- Begin Page Content -->
<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Assign User Roles</h3>
    </div>
    <div class="card-body">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
            </div>
            <div class="card-body">
                <!-- Berikan ID pada tabel untuk DataTables -->
                <table id="userRoleTable" class="table table-bordered table-sm table-striped table-hover">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <th scope="row" class="text-center"><?= $i++ ?></th>
                                <td><?= esc($user->username) ?></td>
                                <td><?= esc($user->email) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($user->role == 'Super Admin') ? 'primary' : (($user->role == 'admin') ? 'success' : 'warning'); ?>">
                                        <?= esc($user->role) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/user_roles/edit/' . $user->userid) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit Role
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<!-- Tambahkan di bagian bawah untuk inisialisasi DataTables -->
<?= $this->section('scripts'); ?>

<?= $this->endSection(); ?>