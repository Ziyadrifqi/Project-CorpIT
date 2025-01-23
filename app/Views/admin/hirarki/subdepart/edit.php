<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<div class="card shadow-sm">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
            <div class="card-tools">
                <!-- Stylish link to go back to roles list -->
                <a href="<?= base_url('admin/hirarki/subdepart'); ?>" class="text-primary mb-3" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Sub Departements
                </a>
            </div>
        </div>
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

        <!-- Form Edit -->
        <form action="<?= base_url('admin/hirarki/subdepart/update/' . $subdepart['id']); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name">Sub Departement Name</label>
                    <input type="text" class="form-control <?= (session('errors.name')) ? 'is-invalid' : ''; ?>"
                        id="name" name="name"
                        value="<?= old('name', $subdepart['name']); ?>"
                        placeholder="Enter sub departement name">
                    <?php if (session('errors.name')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.name') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="department_id" class="form-label">Departement</label>
                    <select class="form-control" id="department_id" name="department_id" required>
                        <option value="">Select Departement</option>
                        <?php foreach ($departement as $dep): ?>
                            <option value="<?= $dep['id'] ?>" <?= old('department_id', $subdepart['department_id']) == $dep['id'] ? 'selected' : '' ?>>
                                <?= esc($dep['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <small class="form-text text-muted">Select the appropriate division to which this departement belongs.</small>
                </div>
            </div>
            <div class="mt-3">
                <button href="<?= base_url('admin/hirarki/departement'); ?>" type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>