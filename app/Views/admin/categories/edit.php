<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>
                Edit Category Form
                <a href="<?= base_url('admin/categories'); ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
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
                <form action="<?= base_url('admin/categories/update/' . $category['id']); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="form-group">
                        <label for="name">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $category['name']; ?>" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>