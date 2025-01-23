<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Activities</h3>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        Activity List
                    </div>
                    <div class="d-flex gap-2">
                        <form action="<?= base_url('admin/activity') ?>" method="get" class="d-flex gap-2">
                            <select name="month" class="form-select">
                                <?php
                                for ($m = 1; $m <= 12; $m++) {
                                    $selected = ($selectedMonth == $m) ? 'selected' : '';
                                    echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                }
                                ?>
                            </select>
                            <select name="year" class="form-select">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 4; $y--) {
                                    $selected = ($selectedYear == $y) ? 'selected' : '';
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                        <a href="<?= base_url('admin/activity/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Activity
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/activity/export/pdf') . '?' . http_build_query(['month' => $selectedMonth, 'year' => $selectedYear]) ?>"
                        class="btn btn-danger"
                        title="<?= empty($activities) ? 'No data available to export' : 'Export to PDF' ?>">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                    <a href="<?= base_url('admin/activity/export/excel') . '?' . http_build_query(['month' => $selectedMonth, 'year' => $selectedYear]) ?>"
                        class="btn btn-success"
                        title="<?= empty($activities) ? 'No data available to export' : 'Export to Excel' ?>">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>

                <table class="table table-sm table-bordered" id="adminactivity">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td scope="row" class="text-center"><?= $i++ ?></td>
                                <td><?= date('d/m/Y', strtotime($activity['activity_date'])) ?></td>
                                <td><?= esc($activity['task']) ?></td>
                                <td><?= esc($activity['description']) ?></td>
                                <td><?= esc($activity['location']) ?></td>
                                <td><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                                <td><?= date('H:i', strtotime($activity['end_time'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/activity/edit/' . $activity['id']) ?>" class="btn btn-xs btn-warning " title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="<?= base_url('admin/activity/delete/' . esc($activity['id'])); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this activity?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>