<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Attendance Categories</h3>
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

        <!-- Tombol untuk Menambahkan Kategori -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
                <a href="<?= base_url('/Absensi/categories/create'); ?>" class="btn btn-primary float-end"><i class="fas fa-plus"></i> Add Categories</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="categories">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr class="text-center">
                                        <td scope="row"><?= $i++ ?></td>
                                        <td><?= esc($category['name']) ?></td>
                                        <td>
                                            <a href="/Absensi/categories/edit/<?= esc($category['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="/Absensi/categories/delete/<?= esc($category['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>