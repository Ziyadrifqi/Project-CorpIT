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
                    <button id="previewPdfBtn" class="btn btn-danger"
                        data-category="<?= $selectedCategory ?? '' ?>"
                        data-month="<?= $selectedMonth ?? date('Y-m') ?>"
                        <?= empty($absensi) ? 'disabled' : '' ?>
                        title="<?= empty($absensi) ? 'No data available to preview' : 'Preview PDF' ?>">
                        <i class="fas fa-file-pdf"></i> Preview PDF
                    </button>
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
<!-- Add the PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewModalLabel">Attendance Report PDF Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="pdfPreviewFrame" style="width:100%; height:75vh;" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadPdfBtn" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the PDF preview button
        const previewPdfBtn = document.getElementById('previewPdfBtn');

        if (previewPdfBtn) {
            previewPdfBtn.addEventListener('click', function() {
                // Get filter parameters from the button's data attributes
                const month = this.getAttribute('data-month');
                const category = this.getAttribute('data-category');

                // Show loading
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
                this.disabled = true;

                // Make the AJAX request
                fetch(`<?= base_url('absensi/preview') ?>?month=${month}&category=${category}`)
                    .then(response => response.json())
                    .then(data => {
                        // Reset button
                        previewPdfBtn.innerHTML = '<i class="bi bi-file-pdf"></i> Preview PDF';
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

                        // Show the modal
                        var myModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                        myModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching PDF:', error);
                        previewPdfBtn.innerHTML = '<i class="bi bi-file-pdf"></i> Preview PDF';
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
<?= $this->endSection(); ?>