<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-0">Manage Users</h2>
            <p class="text-muted">Create and manage users with different roles</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="/admin/users/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No users found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user->id ?></td>
                                    <td><?= esc($user->username) ?></td>
                                    <td><?= esc($user->email ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $roleBadges = [
                                            'superadmin' => 'danger',
                                            'admin' => 'warning',
                                            'exam_expert' => 'info',
                                            'user' => 'secondary'
                                        ];
                                        $badgeClass = $roleBadges[$user->group ?? 'user'] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= ucwords(str_replace('_', ' ', $user->group ?? 'user')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user->active): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user->created_at)) ?></td>
                                    <td class="text-end">
                                        <?php if ($user->id != auth()->user()->id): ?>
                                            <button onclick="deleteUser(<?= $user->id ?>, '<?= esc($user->username) ?>')"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">(You)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deleteUser(id, username) {
    if (!confirm(`Are you sure you want to delete user "${username}"?`)) {
        return;
    }

    $.ajax({
        url: `/admin/users/delete/${id}`,
        method: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('An error occurred while deleting the user');
        }
    });
}
</script>

<?= $this->endSection() ?>
