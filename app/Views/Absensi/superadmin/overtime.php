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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Month</label>
                                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select User</label>
                                <select name="user" class="form-control select2">
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= ($selectedUser == $user['id']) ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary" name="filtered" value="true">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <?php if ($isFiltered && !empty($activities)): ?>
                    <!-- Export Buttons - Only shown after filtering -->
                    <div class="mb-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-primary" id="signButton">
                            <i class="fas fa-signature"></i> Sign Document
                        </button>
                        <button type="button" class="btn btn-danger" id="viewPdfButton">
                            <i class="fas fa-file-pdf"></i> View PDF
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="overtimeTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Task</th>
                                <th>Diary Activity</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Ticket Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($isFiltered && !empty($activities)): ?>
                                <?php $i = 1; ?>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= date('d/m/Y', strtotime($activity['date'])) ?></td>
                                        <td><?= esc($activity['task']) ?></td>
                                        <td><?= nl2br(esc($activity['diary_activity'])) ?></td>
                                        <td><?= date('H:i', strtotime($activity['start_time'])) ?></td>
                                        <td><?= date('H:i', strtotime($activity['end_time'])) ?></td>
                                        <td><?= esc($activity['ticket_number']) ?></td>
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

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Signature</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Do you want to sign this document?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSign">Sign Document</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap',
            width: '100%'
        });

        $('#signButton').click(function() {
            $('#signatureModal').modal('show');
        });

        $('#confirmSign').click(function() {
            // Implement signature logic here
            $('#signButton').prop('disabled', true);
            $('#signatureModal').modal('hide');
        });

        $('#viewPdfButton').click(function() {
            window.location.href = '<?= base_url('overtime/exportPdf') ?>?' +
                'month=<?= $selectedMonth ?>&user=<?= $selectedUser ?>';
        });
    });
</script>
<?= $this->endSection() ?>