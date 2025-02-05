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
                New Article Form
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

                <form action="<?= base_url('admin/article/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="<?= old('author') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control ckeditor" id="content" name="content" rows="10"><?= old('content') ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Article Image</label>
                            <input type="file" class="form-control mt-2" id="image" name="image" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft" <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= old('status') === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id">Category</label>
                                <select name="category_id[]" class="form-control" id="select2cate" multiple="multiple" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"
                                            <?= (in_array($category['id'], old('category_id', []))) ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                    <!-- Distribution Selection -->
                    <div class="card distribution-section" style="display: <?= old('type', $article['type'] ?? '') == 'internal' ? 'block' : 'none' ?>">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h5 class="form-label">Distribution (For Internal Articles Only)</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Directorates</label>
                                        <select class="form-control" id="select2direc" name="directorate_ids[]" multiple="multiple">
                                            <?php foreach ($directorates as $directorate): ?>
                                                <option value="<?= $directorate['id'] ?>" <?= in_array($directorate['id'], old('directorate_ids', [])) ? 'selected' : '' ?>>
                                                    <?= esc($directorate['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Selecting a directorate will distribute to all divisions, departments, and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Divisions</label>
                                        <select class="form-control" id="selectdivision" name="division_ids[]" multiple="multiple">
                                            <?php foreach ($divisions as $division): ?>
                                                <option value="<?= $division['id'] ?>" <?= in_array($division['id'], old('division_ids', [])) ? 'selected' : '' ?>>
                                                    <?= esc($division['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Selecting a division will distribute to all departments and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Departments</label>
                                        <select class="form-control" id="select2dep" name="department_ids[]" multiple="multiple">
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?= $department['id'] ?>" <?= in_array($department['id'], old('department_ids', [])) ? 'selected' : '' ?>>
                                                    <?= esc($department['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Selecting a department will distribute to all sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sub Departments</label>
                                        <select class="form-control" id="select2sub" name="sub_department_ids[]" multiple="multiple">
                                            <?php foreach ($sub_departments as $sub_department): ?>
                                                <option value="<?= $sub_department['id'] ?>" <?= in_array($sub_department['id'], old('sub_department_ids', [])) ? 'selected' : '' ?>>
                                                    <?= esc($sub_department['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Article</button>
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