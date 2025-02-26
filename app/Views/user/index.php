<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>

<!-- Add Bootstrap 5 JS if not already in your layout -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success" id="alert-success">
        <i class="fas fa-check-circle"></i><?= esc(session('success')) ?>
    </div>
<?php endif ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger" id="alert-error">
        <i class="fas fa-exclamation-circle"><?= esc(session('error')) ?>
    </div>
<?php endif ?>

<!-- Begin Page Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-primary bg-gradient text-white py-3">
                    <h3 class="card-title mb-0 fw-bold">
                        <i class="fas fa-user-circle me-2"></i>My Profile
                    </h3>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Profile Card -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="row g-0">
                                    <div class="col-md-4 p-3">
                                        <div class="position-relative">
                                            <img src="<?= base_url('img/' . (user()->user_image ?? 'default.png')); ?>"
                                                class="img-fluid rounded-circle shadow-sm p-1 border"
                                                alt="<?= user()->username; ?>"
                                                style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body p-4">
                                            <div class="text-center mb-4">
                                                <h4 class="fw-bold text-primary mb-1"><?= user()->username; ?></h4>
                                                <?php if (user()->fullname) : ?>
                                                    <p class="text-muted mb-1"><?= user()->fullname; ?></p>
                                                <?php endif; ?>
                                                <p class="text-muted mb-3">
                                                    <i class="fas fa-envelope me-2"></i><?= user()->email; ?>
                                                </p>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm px-4 rounded-pill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editProfileModal">
                                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Signature Card -->
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-header bg-primary bg-gradient text-white py-3">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="fas fa-signature me-2"></i>Digital Signature
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <?php if (user()->signature) : ?>
                                        <div class="text-center mb-4">
                                            <img src="<?= base_url('img/ttd/' . user()->signature); ?>"
                                                class="img-thumbnail shadow-sm mb-3"
                                                alt="Digital Signature"
                                                style="max-width: 200px;">

                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="<?= base_url('/user/delete_ttd'); ?>" method="post">
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <form action="<?= base_url('/user/upload_ttd'); ?>"
                                            method="post"
                                            enctype="multipart/form-data"
                                            class="border rounded-3 p-3 bg-light">
                                            <h6 class="fw-bold mb-3">Update Signature</h6>
                                            <div class="mb-3">
                                                <input type="file"
                                                    class="form-control form-control-sm"
                                                    id="signature"
                                                    name="signature"
                                                    required>
                                            </div>
                                            <button type="submit" class="btn btn-warning btn-sm rounded-pill">
                                                <i class="fas fa-upload me-2"></i>Update
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <form action="<?= base_url('/user/upload_ttd'); ?>"
                                            method="post"
                                            enctype="multipart/form-data"
                                            class="text-center">
                                            <div class="mb-4">
                                                <div class="border-2 border-dashed rounded-3 p-4 bg-light">
                                                    <i class="fas fa-file-signature fs-2 text-muted mb-2"></i>
                                                    <h6 class="fw-bold">Upload Your Signature</h6>
                                                    <p class="text-muted small">Supported formats: PNG, JPG, JPEG</p>
                                                    <input type="file"
                                                        class="form-control form-control-sm"
                                                        id="signature"
                                                        name="signature"
                                                        required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                                <i class="fas fa-upload me-2"></i>Upload Signature
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary bg-gradient text-white">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/user/update'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                value="<?= user()->username; ?>"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label fw-bold">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text"
                                class="form-control"
                                id="fullname"
                                name="fullname"
                                value="<?= user()->fullname; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= user()->email; ?>"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="user_image" class="form-label fw-bold">Profile Image</label>
                        <input type="file"
                            class="form-control"
                            id="user_image"
                            name="user_image">
                        <div class="form-text">Leave empty to keep current image</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize tooltips if you're using them -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Bootstrap 5 is loaded
        if (typeof bootstrap !== 'undefined') {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }
    });
</script>

<?= $this->endsection(); ?>