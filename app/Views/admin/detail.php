<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<!-- Main content -->
<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                User Detail User Form
                <a href="<?= base_url('admin'); ?>" class="text-primary mb-3 float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to users
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="200px" class="bg-light">Username</th>
                                    <td><?= esc($user->username); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Email</th>
                                    <td><?= esc($user->email); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fullname</th>
                                    <td><?= esc($user->fullname); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Initial</th>
                                    <td><?= esc($user->initial); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Sub Department</th>
                                    <td><?= esc($user->sub_department_name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Department</th>
                                    <td><?= esc($user->department_name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Division</th>
                                    <td><?= esc($user->division_name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Directorate</th>
                                    <td><?= esc($user->directorate_name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Position</th>
                                    <td><?= esc($user->position); ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Role</th>
                                    <td><?= esc($user->role); ?></td>
                                </tr>
                            </table>
                            <a href="#" class="btn btn-warning float-right" data-toggle="modal" data-target="#editUserModal">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
            </section>
            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= base_url('admin/update/' . $user->userid); ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <?php if (session()->has('errors')): ?>
                                    <div class="alert alert-danger">
                                        <ul>
                                            <?php foreach (session('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" value="<?= esc($user->username); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= esc($user->email); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="fullname">Fullname</label>
                                    <input type="text" class="form-control" name="fullname" value="<?= esc($user->fullname); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="initial">Initial</label>
                                    <input type="text" class="form-control" name="initial" value="<?= esc($user->initial); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="sub_department">Sub Department</label>
                                    <select name="sub_department" class="form-control" required>
                                        <option value="">Select Sub Department</option>
                                        <?php foreach ($sub_departments as $sub_dept): ?>
                                            <option value="<?= $sub_dept->id ?>" <?= ($user->sub_department_id == $sub_dept->id) ? 'selected' : '' ?>>
                                                <?= esc($sub_dept->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <select name="department" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept->id ?>" <?= ($user->department_id == $dept->id) ? 'selected' : '' ?>>
                                                <?= esc($dept->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="division">Division</label>
                                    <select name="division" class="form-control" required>
                                        <option value="">Select Division</option>
                                        <?php foreach ($divisions as $div): ?>
                                            <option value="<?= $div->id ?>" <?= ($user->division_id == $div->id) ? 'selected' : '' ?>>
                                                <?= esc($div->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="directorate">Directorate</label>
                                    <select name="directorate" class="form-control" required>
                                        <option value="">Select Directorate</option>
                                        <?php foreach ($directorates as $dir): ?>
                                            <option value="<?= $dir->id ?>" <?= ($user->directorate_id == $dir->id) ? 'selected' : '' ?>>
                                                <?= esc($dir->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="position">Position</label>
                                    <input type="text" class="form-control" name="position" value="<?= esc($user->position); ?>" required>
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
        </div>
    </div>
</div>
<?= $this->endSection(); ?>