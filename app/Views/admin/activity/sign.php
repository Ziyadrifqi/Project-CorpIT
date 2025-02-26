<?= $this->extend('layout/index') ?>
<?= $this->section('page-content') ?>
<div class="container mt-4">
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
                                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Select User</label>
                                <select name="user" class="form-control" id="select2-user">
                                    <option value="all" <?= ($selectedUser === 'all') ? 'selected' : '' ?>>All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= ($selectedUser == $user['id']) ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="pdfTable">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>User</th>
                                <th>Periode</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($pdfs as $pdf): ?>
                                <tr class="text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($pdf['username']) ?></td>
                                    <td><?= esc($pdf['display_periode']) ?></td>
                                    <td><?= date('d M Y', strtotime($pdf['created_at'])) ?></td>
                                    <td>
                                        <?php if ($pdf['file_exists']): ?>
                                            <a href="<?= esc($pdf['file_path']) ?>" class="btn btn-sm btn-info preview-btn" target="_blank">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                        <?php else: ?>
                                            <span class="text-danger">File not found</span>
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

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#select2-user').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
<?= $this->endSection() ?>