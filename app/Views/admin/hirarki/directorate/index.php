<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Directorates</h3>
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
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
                <!-- Tombol untuk Menambahkan Kategori -->
                <a href="<?= base_url('admin/hirarki/directorate/create'); ?>" class="btn btn-primary float-end">
                    <i class="fas fa-plus"></i> Add Directorate
                </a>
            </div>
            <div class="card-body">
                <!-- Tabel untuk Menampilkan Kategori -->
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
                            <?php foreach ($directorate as $dir) : ?>
                                <tr class="text-center">
                                    <td scope="row"><?= $i++ ?></td>
                                    <td><?= esc($dir['name']); ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/hirarki/directorate/edit/' . $dir['id']); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="<?= base_url('admin/hirarki/directorate/delete/' . $dir['id']); ?>" method="post" style="display: inline-block;">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="delete"> <!-- Perhatikan huruf kecil pada 'delete' -->
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this directorate?');">
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