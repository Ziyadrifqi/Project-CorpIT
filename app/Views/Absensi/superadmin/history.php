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
                <div class="mb-3 d-flex justify-content-end">
                    <?php if (!empty($absensi) && $selectedUser !== 'all' && $selectedUser !== null): ?>
                        <button type="button" class="btn btn-primary" onclick="previewPdf('<?= $selectedUser ?>', '<?= $selectedMonth ?>')">
                            <i class="fas fa-eye"></i> Preview PDF
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="superadminHistory">
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
                <button type="button" class="btn btn-primary" id="signPdfBtn"
                    data-user-id="<?= user_id() ?>"
                    data-signature="<?= $userDetails->signature ?? '' ?>">
                    <i class="fas fa-signature"></i> Sign PDF
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<!-- Di bagian scripts -->

<?= $this->section('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('#select2-absen').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select User',
            allowClear: true,
            closeOnSelect: false
        });

        const pdfPreviewModal = document.getElementById('pdfPreviewModal');
        const pdfViewer = document.getElementById('pdfPreviewContent');

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

        // Modify your previewPdf function
        async function previewPdf(userId, month, signed = false) {
            try {
                showLoading();
                $(pdfPreviewModal).modal('show');

                const response = await fetch(
                    `${window.location.origin}/absensi/previewPdf?userId=${userId}&month=${month}&signed=${signed}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

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

        // Function to sign PDF
        async function signPdf(userId, month) {
            try {
                showLoading();

                const response = await fetch(`${window.location.origin}/absensi/signPdf`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: userId,
                        month: month
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to sign PDF');
                }

                // After successful signing, show the signed PDF
                await previewPdf(userId, month, true);
                alert('Document signed successfully!');

            } catch (error) {
                console.error('Signing error:', error);
                alert('Error: ' + error.message);
            }
        }

        // Event listener for sign button
        document.getElementById('signPdfBtn')?.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const month = document.querySelector('input[name="month"]').value;

            if (!userId || !month) {
                alert('Missing required parameters');
                return;
            }

            signPdf(userId, month);
        });

        // Event listener for modal close
        $(pdfPreviewModal).on('hidden.bs.modal', function() {
            if (pdfViewer) {
                pdfViewer.innerHTML = '';
            }
        });

        // Make previewPdf function available globally
        window.previewPdf = previewPdf;
    });
</script>
<?= $this->endSection() ?>