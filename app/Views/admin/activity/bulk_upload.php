<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-upload"></i> Bulk Upload Activities
            </h3>
            <a href="<?= base_url('admin/activity') ?>" class="text-primary float-end" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Activities
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <?php foreach (session('errors') as $error) : ?>
                        <p><?= $error ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?php if (session()->has('error')) : ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
            <?php endif ?>

            <div class="alert alert-info">
                <strong>Instructions:</strong>
                <ul>
                    <li>Upload an Excel file (.xlsx, .xls) with activities</li>
                    <li>Columns must be in this order: NIK, Pemberi Tugas, No Ticket, Date, Task, Location, Start Time, End Time, Description</li>
                    <li>All columns except Description are mandatory</li>
                    <li>Date should be in a recognized date format (e.g., YYYY-MM-DD)</li>
                    <li>Time should be in HH:MM 24-hour format</li>
                </ul>
            </div>

            <form action="<?= base_url('admin/activity/process-bulk-upload') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="excel_file" class="form-label">Select Excel File</label>
                    <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                </div>
                <div class="text-center">
                    <a href="<?= base_url('img/lembur.xlsx') ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Activities
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>