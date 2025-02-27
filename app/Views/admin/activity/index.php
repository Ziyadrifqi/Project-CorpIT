<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Overtime Activity Manual</h3>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        Overtime Activity Manual List
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
                        <div class="d-flex gap-2">
                            <!-- Existing buttons -->
                            <a href="<?= base_url('admin/activity/bulk-upload') ?>" class="btn btn-info">
                                <i class="fas fa-file-upload"></i> Bulk Upload
                            </a>
                        </div>
                        <a href="<?= base_url('admin/activity/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Activity
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end gap-2">
                    <!-- Changed Export PDF to Preview PDF button -->
                    <button id="previewPdfBtn" class="btn btn-danger"
                        data-month="<?= $selectedMonth ?>"
                        data-year="<?= $selectedYear ?>"
                        <?= empty($activities) ? 'disabled' : '' ?>
                        title="<?= empty($activities) ? 'No data available to preview' : 'Preview PDF' ?>">
                        <i class="fas fa-file-pdf"></i> Preview PDF
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="adminactivity">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Date</th>
                                <th>Task</th>
                                <th>Description</th>
                                <th>Pemberi Tugas</th>
                                <th>No. Ticket</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Total Lembur</th>
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
                                    <td><?= esc($activity['pbr_tugas']) ?></td>
                                    <td><?= esc($activity['no_tiket']) ?></td>
                                    <td><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                                    <td><?= date('H:i', strtotime($activity['end_time'])) ?></td>
                                    <td><?= $activity['total_lembur'] ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/activity/edit/' . $activity['id']) ?>"
                                                class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="<?= base_url('admin/activity/delete/' . esc($activity['id'])); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this activity?')">
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
</div>

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewModalLabel">PDF Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="pdfPreviewFrame" style="width:100%; height:75vh;" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadPdfBtn" class="btn btn-primary" download> <i class="fas fa-download"></i> Download PDF</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for PDF Preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the PDF preview button
        const previewPdfBtn = document.getElementById('previewPdfBtn');

        if (previewPdfBtn) {
            previewPdfBtn.addEventListener('click', function() {
                // Get the month and year from data attributes
                const month = this.getAttribute('data-month');
                const year = this.getAttribute('data-year');

                // Show loading
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;

                // Make the AJAX request
                fetch(`<?= base_url('admin/activity/previewPdf') ?>?month=${month}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        // Reset button
                        previewPdfBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Preview PDF';
                        previewPdfBtn.disabled = false;

                        if (!data.success) {
                            alert(data.message || 'No data available for the selected period');
                            return;
                        }

                        // Set up the PDF viewer
                        const pdfData = data.pdf;
                        const pdfBlob = base64ToBlob(pdfData, 'application/pdf');
                        const pdfUrl = URL.createObjectURL(pdfBlob);

                        // Set the iframe source
                        document.getElementById('pdfPreviewFrame').src = pdfUrl;

                        // Set up the download button
                        const downloadBtn = document.getElementById('downloadPdfBtn');
                        downloadBtn.href = pdfUrl;
                        downloadBtn.download = data.filename;

                        // Show the modal - Make sure you're using the Bootstrap 5 way to initialize modals
                        var myModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                        myModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching PDF:', error);
                        previewPdfBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Preview PDF';
                        previewPdfBtn.disabled = false;
                        alert('Failed to load PDF preview. Please try again.');
                    });
            });
        }

        // Helper function to convert base64 to Blob
        function base64ToBlob(base64, contentType) {
            contentType = contentType || '';
            const sliceSize = 1024;
            const byteCharacters = atob(base64);
            const byteArrays = [];

            for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                const slice = byteCharacters.slice(offset, offset + sliceSize);
                const byteNumbers = new Array(slice.length);

                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            return new Blob(byteArrays, {
                type: contentType
            });
        }
    });
</script>
<?= $this->endSection() ?>