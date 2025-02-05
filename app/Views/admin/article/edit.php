<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-4">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>
                Edit Article Form
                <a href="<?= base_url('article') ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Articles
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/article/update/' . $article['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $article['title']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="<?= old('author', $article['author']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control ckeditor" id="content" name="content" rows="10"><?= old('content', $article['content']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Article Image</label><br>
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?= base_url('img/articles/' . $article['image']) ?>" alt="Current Image" style="max-width: 150px; margin-bottom: 10px;">
                                <br>
                                <small class="text-muted">Current image shown above. Upload new image below to replace it.</small>
                            <?php else: ?>
                                <p>No image uploaded yet.</p>
                            <?php endif; ?>
                            <input type="file" class="form-control mt-2" id="image" name="image" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft" <?= old('status', $article['status']) === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= old('status', $article['status']) === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id">Category</label>
                                <select name="category_id[]" class="form-control" id="select2cate" multiple="multiple" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"
                                            <?= isset($article['category_id']) && in_array($category['id'], $article['category_id']) ? 'selected' : '' ?>>
                                            <?= $category['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Created At</label>
                                <!-- Mengambil tanggal saja dari created_at -->
                                <input type="date" class="form-control" id="created_at" name="created_at"
                                    value="<?= old('created_at', date('Y-m-d', strtotime($article['created_at']))) ?>" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updated_at" class="form-label">Updated At</label>
                                <!-- Mengambil tanggal saja dari updated_at -->
                                <input type="date" class="form-control" id="updated_at" name="updated_at"
                                    value="<?= old('updated_at', date('Y-m-d', strtotime($article['updated_at']))) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Article Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="public" <?= old('type', $article['type'] ?? '') == 'public' ? 'selected' : '' ?>>Public</option>
                                <option value="internal" <?= old('type', $article['type'] ?? '') == 'internal' ? 'selected' : '' ?>>Internal</option>
                            </select>
                            <small class="text-muted">
                                Public: Articles can be accessed by all users without login, with no distribution required.<br>
                                Internal: Articles can only be accessed by logged-in users, with an additional option for distribution to specific categories.
                            </small>
                        </div>
                    </div>

                    <!-- Distribution section -->
                    <div class="card distribution-section" style="display: <?= old('type', $article['type'] ?? '') == 'internal' ? 'block' : 'none' ?>">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h5 class="form-label">Distribution (For Internal Articles Only)</h5>
                                </div>
                                <div class="row">
                                    <?php
                                    // Helper function to generate select options
                                    function generateOptions($items, $distributions, $type)
                                    {
                                        return array_reduce($items, function ($carry, $item) use ($distributions, $type) {
                                            $selected = in_array($item['id'], array_column(array_filter($distributions, function ($dist) use ($type) {
                                                return $dist['target_type'] === $type;
                                            }), 'target_id')) ? 'selected' : '';
                                            return $carry . "<option value=\"{$item['id']}\" $selected>" . esc($item['name']) . "</option>";
                                        }, '');
                                    }
                                    ?>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Directorates</label>
                                        <select class="form-control" id="select2direc" name="directorate_ids[]" multiple="multiple">
                                            <?= generateOptions($directorates, $article['distributions'], 'directorate') ?>
                                        </select>
                                        <small class="text-muted">Selecting a directorate will distribute to all divisions, departments, and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Divisions</label>
                                        <select class="form-control" id="selectdivision" name="division_ids[]" multiple="multiple">
                                            <?= generateOptions($divisions, $article['distributions'], 'division') ?>
                                        </select>
                                        <small class="text-muted">Selecting a division will distribute to all departments and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Departments</label>
                                        <select class="form-control" id="select2dep" name="department_ids[]" multiple="multiple">
                                            <?= generateOptions($departments, $article['distributions'], 'department') ?>
                                        </select>
                                        <small class="text-muted">Selecting a department will distribute to all sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sub Departments</label>
                                        <select class="form-control" id="select2sub" name="sub_department_ids[]" multiple="multiple">
                                            <?= generateOptions($sub_departments, $article['distributions'], 'sub_department') ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- CKEditor dan CKFinder JS -->
<script src="<?= base_url('/ckeditor/ckeditor.js') ?>"></script>
<script src="<?= base_url('/ckfinder/ckfinder.js') ?>"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for division selection
        $('#selectdivision').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Division',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for directorat selection
        $('#select2direc').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Directorat',
            allowClear: true,
            closeOnSelect: false
        });
        // Initialize Select2 for departement selection
        $('#select2dep').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Departement',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for sub departement selection
        $('#select2sub').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Sub Departement',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for category selection
        $('#select2cate').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Category',
            allowClear: true,
            closeOnSelect: false
        });

        // CKEditor Initialization
        var editor = CKEDITOR.replace('content', {
            height: 400,
            filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
            filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
            filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
        });

        CKFinder.setupCKEditor(editor);

        document.getElementById('type').addEventListener('change', function() {
            const distributionSection = document.querySelector('.distribution-section');
            if (this.value === 'internal') {
                distributionSection.style.display = 'block';
            } else {
                distributionSection.style.display = 'none';
            }
        });
    });
</script>


<?= $this->endSection() ?>