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
                    <?php if (!empty($selectedMonth) && !empty($selectedYear) && !empty($selectedUser)): ?>
                        <button type="button" class="btn btn-primary" id="previewPdfBtn">
                            <i class="fas fa-eye"></i> Preview PDF
                        </button>
                    <?php endif; ?>
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
                                <th>No Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activities)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No data available. Please apply filters.</td>
                                </tr>
                            <?php else: ?>
                                <?php $i = 1; ?>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td><?= date('d/m/Y', strtotime($activity['activity_date'])) ?></td>
                                        <td><?= esc($activity['username']) ?></td>
                                        <td><?= esc($activity['task']) ?></td>
                                        <td><?= esc($activity['description']) ?></td>
                                        <td><?= esc($activity['location']) ?></td>
                                        <td><?= esc($activity['start_time']) ?></td>
                                        <td><?= esc($activity['end_time']) ?></td>
                                        <td><?= esc($activity['no_tiket']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pdfPreviewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="signPdfBtn">
                    <i class="fas fa-signature"></i> Sign PDF
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('#select2-activity').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });

        const pdfPreviewModal = document.getElementById('pdfPreviewModal');
        const pdfViewer = document.getElementById('pdfPreviewContent');
        const signPdfBtn = document.getElementById('signPdfBtn');

        // Function to show loading indicator
        function showLoading() {
            if (pdfViewer) {
                pdfViewer.innerHTML = `
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="mt-2">Processing...</div>
                    </div>
                `;
            }
        }

        // Function to preview PDF
        async function loadPdfPreview(signed = false) {
            try {
                showLoading();
                $(pdfPreviewModal).modal('show');

                const month = document.querySelector('select[name="month"]').value;
                const year = document.querySelector('select[name="year"]').value;
                const user = document.querySelector('select[name="user"]').value;

                const queryParams = new URLSearchParams({
                    month: month,
                    year: year,
                    user: user,
                    signed: signed
                });

                const response = await fetch(`${window.location.origin}/admin/activity/exportsuper/pdf?${queryParams}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to generate PDF');
                }

                // Create PDF viewer iframe
                const iframe = document.createElement('iframe');
                iframe.style.width = '100%';
                iframe.style.height = '600px';
                iframe.src = `data:application/pdf;base64,${data.pdfData}`;

                // Update modal content
                if (pdfViewer) {
                    pdfViewer.innerHTML = '';
                    pdfViewer.appendChild(iframe);
                }

            } catch (error) {
                console.error('Preview error:', error);
                alert('Error: ' + error.message);
                $(pdfPreviewModal).modal('hide');
            }
        }

        async function signPdf() {
            try {
                showLoading();

                const month = document.querySelector('select[name="month"]').value;
                const year = document.querySelector('select[name="year"]').value;
                const user = document.querySelector('select[name="user"]').value;

                const queryParams = new URLSearchParams({
                    month: month,
                    year: year,
                    user: user
                });

                const response = await fetch(`/admin/activity/sign?${queryParams}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message);
                }

                // Create temporary link for auto-download
                const downloadLink = document.createElement('a');
                downloadLink.href = data.downloadUrl;
                downloadLink.download = data.downloadUrl.split('/').pop();
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

                // Show success message immediately before closing modal
                alert('Document signed successfully!');

                // Close modal after a short delay
                setTimeout(() => {
                    $('#pdfPreviewModal').modal('hide');
                }, 500);

            } catch (error) {
                console.error('Signing error:', error);
                alert('Error: ' + error.message);
            }
        }


        // Event listeners
        if (signPdfBtn) {
            signPdfBtn.addEventListener('click', signPdf);
        }

        // Event listener for preview button (if exists)
        const previewPdfBtn = document.getElementById('previewPdfBtn');
        if (previewPdfBtn) {
            previewPdfBtn.addEventListener('click', () => loadPdfPreview(false));
        }

        // Cleanup when modal is closed
        $(pdfPreviewModal).on('hidden.bs.modal', function() {
            if (pdfViewer) {
                pdfViewer.innerHTML = '';
            }
        });

        // Make preview function available globally
        window.loadPdfPreview = loadPdfPreview;
    });
</script>
<?= $this->endSection() ?>