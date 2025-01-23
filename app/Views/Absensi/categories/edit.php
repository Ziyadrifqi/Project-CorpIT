<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="card shadow-sm">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
            <div class="card-tools">
                <!-- Stylish link to go back to roles list -->
                <a href="<?= base_url('/Absensi/categories'); ?>" class="text-primary mb-3" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Absen Category
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger">
                <?= session()->get('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->has('validation')): ?>
            <div class="alert alert-danger">
                <?= session()->get('validation')->listErrors() ?>
            </div>
        <?php endif; ?>
        <form action="/Absensi/categories/update/<?= esc($category['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group mb-3">
                <label for="name">Category Name</label>
                <input type="text" name="name" class="form-control" id="name" value="<?= esc($category['name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>