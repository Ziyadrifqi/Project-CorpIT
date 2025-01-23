<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Manage Role</h3>
    </div>
    <div class="card-body">
        <!-- Notifikasi Success -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Error -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
                <a href="<?= base_url('admin/roles/create'); ?>" class="btn btn-primary mb-3 float-end"><i class="fas fa-plus"></i> Add New Role</a>
            </div>
            <div class="card-body">
                <!-- Tabel Daftar Roles -->
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($roles) && is_array($roles)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($roles as $role) : ?>
                                <tr class="text-center">
                                    <th scope="row"><?= $i++ ?></th>
                                    <td><?= esc($role['name']); ?></td>
                                    <td><?= esc($role['description']); ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/roles/edit/' . $role['id']); ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="<?= base_url('admin/roles/delete/' . $role['id']); ?>" method="post" style="display:inline;">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No roles found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection(); ?>