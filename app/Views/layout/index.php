<?php echo view('layout/header'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?= $this->renderSection('page-content'); ?>
    <!-- /.content -->
</div>

<!-- /.content-wrapper -->
<?php echo view('layout/footer'); ?>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url(); ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url(); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url(); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- DataTables CSS dengan Bootstrap -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<!-- jQuery, DataTables, dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Ikon Font Awesome -->
<script>
    // Fungsi untuk menghilangkan alert setelah 5 detik (5000 ms)
    setTimeout(function() {
        var successAlert = document.getElementById('alert-success');
        var errorAlert = document.getElementById('alert-error');

        if (successAlert) {
            successAlert.style.display = 'none';
        }
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 5000); // 5000 ms = 5 detik
</script>

<!-- Inisialisasi DataTables -->
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

        // Inisialisasi DataTables untuk #userTable
        initializeDataTable('#userTable');

        // Inisialisasi DataTables untuk #userRoleTable 
        initializeDataTable('#userRoleTable');

        // Inisialisasi DataTables untuk #userArticles 
        initializeDataTable('#userArticle');

        // Inisialisasi DataTables untuk #categories 
        initializeDataTable('#categories');
        // Inisialisasi DataTables untuk #fileupload 
        initializeDataTable('#fileupload');
        // Inisialisasi DataTables untuk #departement 
        initializeDataTable('#departements ');

        // Inisialisasi DataTables untuk #subdepartement 
        initializeDataTable('#subdepart ');

        // Inisialisasi DataTables untuk #histroyabsen 
        initializeDataTable('#historyabsen');

        // Inisialisasi DataTables untuk #superadminHistoryAbsen 
        initializeDataTable('#superadminHistoryAbsen');

        // Inisialisasi DataTables untuk #categoryabsen 
        initializeDataTable('#categoryabsen');

        // Inisialisasi DataTables untuk #adminactivity 
        initializeDataTable('#adminactivity');

        // Inisialisasi DataTables untuk #historyactivity 
        initializeDataTable('#historyactivity');
        // Inisialisasi DataTables untuk #Guest_visitor 
        initializeDataTable('#Guest_visitor');
        // Inisialisasi DataTables untuk #overtimeTable
        initializeDataTable('#overtimeTable');
        // Inisialisasi DataTables untuk #pdfTable 
        initializeDataTable('#pdfTable');
        // Inisialisasi DataTables untuk #sign 
        initializeDataTable('#sign');
        // Inisialisasi DataTables untuk #superadminHistoryAbsen
        initializeDataTable('#superadminHistory');
    });
</script>

<?= $this->renderSection('scripts') ?>