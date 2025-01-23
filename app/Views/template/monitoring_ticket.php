<?php echo view('template/header'); ?>

<!-- Monitoring Ticket Section Start -->
<section class="monitoring-ticket-section section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ticket-search-form">
                    <form id="ticketSearchForm">
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" class="form-control" id="ticketNumber" name="ticket_number" placeholder="Enter Ticket Number" required>
                            <button class="btn btn-sm btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>

                <div id="ticketResultContainer" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-white">Ticket Details</h4>
                        </div>
                        <div class="card-body">
                            <table class="ticket-details-table">
                                <tr>
                                    <td class="label">Ticket Number</td>
                                    <td id="resultTicketNumber" class="value"></td>
                                </tr>
                                <tr>
                                    <td class="label">Subject</td>
                                    <td id="resultSubject" class="value"></td>
                                </tr>
                                <tr>
                                    <td class="label">Technician</td>
                                    <td id="resultTechnician" class="value"></td>
                                </tr>
                                <tr>
                                    <td class="label">Status Ticket</td>
                                    <td>
                                        <span id="resultStatusTicket"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Status Approval</td>
                                    <td>
                                        <span id="resultStatusApproval"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Resolution</td>
                                    <td id="resultResolution" class="value"></td>
                                </tr>
                                <tr>
                                    <td class="label">Conversation</td>
                                    <td>
                                        <div id="resultConversation" class="conversation-box"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="ticketErrorContainer" class="mt-4" style="display: none;">
                    <div class="alert alert-danger" role="alert">
                        Ticket not found. Please check the ticket number and try again.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada ticket number yang di-preload
        var preloadedTicketNumber = '<?= $prefilledTicketNumber ?? '' ?>';
        if (preloadedTicketNumber) {
            document.getElementById('ticketNumber').value = preloadedTicketNumber;
            document.getElementById('ticketSearchForm').dispatchEvent(new Event('submit'));
        }
    });
    document.getElementById('ticketSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var ticketNumber = document.getElementById('ticketNumber').value;

        fetch('<?= base_url('pages/searchTicket') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ticket_number=' + encodeURIComponent(ticketNumber)
            })
            .then(response => response.json())
            .then(data => {
                var resultContainer = document.getElementById('ticketResultContainer');
                var errorContainer = document.getElementById('ticketErrorContainer');

                if (data.status === 'success') {
                    document.getElementById('resultTicketNumber').textContent = data.ticket.ticket_number;
                    document.getElementById('resultSubject').textContent = data.ticket.subject;
                    document.getElementById('resultTechnician').textContent = data.ticket.technician || 'Not assigned';

                    // Status Ticket Styling
                    var statusTicketElem = document.getElementById('resultStatusTicket');
                    statusTicketElem.textContent = data.ticket.status_ticket;
                    statusTicketElem.className = 'status-ticket ' +
                        (data.ticket.status_ticket.toLowerCase() === 'open' ? 'status-open' :
                            data.ticket.status_ticket.toLowerCase() === 'closed' ? 'status-closed' :
                            data.ticket.status_ticket.toLowerCase() === 'resolved' ? 'status-resolved' :
                            data.ticket.status_ticket.toLowerCase() === 'rejected' ? 'status-rejected' :
                            data.ticket.status_ticket.toLowerCase() === 'stopclock' ? 'status-stopclock' :
                            '');

                    // Status Approval Styling
                    var statusApprovalElem = document.getElementById('resultStatusApproval');
                    statusApprovalElem.textContent = data.ticket.status_approval;
                    statusApprovalElem.className = 'status-approval ' +
                        (data.ticket.status_approval.toLowerCase() === 'pending' ? 'status-pending' :
                            data.ticket.status_approval.toLowerCase() === 'approval not provided' ? 'status-approval-not-provided' :
                            data.ticket.status_approval.toLowerCase() === 'successfully approval' ? 'status-successfully-approval' :
                            '');

                    document.getElementById('resultResolution').textContent = data.ticket.resolution || 'No resolution yet';
                    document.getElementById('resultConversation').textContent = data.ticket.conversation || 'No conversation';

                    resultContainer.style.display = 'block';
                    errorContainer.style.display = 'none';
                } else {
                    resultContainer.style.display = 'none';
                    errorContainer.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>

<?php echo view('template/footer'); ?>