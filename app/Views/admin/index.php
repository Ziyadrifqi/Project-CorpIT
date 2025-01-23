<?= $this->extend('layout/index'); ?>

<?= $this->section('page-content'); ?>
<!-- Begin Page Content -->
<div class="container-mt4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($title); ?></h3>
    </div>
    <div class="card-body">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <!-- Tabel dengan DataTables -->
                        <div class="table-responsive">
                            <table id="userTable" class="table table-bordered table-hover table-sm">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    <?php foreach ($users as $user) : ?>
                                        <tr class="text-center">
                                            <th scope="row"><?= $i++ ?></th>
                                            <td><?= esc($user->username); ?></td>
                                            <td><?= esc($user->email); ?></td>
                                            <td><?= esc($user->name); ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('admin/' . $user->userid); ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-info-circle"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<!-- Tambahkan di bagian bawah untuk inisialisasi DataTables -->
<?= $this->section('scripts'); ?>

<?= $this->endSection(); ?>