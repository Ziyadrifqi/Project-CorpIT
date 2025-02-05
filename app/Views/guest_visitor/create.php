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
                New Guest Visitor Form
                <a href="<?= base_url('guest-visitor') ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Guest List
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('guest-visitor/save') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Guest name will be automatically generated in format: GUEST[DATE]_[NUMBER]
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notify" name="notify" checked>
                            <label class="form-check-label" for="notify">
                                Send email notification
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" checked>
                            <label class="form-check-label" for="is_enabled">
                                Enable Account
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="valid_till_days" class="form-label">Valid for (days)</label>
                        <input type="number" class="form-control" id="valid_till_days" name="valid_till_days" value="5" min="1" max="365">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Guest</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Creating guest visitor...</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    document.querySelector('form').addEventListener('submit', function() {
        new bootstrap.Modal(document.getElementById('loadingModal')).show();
    });
</script>
<?= $this->endSection(); ?>