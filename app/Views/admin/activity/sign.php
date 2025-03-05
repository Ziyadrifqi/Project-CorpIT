<?= $this->extend('layout/index') ?>
<?= $this->section('page-content') ?>
<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($title); ?></h3>
    </div>
    <div class="card-body">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success" id="alert-success">
                <i class="fas fa-check-circle"></i> <?= esc(session('success')) ?>
            </div>
        <?php endif ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger" id="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= esc(session('error')) ?>
            </div>
        <?php endif ?>
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
                                            <?= esc($user['fullname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <?php if ($selectedUser === 'all'): ?>
                                    <a href="<?= base_url('admin/signpdf/downloadPdfsZip') . '?month=' . $selectedMonth . '&user=' . $selectedUser ?>" class="btn btn-success">
                                        <i class="fas fa-download"></i> Download Full PDF
                                    </a>
                                <?php endif; ?>
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
                                <th>Signed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($pdfs as $pdf): ?>
                                <tr class="text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($pdf['fullname']) ?></td>
                                    <td><?= esc($pdf['display_periode']) ?></td>
                                    <td><?= date('d M Y', strtotime($pdf['created_at'])) ?></td>
                                    <td>
                                        <?php if ($pdf['file_exists']): ?>
                                            <button class="btn btn-sm btn-info preview-btn" data-file="<?= esc($pdf['file_path']) ?>">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
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

<!-- Modal for PDF Preview -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">PDF Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#select2-user').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });

        // PDF Preview Modal Handler
        $('.preview-btn').on('click', function() {
            var filePath = $(this).data('file');
            $('#pdfViewer').attr('src', filePath);
            $('#pdfModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>