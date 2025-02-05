<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Articles</h3>
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
                <a href="<?= base_url('article/create'); ?>" class="btn btn-primary float-end"><i class="fas fa-plus"></i> Create New Article</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="userArticle">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Distribution</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td scope="row" class="text-center"><?= $i++ ?></td>
                                    <td><?= esc($article['title']) ?></td>
                                    <td><?= esc($article['author']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= esc($article['status'] === 'published' ? 'success' : 'warning') ?>">
                                            <?= esc(ucfirst($article['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($article['category_names']) ?></td>
                                    <td>
                                        <?php
                                        $distributions = [];
                                        if (!empty($article['distributions'])) {
                                            foreach ($article['distributions'] as $dist) {
                                                $distributions[] = ucfirst(esc($dist['target_type']));
                                            }
                                            echo esc(implode(', ', array_unique($distributions)));
                                        } else {
                                            echo "Public";
                                        }
                                        ?>
                                    </td>
                                    <td><?= esc(date('d M Y', strtotime($article['created_at']))) ?></td>
                                    <td><?= esc(date('d M Y', strtotime($article['updated_at']))) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url('article/edit/' . esc($article['id'])); ?>" class="btn btn-xs btn-warning " title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="<?= base_url('article/delete/' . esc($article['id'])); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this article?')">
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

<!-- Tambahkan di bagian bawah untuk inisialisasi DataTables -->
<?= $this->section('scripts'); ?>
<?= $this->endSection(); ?>