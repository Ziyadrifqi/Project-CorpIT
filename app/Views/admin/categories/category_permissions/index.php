<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Category User Permissions</h3>
    </div>
    <div class="card-body">
        <!-- Notifikasi Success -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Error -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" id="alert-error" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        <?= esc($title); ?>
                    </div>
                    <!-- Tombol untuk Menambahkan Kategori -->
                    <a href="<?= base_url('admin/category-permissions/create'); ?>" class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> Add New Permission
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabel untuk Menampilkan Kategori -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="categoryabsen">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>User</th>
                                <th>Categories</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($categoryPermissions as $permission): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($permission['username']); ?></td>
                                    <td><?= esc($permission['categories']); ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url("admin/category-permissions/edit/{$permission['id']}"); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="<?= base_url("admin/category-permissions/delete/{$permission['id']}"); ?>" method="post" style="display: inline-block;">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<!-- Tambahkan di bagian bawah untuk inisialisasi DataTables -->
<?= $this->section('scripts'); ?>
<?= $this->endSection(); ?>