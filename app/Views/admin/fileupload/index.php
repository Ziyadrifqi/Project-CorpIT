<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Work Instructions</h3>
    </div>
    <div class="card-body">
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
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                <?= esc($title); ?>
                <a href="<?= base_url('admin/fileupload/create') ?>" class="btn btn-primary mb-3 float-end"><i class="fas fa-plus"></i> Add New WI</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="fileupload">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Distribution</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($files as $file): ?>
                                <tr>
                                    <td scope="row" class="text-center"><?= $i++ ?></td>
                                    <td><?= esc($file['title']) ?></td>
                                    <td><?= esc($file['author']) ?></td>
                                    <td><?= esc($file['category_names']) ?></td>
                                    <td>
                                        <a href="<?= base_url($file['file_path']) ?>"
                                            class="btn btn-sm btn-info" target="_blank">
                                            Preview
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= esc($file['status'] === 'published' ? 'success' : 'warning') ?>">
                                            <?= esc(ucfirst($file['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $distributions = [];
                                        if (!empty($file['distributions'])) {
                                            foreach ($file['distributions'] as $dist) {
                                                $distributions[] = ucfirst(esc($dist['target_type']));
                                            }
                                            echo esc(implode(', ', array_unique($distributions)));
                                        } else {
                                            echo "Public";
                                        }
                                        ?>
                                    </td>
                                    <td><?= esc(date('d M Y', strtotime($file['created_at']))) ?></td>
                                    <td><?= esc(date('d M Y', strtotime($file['updated_at']))) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/fileupload/edit/' . $file['id']) ?>" class="btn btn-xs btn-warning " title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="<?= base_url('admin/fileupload/delete/' . $file['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
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
<?= $this->endSection(); ?>
<!--  untuk inisialisasi DataTables -->
<?= $this->section('scripts'); ?>
<?= $this->endSection(); ?>