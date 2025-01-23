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
                <i class="fas fa-exclamation-circle"></i><?= esc(session('error')) ?>
            </div>
        <?php endif ?>

        <div class="card mb-4">
            <div class="card-header">
                <!-- Filter Form -->
                <form action="<?= base_url('activity/history') ?>" method="get">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-control">
                                    <?php
                                    for ($m = 1; $m <= 12; $m++) {
                                        $selected = ($selectedMonth == $m) ? 'selected' : '';
                                        echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-control">
                                    <?php
                                    $currentYear = date('Y');
                                    for ($y = $currentYear; $y >= $currentYear - 2; $y--) {
                                        $selected = ($selectedYear == $y) ? 'selected' : '';
                                        echo "<option value='$y' $selected>$y</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Select User</label>
                                <select name="user" class="form-control" id="select2-activity">
                                    <option value="all" <?= ($selectedUser === null) ? 'selected' : '' ?>>All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= ($selectedUser == $user['id']) ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/activity/exportsuper/pdf') . '?' . http_build_query(['month' => $selectedMonth, 'year' => $selectedYear, 'user' => $selectedUser]) ?>" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                    <a href="<?= base_url('admin/activity/exportsuper/excel') . '?' . http_build_query(['month' => $selectedMonth, 'year' => $selectedYear, 'user' => $selectedUser]) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="historyactivity">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Task</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td scope="row" class="text-center"><?= $i++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($activity['activity_date'])) ?></td>
                                    <td><?= esc($activity['username']) ?></td>
                                    <td><?= esc($activity['task']) ?></td>
                                    <td><?= esc($activity['description']) ?></td>
                                    <td><?= esc($activity['location']) ?></td>
                                    <td><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                                    <td><?= date('H:i', strtotime($activity['end_time'])) ?></td>
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
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for category selection
        $('#select2-activity').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
<?= $this->endSection(); ?>