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
                PJ Assignment List
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

                <!-- Button to open application selection modal -->
                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#aplikasiModal">
                    Pilih Aplikasi
                </button>

                <!-- PJ Time Alert -->
                <div id="pjTimeAlert" class="alert alert-warning mb-4" style="display: none;">
                    <i class="fas fa-clock"></i> Ada penugasan PJ yang lebih dari <span id="thresholdDays">7</span> hari. Harap segera perbarui!
                </div>

                <!-- PJ Status Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover" id="pjTable">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama Aplikasi</th>
                                <th>PJ Saat Ini</th>
                                <th>Waktu Penunjukan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $currentDate = new DateTime();
                            $hasOldAssignments = false;
                            $thresholdDays = 7; // Threshold in days for alert
                            ?>
                            <?php foreach ($grouped_pj_assignments as $app_name => $assignments) : ?>
                                <?php
                                // Sort assignments by created_at in descending order
                                usort($assignments, function ($a, $b) {
                                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                                });

                                // Get the most recent assignment
                                $latest_assignment = $assignments[0];

                                // Calculate days since assignment
                                $assignmentDate = new DateTime($latest_assignment['created_at']);
                                $daysDiff = $currentDate->diff($assignmentDate)->days;
                                $isOld = $daysDiff > $thresholdDays;

                                if ($isOld) {
                                    $hasOldAssignments = true;
                                }
                                ?>
                                <tr class="<?= $isOld ? 'table-warning' : '' ?> text-center">
                                    <td><?= $i++ ?></td>
                                    <td><?= $app_name ?></td>
                                    <td><?= $latest_assignment['nama_user'] ?></td>
                                    <td><?= date('d M Y H:i', strtotime($latest_assignment['created_at'])) ?></td>
                                    <td>
                                        <?php if ($isOld): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle"></i> <?= $daysDiff ?> hari
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($selected_pj) : ?>
                    <div class="alert alert-success mt-3" id="pj-success-alert">
                        <i class="fas fa-check-circle"></i> PJ baru untuk aplikasi <strong><?= $selected_pj['nama_aplikasi'] ?></strong> telah ditetapkan: <strong><?= $selected_pj['nama_user'] ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Application Selection -->
<div class="modal fade" id="aplikasiModal" tabindex="-1" aria-labelledby="aplikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aplikasiModalLabel">Pilih Aplikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="aplikasi_id" class="form-label">Aplikasi:</label>
                    <select id="aplikasi_id" class="form-select" required>
                        <option value="">-- Pilih Aplikasi --</option>
                        <?php foreach ($aplikasi as $app) : ?>
                            <option value="<?= $app['id'] ?>"><?= $app['nama_aplikasi'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="userListContainer" class="mt-3" style="display: none;">
                    <h6>Daftar User:</h6>
                    <div class="list-group" id="userList">
                        <!-- User list will be populated here via AJAX -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnDapatkanPJ" disabled>Dapatkan PJ</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- JavaScript for handling modal interactions -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const aplikasiSelect = document.getElementById('aplikasi_id');
        const userListContainer = document.getElementById('userListContainer');
        const userList = document.getElementById('userList');
        const btnDapatkanPJ = document.getElementById('btnDapatkanPJ');
        const pjTimeAlert = document.getElementById('pjTimeAlert');
        const hasOldAssignments = <?= $hasOldAssignments ? 'true' : 'false' ?>;

        // Show time alert if needed
        if (hasOldAssignments) {
            pjTimeAlert.style.display = 'block';
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('alert-success');
            const errorAlert = document.getElementById('alert-error');

            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 5000);

        // When application is selected, load users
        aplikasiSelect.addEventListener('change', function() {
            const aplikasiId = this.value;

            if (aplikasiId) {
                // Enable the button
                btnDapatkanPJ.disabled = false;

                // Fetch users for the selected application
                fetch(`<?= base_url('pj-assignment/get-users') ?>/${aplikasiId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous user list
                        userList.innerHTML = '';

                        // Display users
                        if (data.users && data.users.length > 0) {
                            data.users.forEach(user => {
                                const listItem = document.createElement('a');
                                listItem.className = 'list-group-item';
                                listItem.textContent = user.nama_user;

                                // Indicate if this user has already been assigned (status = 1)
                                if (user.status == 1) {
                                    listItem.classList.add('list-group-item-secondary');
                                    listItem.innerHTML += ' <span class="badge bg-secondary float-end">Sudah Ditugaskan</span>';
                                } else {
                                    listItem.classList.add('list-group-item-light');
                                }

                                userList.appendChild(listItem);
                            });

                            // Show the user list
                            userListContainer.style.display = 'block';
                        } else {
                            userList.innerHTML = '<div class="alert alert-warning">Tidak ada user yang terkait dengan aplikasi ini</div>';
                            userListContainer.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        userList.innerHTML = '<div class="alert alert-danger">Gagal memuat daftar user</div>';
                        userListContainer.style.display = 'block';
                    });
            } else {
                // Disable the button if no application is selected
                btnDapatkanPJ.disabled = true;
                userListContainer.style.display = 'none';
            }
        });

        // When "Dapatkan PJ" button is clicked
        btnDapatkanPJ.addEventListener('click', function() {
            const aplikasiId = aplikasiSelect.value;

            if (aplikasiId) {
                // Submit form to get PJ
                window.location.href = `<?= base_url('pj-assignment/get-pj') ?>/${aplikasiId}`;
            }
        });
    });
</script>
<?= $this->endSection() ?>