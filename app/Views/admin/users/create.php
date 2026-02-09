<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/admin/users" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
            <h2>Create New User</h2>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="/admin/users/store" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text"
                                   class="form-control"
                                   id="username"
                                   name="username"
                                   value="<?= old('username') ?>"
                                   required
                                   maxlength="30">
                            <small class="text-muted">Only letters and numbers, 3-30 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="<?= old('email') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   required
                                   minlength="8">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select a role</option>
                                <option value="user" <?= old('role') === 'user' ? 'selected' : '' ?>>
                                    User (Can take exams only)
                                </option>
                                <option value="exam_expert" <?= old('role') === 'exam_expert' ? 'selected' : '' ?>>
                                    Exam Expert (Can create subjects, questions, and exams)
                                </option>
                                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>
                                    Admin (Can schedule exams)
                                </option>
                                <option value="superadmin" <?= old('role') === 'superadmin' ? 'selected' : '' ?>>
                                    Super Admin (Full access)
                                </option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Role Permissions:</h6>
                            <ul class="mb-0">
                                <li><strong>User:</strong> Can only take exams and view results</li>
                                <li><strong>Exam Expert:</strong> Can create subjects, questions, and build exams</li>
                                <li><strong>Admin:</strong> Can schedule exam dates and times</li>
                                <li><strong>Super Admin:</strong> Complete control over all features</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/users" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Access Control</h5>
                </div>
                <div class="card-body">
                    <h6>Exam Expert can access:</h6>
                    <ul class="small">
                        <li>/admin/subjects</li>
                        <li>/admin/questions</li>
                        <li>/admin/exams (create/edit)</li>
                    </ul>

                    <hr>

                    <h6>Admin can access:</h6>
                    <ul class="small">
                        <li>/admin/exams (schedule)</li>
                    </ul>

                    <hr>

                    <h6>Super Admin can access:</h6>
                    <ul class="small">
                        <li>All admin features</li>
                        <li>User management</li>
                        <li>System settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
