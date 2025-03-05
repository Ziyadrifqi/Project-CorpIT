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
                <form action="" method="get" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Periode</label>
                                <?php $selectedMonth = $_GET['month'] ?? date('Y-m'); ?>
                                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="sign" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Periode</th>
                                <th>Document</th>
                                <th>Signed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($pdfs as $pdf): ?>
                                <tr class="text-center">
                                    <td><?= $i++; ?></td>
                                    <td><?= esc($pdf['display_periode']); ?></td>
                                    <td><?= esc($pdf['display_name']); ?></td>
                                    <td><?= date('d M Y', strtotime($pdf['created_at'])); ?></td>
                                    <td>
                                        <?php if ($pdf['file_exists']): ?>
                                            <button class="btn btn-sm btn-info view-pdf" data-file="<?= $pdf['file_path']; ?>" data-toggle="modal" data-target="#pdfModal">
                                                <i class="fas fa-file-pdf"></i> View
                                            </button>
                                            <a href="<?= $pdf['file_path']; ?>" class="btn btn-sm btn-success" download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        <?php else: ?>
                                            <span class="badge badge-danger">File Not Found</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pdfs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No documents found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">View PDF</h5>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".view-pdf").forEach(function(button) {
            button.addEventListener("click", function() {
                var filePath = this.getAttribute("data-file");
                document.getElementById("pdfViewer").src = filePath;
            });
        });
    });
</script>

<?= $this->endSection() ?>