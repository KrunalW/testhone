<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/admin/subjects" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Subjects
            </a>
            <h2>Edit Subject</h2>
        </div>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="/admin/subjects/update/<?= $subject->id ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="code" class="form-label">Subject Code *</label>
                    <input type="text"
                           class="form-control"
                           id="code"
                           name="code"
                           value="<?= old('code', $subject->code) ?>"
                           required
                           maxlength="20">
                    <small class="text-muted">Use uppercase letters and numbers only</small>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Subject Name *</label>
                    <input type="text"
                           class="form-control"
                           id="name"
                           name="name"
                           value="<?= old('name', $subject->name) ?>"
                           required
                           maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control"
                              id="description"
                              name="description"
                              rows="3"
                              maxlength="500"><?= old('description', $subject->description) ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/admin/subjects" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
