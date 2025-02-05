<?= $this->extend('layout/index'); ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>

<?= $this->section('page-content'); ?>
<!-- Begin Page Content -->
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">My Profile</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3 shadow-sm" style="max-width: 540px;">
                        <div class="row g-0">
                            <div class="col-md-4 d-flex align-items-center">
                                <img src="<?= base_url('img/' . (user()->user_image ?? 'default.png')); ?>" class="img-fluid rounded-circle mx-auto d-block" alt="<?= user()->username; ?>">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item text-center">
                                            <h4 class="fw-bold text-primary"> <?= user()->username; ?> </h4>
                                        </li>

                                        <?php if (user()->fullname) : ?>
                                            <li class="list-group-item text-center text-muted"> <?= user()->fullname; ?> </li>
                                        <?php endif; ?>

                                        <li class="list-group-item text-center text-muted"> <?= user()->email; ?> </li>
                                        <li class="list-group-item text-center">
                                            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#editProfileModal">Edit Profile</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card untuk Upload & Edit TTD -->
                    <div class="card shadow-sm border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">✍️ Signature</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if (user()->signature) : ?>
                                <!-- Display signature image with a smaller size -->
                                <img src="<?= base_url('img/ttd/' . user()->signature); ?>" class="img-thumbnail mb-3" alt="Tanda Tangan" style="max-width: 150px; height: auto;">
                                <!-- Form to delete the signature -->
                                <form action="<?= base_url('/user/delete_ttd'); ?>" method="post" class="mb-3">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete Signature</button>
                                </form>
                                <!-- Form to update the signature -->
                                <form action="<?= base_url('/user/upload_ttd'); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="signature" class="fw-bold" class="mb-3">Edit Signature</label>
                                        <input type="file" class="form-control" id="signature" name="signature" required>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-warning px-4">Update</button>
                                    </div>
                                </form>
                            <?php else : ?>
                                <!-- Form to upload a new signature -->
                                <form action="<?= base_url('/user/upload_ttd'); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="signature" class="fw-bold">Select Signature file</label>
                                        <input type="file" class="form-control" id="signature" name="signature" required>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success px-4">Upload</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('/user/update'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username" class="fw-bold">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= user()->username; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fullname" class="fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?= user()->fullname; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email" class="fw-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= user()->email; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="user_image" class="fw-bold">Profile Image</label>
                        <input type="file" class="form-control" id="user_image" name="user_image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endsection(); ?>