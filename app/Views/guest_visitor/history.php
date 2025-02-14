<?= $this->extend('layout/index') ?>

<?= $this->section('page-content') ?>
<div class="container mt-4">
    <!-- Card Header -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($title); ?></h3>
    </div>
    <div class="card-body">
        <div class="card mb-4">
            <div class="card-header">
                <!-- Filter Form -->
                <form action="" method="get" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Select Month</label>
                                <input type="month" name="month" class="form-control"
                                    value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select User</label>
                                <select name="user" class="form-control" id="select2-history">
                                    <option value="all">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>"
                                            <?= ($selectedUser == $user['id']) ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- History Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="guestHistoryTable">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Guest Name</th>
                                <th>Email</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($guests as $guest): ?>
                                <tr class="text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($guest['guest_name']) ?></td>
                                    <td><?= esc($guest['email']) ?></td>
                                    <td><?= esc($guest['created_by']) ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($guest['created_at'])) ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($guest['valid_until'])) ?></td>
                                    <td>
                                        <?php if (strtotime($guest['valid_until']) > time()): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#guestHistoryTable').DataTable({
            "order": [
                [4, "desc"]
            ], // Sort by created date by default
            "pageLength": 25
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for category selection
            $('#select2-history').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Select User',
                allowClear: true,
                closeOnSelect: false
            });
        });
    });
</script>
<?= $this->endSection() ?>