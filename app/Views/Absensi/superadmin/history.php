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
                                <?php
                                $selectedMonth = $_GET['month'] ?? date('Y-m');
                                ?>
                                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Select Category</label>
                                <?php
                                $selectedCategory = $_GET['category'] ?? '';
                                ?>
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Select User</label>
                                <select name="user" class="form-control" id="select2-absen">
                                    <option value="all" <?= ($selectedUser === null || $selectedUser === 'all') ? 'selected' : '' ?>>All Users</option>
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

                <!-- Export Buttons -->
                <div class="mb-3 d-flex justify-content-end gap-2">
                    <a href="<?= base_url('absensi/exportPdfsuper') . '?' . http_build_query([
                                    'month' => $selectedMonth,
                                    'category' => $selectedCategory,
                                    'user' => $selectedUser
                                ]) ?>"
                        class="btn btn-danger"
                        title="<?= empty($data) ? 'No data available to export' : 'Export to PDF' ?>">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="<?= base_url('absensi/superadminExportExcel') . '?' . http_build_query([
                                    'month' => $selectedMonth,
                                    'category' => $selectedCategory,
                                    'user' => $selectedUser
                                ]) ?>"
                        class="btn btn-success"
                        title="<?= empty($data) ? 'No data available to export' : 'Export to Excel' ?>">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="superadminHistoryAbsen">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Activity Title</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>End Date</th>
                                <th>Total Hours</th>
                                <th>Diary Activity</th>
                                <th>Ticket Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($absensi)) : ?>
                                <?php $i = 1; ?>
                                <?php foreach ($absensi as $item): ?>
                                    <tr>
                                        <td scope="row"><?= $i++ ?></td>
                                        <td><?= esc($item['user_name']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                        <td><?= esc($item['category_name']) ?></td>
                                        <td><?= esc($item['judul_kegiatan']) ?></td>
                                        <td><?= $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-' ?></td>
                                        <td><?= $item['jam_keluar'] ? date('H:i', strtotime($item['jam_keluar'])) : '-' ?></td>
                                        <td><?= $item['tanggal_keluar'] ? date('d/m/Y', strtotime($item['tanggal_keluar'])) : '-' ?></td>
                                        <td><?= $item['total_jam'] ?? '-' ?></td>
                                        <td><?= nl2br(esc($item['kegiatan_harian'])) ?></td>
                                        <td class="text-center"><?= esc($item['no_tiket']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">There is no absenteeism data for this month</td>
                                </tr>
                            <?php endif; ?>
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
        $('#select2-absen').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
<?= $this->endSection(); ?>