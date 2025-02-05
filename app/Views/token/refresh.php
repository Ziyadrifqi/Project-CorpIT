<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-sync me-1"></i>
                Refresh Token Form
                <a href="<?= base_url('user/') ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to HOME
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->get('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->get('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('token/refresh') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="current_token" class="form-label">Current Refresh Token</label>
                        <textarea class="form-control" id="current_token" name="current_token" rows="3" required><?= old('current_token') ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Refresh Token</button>
                    </div>
                </form>

                <?php if (session()->has('token_data')): ?>
                    <?php $token_data = session()->get('token_data'); ?>
                    <div class="mt-4">
                        <h4>New Token Information</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Access Token</th>
                                    <td><textarea class="form-control" rows="2" readonly><?= $token_data['access_token'] ?></textarea></td>
                                </tr>
                                <tr>
                                    <th>Refresh Token</th>
                                    <td><textarea class="form-control" rows="2" readonly><?= $token_data['refresh_token'] ?></textarea></td>
                                </tr>
                                <tr>
                                    <th>Token Type</th>
                                    <td><?= $token_data['token_type'] ?></td>
                                </tr>
                                <tr>
                                    <th>Expires In</th>
                                    <td><?= $token_data['expires_in'] ?> seconds</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>