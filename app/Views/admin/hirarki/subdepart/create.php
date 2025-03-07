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
                New Sub Departement Form
                <a href="<?= base_url('admin/hirarki/subdepart'); ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Sub Departements
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

                <!-- Form Create -->
                <form action="<?= base_url('admin/hirarki/subdepart/store'); ?>" method="post">
                    <?= csrf_field(); ?>

                    <div class="form-group">
                        <label for="name">Sub Departement Name</label>
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
                        <label for="department_id" class="form-label">Departement</label>
                        <select class="form-control" id="department_id" name="department_id" required>
                            <option value="">Select Departement</option>
                            <?php foreach ($departement  as $dep): ?>
                                <option value="<?= $dep['id'] ?>" <?= old('department_id') == $dep['id'] ? 'selected' : '' ?>>
                                    <?= esc($dep['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <small class="form-text text-muted">Select the appropriate Division to which this departement belongs.</small>
                    </div>
                    <div class="text-end">
                        <button href="<?= base_url('admin/hirarki/departement'); ?>" type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
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