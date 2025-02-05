<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>
                Edit Work Instruction Form
                <a href="<?= base_url('admin/fileuploads') ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Work Instructions
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->get('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('validation')): ?>
                    <div class="alert alert-danger">
                        <?= session()->get('validation')->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/fileupload/update/' . $file['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $file['title']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="<?= old('author', $file['author']) ?>" required>
                            </div>
                        </div>

                        <!-- Description Input -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?= (session()->has('validation') && session()->get('validation')->hasError('description')) ? 'is-invalid' : '' ?>"
                                id="description" name="description" rows="3" required><?= old('description', $file['description']) ?></textarea>
                            <div class="invalid-feedback">
                                <?= session()->get('validation')?->getError('description') ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Category Dropdown -->
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id[]" class="form-control" id="select2cate" multiple="multiple" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"
                                        <?= isset($file['category_id']) && in_array($category['id'], $file['category_id']) ? 'selected' : '' ?>>
                                        <?= $category['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= session()->get('validation')?->getError('category_id') ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft" <?= old('status', $file['status']) === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= old('status', $file['status']) === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                        </div>
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <label for="userfile" class="form-label">File (leave empty to keep existing file)</label>
                            <input type="file" class="form-control <?= (session()->has('validation') && session()->get('validation')->hasError('userfile')) ? 'is-invalid' : '' ?>"
                                id="userfile" name="userfile">
                            <div class="invalid-feedback">
                                <?= session()->get('validation')?->getError('userfile') ?>
                            </div>
                            <small class="text-muted">Current file: <?= $file['file_path'] ?></small>
                            <small class="text-muted d-block">Maximum file size: 15MB</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Created At -->
                        <div class="col-md-6 mb-3">
                            <label for="created_at" class="form-label">Created At</label>
                            <input type="date" class="form-control" id="created_at" name="created_at"
                                value="<?= old('created_at', date('Y-m-d', strtotime($file['created_at']))) ?>" disabled>
                        </div>

                        <!-- Updated At -->
                        <div class="col-md-6 mb-3">
                            <label for="updated_at" class="form-label">Updated At</label>
                            <input type="date" class="form-control" id="updated_at" name="updated_at"
                                value="<?= old('updated_at', date('Y-m-d', strtotime($file['updated_at']))) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">WI Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="public" <?= old('type', $file['type'] ?? '') == 'public' ? 'selected' : '' ?>>Public</option>
                                <option value="internal" <?= old('type', $file['type'] ?? '') == 'internal' ? 'selected' : '' ?>>Internal</option>
                            </select>
                            <small class="text-muted">
                                Public: WI can be accessed by all users without login, with no distribution required.<br>
                                Internal: WI can only be accessed by logged-in users, with an additional option for distribution to specific categories.
                            </small>
                        </div>
                    </div>

                    <!-- Distribution section -->
                    <div class="card distribution-section" style="display: <?= old('type', $file['type'] ?? '') == 'internal' ? 'block' : 'none' ?>">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h5 class="form-label">Distribution (For Internal WI Only)</h5>
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
                                            <?= generateOptions($directorates, $file['distributions'], 'directorate') ?>
                                        </select>
                                        <small class="text-muted">Selecting a directorate will distribute to all divisions, departments, and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Divisions</label>
                                        <select class="form-control" id="selectdivision" name="division_ids[]" multiple="multiple">
                                            <?= generateOptions($divisions, $file['distributions'], 'division') ?>
                                        </select>
                                        <small class="text-muted">Selecting a division will distribute to all departments and sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Departments</label>
                                        <select class="form-control" id="select2dep" name="department_ids[]" multiple="multiple">
                                            <?= generateOptions($departments, $file['distributions'], 'department') ?>
                                        </select>
                                        <small class="text-muted">Selecting a department will distribute to all sub-departments under it</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sub Departments</label>
                                        <select class="form-control" id="select2sub" name="sub_department_ids[]" multiple="multiple">
                                            <?= generateOptions($sub_departments, $file['distributions'], 'sub_department') ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for division selection
        $('#selectdivision').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a Division',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for directorat selection
        $('#select2direc').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a Directorat',
            allowClear: true,
            closeOnSelect: false
        });
        // Initialize Select2 for departement selection
        $('#select2dep').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a Departement',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for sub departement selection
        $('#select2sub').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a Sub Departement',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize Select2 for category selection
        $('#select2cate').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select a Category',
            allowClear: true,
            closeOnSelect: false
        });
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