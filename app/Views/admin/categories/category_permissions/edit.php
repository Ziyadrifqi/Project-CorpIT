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
                <a href="<?= base_url('admin/category-permissions'); ?>" class="text-primary mb-3 float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Categories Permissions
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->get('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('validation')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->get('validation')->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/category-permissions/update/' . $user['id']); ?>" method="post">
                    <?= csrf_field(); ?>

                    <!-- User Information -->
                    <div class="form-group mb-4">
                        <label class="form-label">User</label>
                        <input type="text" class="form-control" value="<?= esc($user['username']); ?>" readonly>
                    </div>

                    <!-- Category Selection -->
                    <div class="form-group mb-4">
                        <label class="form-label">Select Categories</label>
                        <select name="categories[]" id="select2-categories" class="form-control" multiple="multiple" required>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id']; ?>"
                                    <?= in_array($category['id'], $selectedCategories) ? 'selected' : ''; ?>>
                                    <?= esc($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for category selection
        $('#select2-categories').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select categories',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
<?= $this->endSection(); ?>