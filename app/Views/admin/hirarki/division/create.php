<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="container mt-4">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>
                New Divisions Form
                <a href="<?= base_url('admin/hirarki/division'); ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Divisions
                </a>
            </div>
            <div class="card-body">
                <!-- Tampilkan error validasi -->
                <?php if (session()->has('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <!-- Form Create -->
                    <form action="<?= base_url('admin/hirarki/division/store'); ?>" method="post">
                        <?= csrf_field(); ?>

                        <div class="form-group">
                            <label for="name">Division Name</label>
                            <input type="text" class="form-control <?= (session('errors.name')) ? 'is-invalid' : ''; ?>"
                                id="name" name="name" value="<?= old('name'); ?>"
                                placeholder="Enter division name">
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.name') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="directorate_id" class="form-label">Directorate</label>
                            <select class="form-control" id="directorate_id" name="directorate_id" required>
                                <option value="">Select Directorate</option>
                                <?php foreach ($directorate  as $dir): ?>
                                    <option value="<?= $dir['id'] ?>" <?= old('directorate_id') == $dir['id'] ? 'selected' : '' ?>>
                                        <?= esc($dir['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <small class="form-text text-muted">Select the appropriate directorate to which this division belongs.</small>
                        </div>
                        <div class="text-end">
                            <button href="<?= base_url('admin/hirarki/division'); ?>type=" submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
<?= $this->endSection() ?>