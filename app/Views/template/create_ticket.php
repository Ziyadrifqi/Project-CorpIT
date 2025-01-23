<?php echo view('template/header'); ?>

<!-- Create Ticket Section Start -->
<section class="create-ticket-section fix section-padding">
    <div class="container">
        <div class="create-ticket-wrapper">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7 mx-auto">
                    <div class="create-ticket-content">
                        <h3 class="text-center">Create a New Ticket</h3>
                        <p class="text-center mt-3">Please fill in the details to create a new ticket.</p>

                        <!-- Display success or error messages -->
                        <?php if (session()->get('message')): ?>
                            <div class="alert alert-success">
                                <?= session()->get('message') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->get('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->get('error') ?>
                            </div>
                        <?php endif; ?>

                        <!-- Ticket Form -->
                        <form action="<?= base_url('/pages/Ticket') ?>" method="post">

                            <!-- Ticket Number -->
                            <div class="form-group">
                                <label for="ticket_number">Ticket Number</label>
                                <input
                                    type="text"
                                    class="form-control <?= session('errors.ticket_number') ? 'is-invalid' : '' ?>"
                                    id="ticket_number"
                                    name="ticket_number"
                                    value="<?= old('ticket_number'); ?>"
                                    required
                                    maxlength="6"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                                <div class="invalid-feedback">
                                    <?= session('errors.ticket_number'); ?>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject'); ?>" required>
                                <div class="text-danger"><?= session('errors.subject'); ?></div>
                            </div>

                            <!-- Status Ticket -->
                            <div class="form-group">
                                <label for="status_ticket">Status Ticket</label>
                                <select class="form-control" id="status_ticket" name="status_ticket" required>
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="open" <?= old('status_ticket') === 'open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="closed" <?= old('status_ticket') === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                    <option value="resolved" <?= old('status_ticket') === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="rejected" <?= old('status_ticket') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="stopclock" <?= old('status_ticket') === 'stopclock' ? 'selected' : ''; ?>>Stop Clock</option>
                                </select>
                                <div class="text-danger"><?= session('errors.status_ticket'); ?></div>
                            </div>

                            <!-- Status Approval -->
                            <div class="form-group">
                                <label for="status_approval">Status Approval</label>
                                <select class="form-control" id="status_approval" name="status_approval" required>
                                    <option value="" disabled selected>Select Approval Status</option>
                                    <option value="pending" <?= old('status_approval') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approval Not Provided" <?= old('status_approval') === 'Approval Not Provided' ? 'selected' : ''; ?>>Approval Not Provided</option>
                                    <option value="successfully approval" <?= old('status_approval') === 'successfully approval' ? 'selected' : ''; ?>>Successfully Approved</option>
                                </select>
                                <div class="text-danger"><?= session('errors.status_approval'); ?></div>
                            </div>

                            <!-- Resolution -->
                            <div class="form-group">
                                <label for="resolution">Resolution</label>
                                <textarea class="form-control" id="resolution" name="resolution"><?= old('resolution'); ?></textarea>
                            </div>

                            <!-- Conversation -->
                            <div class="form-group">
                                <label for="conversation">Percakapan Terakhir</label>
                                <textarea class="form-control" id="conversation" name="conversation"><?= old('conversation'); ?></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary">Create Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket History Section -->
        <div class="ticket-history-section mt-5">
            <h4>Ticket History</h4>
            <div class="table-responsive">
                <table class="table table-bordered mt-3" id="ticketHistoryTable">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Subject</th>
                            <th>Status Ticket</th>
                            <th>Status Approval</th>
                            <th>Resolution</th>
                            <th>Conversation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ticketHistory)): ?>
                            <?php foreach ($ticketHistory as $ticket): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('pages/monitoring_ticket/' . $ticket['ticket_number']); ?>" class="ticket-link">
                                            <?= esc($ticket['ticket_number']); ?>
                                        </a>
                                    </td>
                                    <td><?= esc($ticket['subject']); ?></td>
                                    <td><?= esc($ticket['status_ticket']); ?></td>
                                    <td><?= esc($ticket['status_approval']); ?></td>
                                    <td><?= esc($ticket['resolution']); ?></td>
                                    <td><?= esc($ticket['conversation']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No tickets found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- Create Ticket Section End -->

<?php echo view('template/footer'); ?>
<!-- Add DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // Fungsi umum untuk inisialisasi DataTables
        function initializeDataTable(tableId, searchPlaceholder = "Search:", lengthMenuText = "Show _MENU_ data per page", infoText = "Displays _START_ to _END_ of _TOTAL_ data") {
            $(tableId).DataTable({
                "paging": true, // Aktifkan pagination
                "searching": true, // Aktifkan pencarian
                "info": true, // Tampilkan info jumlah data
                "lengthChange": true, // Aktifkan opsi jumlah data per halaman
                "pageLength": 5, // Jumlah default data per halaman
                "language": {
                    "search": searchPlaceholder, // Ubah teks pencarian
                    "lengthMenu": lengthMenuText,
                    "info": infoText,
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        }
        // Inisialisasi DataTables untuk #categoryabsen dengan teks bahasa Inggris
        initializeDataTable('#ticketHistoryTable');
    });
</script>