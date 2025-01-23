<?= $this->extend('layout/index') ?>

<?= $this->section('page-content') ?>
<div class="container mt-4">
    <!-- Card Header -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($title); ?></h3>
    </div>

    <!-- Card Body -->
    <div class="card-body">
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header">
                <!-- Filter Form -->
                <form action="" method="get" class="mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Month</label>
                                <?php $selectedMonth = $_GET['month'] ?? date('Y-m'); ?>
                                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Category</label>
                                <?php $selectedCategory = $_GET['category'] ?? ''; ?>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="card-body">
                <!-- Export Buttons -->
                <div class="mb-3 d-flex justify-content-end gap-2">

                    <a href="<?= base_url('absensi/exportPdf') ?>?month=<?= $selectedMonth ?>&category=<?= $selectedCategory ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
                    <a href="<?= base_url('absensi/exportExcel') ?>?month=<?= $selectedMonth ?>&category=<?= $selectedCategory ?>" class="btn btn-success"> <i class="fas fa-file-excel"></i> Export Excel</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="historyabsen">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Start Date</th>
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
                                        <td><?= $i++ ?></td>
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
                                    <td colspan="10" class="text-center">There is no absenteeism data for this month</td>
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
<?= $this->endSection(); ?>