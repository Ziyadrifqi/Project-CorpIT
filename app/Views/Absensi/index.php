<?= $this->extend('layout/index') ?>

<?= $this->section('page-content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="text-center mb-0 fw-bold text-primary">Daily Attendance</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h6 class="text-muted mb-2">Date: <?= date('d F Y') ?></h6>
                        <h5 class="fw-bold"><span id="jam" class="text-primary"><?= date('H:i:s') ?></span></h5>
                    </div>

                    <?php if (isset($absensi) && $absensi): ?>
                        <!-- Information Panel -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Category:
                                        <span class="text-dark"><?= isset($absensi['category_name']) ? esc($absensi['category_name']) : 'No category' ?></span>
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2">Activity Title:
                                        <span class="text-dark"><?= esc($absensi['judul_kegiatan']) ?></span>
                                    </h6>
                                </div>
                            </div>
                            <?php if ($absensi['tanggal'] < date('Y-m-d')): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    This attendance is from <?= date('d F Y', strtotime($absensi['tanggal'])) ?>. Please complete the tap out process.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Display -->
                        <div class="text-center mb-4">
                            <div class="d-inline-block px-4 py-2 rounded-pill
                                <?= $absensi['status'] === 'pending' ? 'bg-warning' : ($absensi['status'] === 'hadir' ? 'bg-info' : 'bg-success') ?> text-white">
                                Status: <strong><?= ucfirst($absensi['status']) ?></strong>
                            </div>

                            <?php if ($absensi['tanggal'] < date('Y-m-d') && $absensi['status'] === 'hadir'): ?>
                                <div class="mt-2">
                                    <span class="badge bg-danger">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Pending Tap Out from <?= date('d F Y', strtotime($absensi['tanggal'])) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($absensi['jam_masuk']): ?>
                                <p class="mt-3 mb-1">Start Time: <span class="fw-bold"><?= $absensi['jam_masuk'] ?></span></p>
                            <?php endif; ?>
                            <?php if ($absensi['jam_keluar']): ?>
                                <p class="mb-0">End Time: <span class="fw-bold"><?= $absensi['jam_keluar'] ?></span></p>
                                <p class="mb-0">Total Duration: <span class="fw-bold"><?= $absensi['total_jam'] ?></span></p>
                                <p class="mb-0">Start Date: <span class="fw-bold"><?= $absensi['tanggal'] ?></span></p>
                                <p class="mb-0">End Date: <span class="fw-bold"><?= $absensi['tanggal_keluar'] ?></span></p>
                            <?php endif; ?>
                        </div>

                        <!-- Tap In Section -->
                        <?php if ($absensi['status'] == 'pending'): ?>
                            <div class="text-center mb-4">
                                <p class="text-danger fw-bold">Please Tap In First</p>
                                <button id="tapIn" class="btn btn-primary btn-lg px-4 rounded-pill">
                                    <i class="bi bi-box-arrow-in-right"></i>Tap In
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Activities Form -->
                        <?php if ($absensi['status'] == 'hadir' && (empty($absensi['kegiatan_harian']) || empty($absensi['no_tiket']))): ?>
                            <div class="mb-4">
                                <form id="kegiatanForm" class="p-3 border rounded">
                                    <div class="mb-3">
                                        <label for="kegiatan_harian" class="form-label fw-bold">Daily Activities</label>
                                        <textarea class="form-control" id="kegiatan_harian" name="kegiatan_harian"
                                            rows="4" required placeholder="Enter your daily activities..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="no_tiket" class="form-label fw-bold">Ticket Number</label>
                                        <input type="text" class="form-control" id="no_tiket" name="no_tiket"
                                            required placeholder="Enter 6-digit ticket number"
                                            maxlength="6" pattern="^\d{6}$" title="Please enter exactly 6 digits">
                                        <div class="form-text text-muted">Must be exactly 6 digits</div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Submit Activities
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Display Submitted Activities -->
                        <?php if (!empty($absensi['kegiatan_harian']) && !empty($absensi['no_tiket'])): ?>
                            <div class="mb-4 p-3 border rounded">
                                <h6 class="fw-bold mb-3">Daily Activities:</h6>
                                <p class="text-muted mb-3"><?= nl2br(esc($absensi['kegiatan_harian'])) ?></p>
                                <h6 class="fw-bold mb-2">Ticket Number:</h6>
                                <p class="text-muted mb-0"><?= esc($absensi['no_tiket']) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Tap Out Section -->
                        <?php if ($absensi['status'] == 'hadir' && !empty($absensi['kegiatan_harian']) && !empty($absensi['no_tiket'])): ?>
                            <div class="text-center">
                                <?php if ($absensi['tanggal'] < date('Y-m-d')): ?>
                                    <button class="btn btn-warning btn-lg px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#correctTapOutModal">
                                        <i class="bi bi-clock-history me-2"></i>Correct Missed Tap Out
                                    </button>
                                <?php else: ?>
                                    <button id="tapOut" class="btn btn-danger btn-lg px-4 rounded-pill">
                                        <i class="bi bi-box-arrow-right"></i>Tap Out
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- New Attendance Button -->
                        <?php if ($absensi['status'] == 'pulang'): ?>
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-primary" id="startNewAttend">
                                    <i class="bi bi-plus-circle me-2"></i>Start New Attendance
                                </button>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modify the Correct Tap Out Modal -->
<div class="modal fade" id="correctTapOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Correct Missed Tap Out
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="correctTapOutForm">
                    <?php if (isset($absensi) && !empty($absensi)): ?>
                        <input type="hidden" name="absen_id" value="<?= $absensi['id'] ?? '' ?>">
                        <p class="text-muted mb-3">
                            Please enter the time and date you finished work on <?= date('d F Y', strtotime($absensi['tanggal'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted mb-3">No attendance record found.</p>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="tanggal_keluar" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar"
                            value="<?= isset($absensi) ? date('Y-m-d', strtotime($absensi['tanggal'])) : date('Y-m-d') ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="jam_keluar" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="jam_keluar" name="jam_keluar" required>
                        <div class="form-text">Enter the time in 24-hour format (HH:mm)</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Correction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Section -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Update clock
    setInterval(() => {
        const now = new Date();
        document.getElementById('jam').textContent = now.toLocaleTimeString('id-ID');
    }, 1000);

    // Helper function for displaying messages
    function displayMessage(data) {
        if (!data.status && data.message && typeof data.message === 'object') {
            const errorMessage = Object.values(data.message).join('\n');
            alert(errorMessage);
        } else {
            alert(data.message || 'An error occurred');
        }
    }

    // Tap In handler
    document.getElementById('tapIn')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to tap in?')) {
            fetch('<?= base_url('absensi/tapIn') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data);
                    if (data.status) location.reload();
                })
                .catch(error => {
                    alert('An error occurred while processing the request');
                    console.error('Error:', error);
                });
        }
    });

    // Activities form handler
    document.getElementById('kegiatanForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to save the activity?')) {
            fetch('<?= base_url('absensi/submitKegiatan') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data);
                    if (data.status) location.reload();
                })
                .catch(error => {
                    alert('An error occurred while processing the request');
                    console.error('Error:', error);
                });
        }
    });

    // Tap Out handler
    document.getElementById('tapOut')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to tap out?')) {
            fetch('<?= base_url('absensi/tapOut') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data);
                    if (data.status) location.reload();
                })
                .catch(error => {
                    alert('An error occurred while processing the request');
                    console.error('Error:', error);
                });
        }
    });

    // Modify the Correct Tap Out form handler
    document.getElementById('correctTapOutForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to correct this tap out time?')) {
            fetch('<?= base_url('absensi/correctTapOut') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data);
                    if (data.status) {
                        if (data.redirect) {
                            // Reload the page to show the information panel
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    alert('An error occurred while processing the request');
                    console.error('Error:', error);
                });
        }
    });
    document.getElementById('startNewAttend')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to start new attendance?')) {
            fetch('<?= base_url('absensi/start_new_attendance') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    displayMessage(data);
                    if (data.status) location.reload();
                })
                .catch(error => {
                    alert('An error occurred while processing the request');
                    console.error('Error:', error);
                });
        }
    });
</script>
<?= $this->endSection() ?>