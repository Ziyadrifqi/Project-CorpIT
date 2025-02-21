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
                <h5 class="modal-title">Preview PDF</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-1by1">
                    <iframe id="pdfViewer" class="embed-responsive-item"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="signPdfBtn">
                    <i class="fas fa-signature"></i> Sign
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        // Initialize Select2 if jQuery and Select2 are available
        if (window.jQuery && $.fn.select2) {
            $('#select2-activity').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Select User',
                allowClear: true,
                closeOnSelect: false
            });
        }

        const previewPdfBtn = document.getElementById('previewPdfBtn');
        const pdfPreviewModal = document.getElementById('pdfPreviewModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const signPdfBtn = document.getElementById('signPdfBtn');
        let modalInstance = null;

        // Function untuk menampilkan loading
        function showLoading() {
            if (pdfViewer) {
                pdfViewer.style.display = 'none';
            }
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'loadingIndicator';
            loadingDiv.className = 'text-center p-4';
            loadingDiv.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="mt-2">Processing...</div>
        `;
            pdfViewer.parentNode.insertBefore(loadingDiv, pdfViewer);
        }

        // Function untuk menyembunyikan loading
        function hideLoading() {
            const loadingDiv = document.getElementById('loadingIndicator');
            if (loadingDiv) {
                loadingDiv.remove();
            }
            if (pdfViewer) {
                pdfViewer.style.display = 'block';
            }
        }

        // Function untuk load preview PDF
        async function loadPdfPreview(signed = false) {
            try {
                showLoading();

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

                // Convert base64 to blob and create URL
                const blobData = atob(data.pdfData);
                const arrayBuffer = new ArrayBuffer(blobData.length);
                const uint8Array = new Uint8Array(arrayBuffer);

                for (let i = 0; i < blobData.length; i++) {
                    uint8Array[i] = blobData.charCodeAt(i);
                }

                const blob = new Blob([uint8Array], {
                    type: 'application/pdf'
                });
                const blobUrl = URL.createObjectURL(blob);

                // Update iframe source
                if (pdfViewer) {
                    pdfViewer.src = blobUrl;
                }

                // Show modal if not already shown
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(pdfPreviewModal);
                }
                modalInstance.show();

            } catch (error) {
                console.error('Preview error:', error);
                alert('Error: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Function untuk sign PDF
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

                // Disable sign button
                if (signPdfBtn) {
                    signPdfBtn.disabled = true;
                }

                // Reload preview with signed PDF
                await loadPdfPreview(true);

                alert('Document signed successfully!');

            } catch (error) {
                console.error('Signing error:', error);
                alert('Error: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Event listeners
        if (previewPdfBtn) {
            previewPdfBtn.addEventListener('click', () => loadPdfPreview(false));
        }

        if (signPdfBtn) {
            signPdfBtn.addEventListener('click', signPdf);
        }

        // Cleanup when modal is closed
        if (pdfPreviewModal) {
            pdfPreviewModal.addEventListener('hidden.bs.modal', function() {
                if (pdfViewer && pdfViewer.src) {
                    URL.revokeObjectURL(pdfViewer.src);
                    pdfViewer.src = '';
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?>