<?= $this->extend('layout/index') ?>

<?= $this->section('page-content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="text-center mb-0 fw-bold text-primary">Pre-Attendance Form</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h6 class="text-muted mb-2">Date: <?= date('d F Y') ?></h6>
                        <h5 class="fw-bold"><span id="jam" class="text-primary"><?= date('H:i:s') ?></span></h5>
                    </div>

                    <form id="preAbsensiForm" class="p-3 bg-light rounded">
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold mb-2">Absence Category</label>
                            <select class="form-select form-select-lg shadow-sm" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="judul_kegiatan" class="form-label fw-bold mb-2">Activity Title</label>
                            <input type="text"
                                class="form-control form-control-lg shadow-sm"
                                id="judul_kegiatan"
                                name="judul_kegiatan"
                                placeholder="Enter activity title"
                                required>
                        </div>
                        <div class="text-center pt-2">
                            <button type="submit" class="btn btn-primary btn-lg px-3 rounded-pill">
                                <i class="bi bi-check2-circle"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(() => {
        const now = new Date();
        document.getElementById('jam').textContent = now.toLocaleTimeString('id-ID');
    }, 1000);

    document.getElementById('preAbsensiForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        fetch('<?= base_url('absensi/submitPreAbsensi') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(typeof data.message === 'object' ? Object.values(data.message).join('\n') : data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Submit';
                }
            })
            .catch(error => {
                alert('An error occurred while processing the request');
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Submit';
            });
    });
</script>
<?= $this->endSection() ?>