<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($title); ?></h3>
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
                My Guest Visitor List
                <a href="<?= base_url('guest-visitor/create'); ?>" class="btn btn-primary float-end">
                    <i class="fas fa-plus"></i> Create New Visitor
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="Guest_visitor">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Guest Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Valid Until</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($guests as $guest): ?>
                                <?php
                                $isExpired = strtotime($guest['valid_until']) < time();
                                $statusClass = $isExpired ? 'bg-danger' : ($guest['status'] ? 'bg-success' : 'bg-danger');
                                $statusText = $isExpired ? 'Inactive' : ($guest['status'] ? 'Active' : 'Inactive');
                                ?>
                                <tr class="text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($guest['guest_name']) ?></td>
                                    <td><?= esc($guest['email']) ?></td>
                                    <td><?= esc($guest['phone']) ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $statusText ?>
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
            }, 5000);
        });
    });
</script>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<?= $this->endSection(); ?>