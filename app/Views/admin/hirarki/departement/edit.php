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
                Edit Departement Form
                <a href="<?= base_url('admin/hirarki/departement'); ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Departements
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
                    <!-- Form Edit -->
                    <form action="<?= base_url('admin/hirarki/departement/update/' . $departement['id']); ?>" method="post">
                        <?= csrf_field(); ?>

                        <div class="form-group">
                            <label for="name">Departement Name</label>
                            <input type="text" class="form-control <?= (session('errors.name')) ? 'is-invalid' : ''; ?>"
                                id="name" name="name"
                                value="<?= old('name', $departement['name']); ?>"
                                placeholder="Enter departement name">
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.name') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="division_id" class="form-label">Division</label>
                            <select class="form-control" id="division_id" name="division_id" required>
                                <option value="">Select Division</option>
                                <?php foreach ($division as $div): ?>
                                    <option value="<?= $div['id'] ?>" <?= old('division_id', $departement['division_id']) == $div['id'] ? 'selected' : '' ?>>
                                        <?= esc($div['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <small class="form-text text-muted">Select the appropriate division to which this departement belongs.</small>
                        </div>
                        <div class="text-end">
                            <button href="<?= base_url('admin/hirarki/departement'); ?>" type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>