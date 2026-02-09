<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-0">Manage Subjects</h2>
            <p class="text-muted">Create and manage exam subjects</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="/admin/subjects/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Subject
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
            <?php if (empty($subjects)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No subjects found. Create your first subject to get started.</p>
                    <a href="/admin/subjects/create" class="btn btn-primary mt-2">Create Subject</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Questions</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td><span class="badge bg-primary"><?= esc($subject->code) ?></span></td>
                                    <td><?= esc($subject->name) ?></td>
                                    <td><?= esc($subject->description) ?></td>
                                    <td>
                                        <?php
                                        $db = \Config\Database::connect();
                                        $count = $db->table('questions')->where('subject_id', $subject->id)->countAllResults();
                                        echo $count;
                                        ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($subject->created_at)) ?></td>
                                    <td class="text-end">
                                        <a href="/admin/subjects/edit/<?= $subject->id ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button onclick="deleteSubject(<?= $subject->id ?>, '<?= esc($subject->name) ?>')"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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
function deleteSubject(id, name) {
    if (!confirm(`Are you sure you want to delete "${name}"?`)) {
        return;
    }

    $.ajax({
        url: `/admin/subjects/delete/${id}`,
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
            alert('An error occurred while deleting the subject');
        }
    });
}
</script>

<?= $this->endSection() ?>
