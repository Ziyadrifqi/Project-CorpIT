<?php echo view('template/header'); ?>
<!-- User Profile Section Start -->
<section class="team-details-section fix section-padding">
    <div class="container">
        <div class="team-details-wrapper">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="team-details-image">
                        <img src="<?= base_url('img/' . (user()->user_image ?? 'default.png')); ?>" alt="<?= user()->username; ?>" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="team-details-content">
                        <div class="details-info">
                            <h3><?= user()->username; ?></h3>
                            <span><?= esc($user->position); ?></span>
                        </div>
                        <p class="mt-3">
                            Selamat datang di profil Anda. Di bawah ini adalah informasi pribadi Anda:
                        </p>
                        <div class="profile-info mt-4">
                            <div class="profile-grid">
                                <?php if (user()->fullname) : ?>
                                    <div class="profile-field">
                                        <span class="profile-label">Full Name:</span>
                                        <span class="profile-value"><?= user()->fullname; ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="profile-field">
                                    <span class="profile-label">Email:</span>
                                    <span class="profile-value"><?= esc($user->email); ?></span>
                                </div>
                                <div class="profile-field">
                                    <span class="profile-label">Position:</span>
                                    <span class="profile-value"><?= esc($user->position); ?></span>
                                </div>
                                <div class="profile-field">
                                    <span class="profile-label">Sub Department:</span>
                                    <span class="profile-value"><?= esc($user->sub_department_name); ?></span>
                                </div>
                                <div class="profile-field">
                                    <span class="profile-label">Department:</span>
                                    <span class="profile-value"><?= esc($user->department_name); ?></span>
                                </div>
                                <div class="profile-field">
                                    <span class="profile-label">Division:</span>
                                    <span class="profile-value"><?= esc($user->division_name); ?></span>
                                </div>
                                <div class="profile-field">
                                    <span class="profile-label">Directorate:</span>
                                    <span class="profile-value"><?= esc($user->directorate_name); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="profile-actions mt-4">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">Edit Profile</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                </div>
                <form action="<?= base_url('/pages/profile/updated'); ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" style="color: black;" value="<?= user()->username; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" style="color: black;" value="<?= user()->fullname; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" style="color: black;" value="<?= user()->email; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="user_image">Profile Image</label>
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
</section>

<?php echo view('template/footer'); ?>