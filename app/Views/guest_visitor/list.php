<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-users me-1"></i>
                Guest Visitors List
                <a href="<?= base_url('guest-visitor/create') ?>" class="btn btn-primary float-end">
                    <i class="fas fa-plus"></i> Create New Guest
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="guestTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Valid Until</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guests as $key => $guest): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($guest['name']) ?></td>
                                <td><?= esc($guest['email']) ?></td>
                                <td>
                                    <span class="badge <?= $guest['status'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $guest['status'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= esc($guest['valid_till_days']) ?> days</td>
                                <td>
                                    <a href="<?= base_url('guest-visitor/edit/' . $guest['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-guest" data-id="<?= $guest['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>