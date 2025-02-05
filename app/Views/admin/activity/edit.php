<?= $this->extend('layout/index'); ?>
<?= $this->section('page-content'); ?>

<div class="container mt-2">
    <div class="card-header">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?= esc($title); ?></h1>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                Edit Activity Form
                <a href="<?= base_url('admin/activity') ?>" class="text-primary float-end" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Activities
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')) : ?>
                    <div class="alert alert-danger">
                        <?php foreach (session('errors') as $error) : ?>
                            <?= $error ?><br>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('admin/activity/update/' . $activity['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="task" class="form-label">NIK</label>
                        <input type="text" class="form-control" id="nik" name="nik"
                            value="<?= old('nik', $activity['nik']) ?>" required maxlength="8"
                            pattern="\d{1,8}" title="Masukkan hingga 8 angka"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);">
                    </div>
                    <div class="mb-3">
                        <label for="task" class="form-label">Pemberi Tugas</label>
                        <input type="text" class="form-control" id="pbr_tugas" name="pbr_tugas"
                            value="<?= old('pbr_tugas', $activity['pbr_tugas']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="task" class="form-label">No. Ticket</label>
                        <input type="text" class="form-control" id="no_tiket" name="no_tiket"
                            value="<?= old('no_tiket', $activity['no_tiket']) ?>" required maxlength="6"
                            pattern="\d{6}" title="Masukkan tepat 6 angka"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                    </div>
                    <div class="mb-3">
                        <label for="activity_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="activity_date" name="activity_date"
                            value="<?= old('activity_date', $activity['activity_date']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="task" class="form-label">Task</label>
                        <input type="text" class="form-control" id="task" name="task"
                            value="<?= old('task', $activity['task']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location"
                            value="<?= old('location', $activity['location']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="start_time" name="start_time"
                            value="<?= old('start_time', date('H:i', strtotime($activity['start_time']))) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="end_time" name="end_time"
                            value="<?= old('end_time', date('H:i', strtotime($activity['end_time']))) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $activity['description']) ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Activity</button>
                        <a href="<?= base_url('admin/activity') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>