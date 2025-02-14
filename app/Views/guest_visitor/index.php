<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Guest Visitor Management</h3>
    </div>
    <div class="card-body">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success" id="alert-success">
                <i class="fas fa-check-circle"></i><?= esc(session('success')) ?>
            </div>
        <?php endif ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger" id="alert-error">
                <i class="fas fa-exclamation-circle"><?= esc(session('error')) ?>
            </div>
        <?php endif ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
                <a href="<?= base_url('guest-visitor/create'); ?>" class="btn btn-primary float-end"><i class="fas fa-plus"></i> Create New Visitor</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="Guest_visitor">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Guest Name</th>
                                <th>Phone</th>
                                <th>Password</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Valid_until</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($guests as $guest): ?>
                                <tr class="text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($guest['guest_name']) ?></td>
                                    <td><?= esc($guest['phone']) ?></td>
                                    <td><?= esc($guest['password']) ?></td>
                                    <td>
                                        <span class="badge <?= $guest['status'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $guest['status'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= esc($guest['created_at']) ?></td>
                                    <td><?= esc($guest['valid_until']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000); // 5 seconds
        });
    });
</script>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<?= $this->endSection(); ?>