<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-0">Manage Exams</h2>
            <p class="text-muted">Create, schedule and manage exams</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (auth()->user()->can('exams.create')): ?>
                <a href="/admin/exams/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Exam
                </a>
            <?php endif; ?>
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
            <?php if (empty($exams)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No exams found. Create your first exam to get started.</p>
                    <?php if (auth()->user()->can('exams.create')): ?>
                        <a href="/admin/exams/create" class="btn btn-primary mt-2">Create Exam</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Schedule</th>
                                <th>Creator</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?= $exam->id ?></td>
                                    <td>
                                        <strong><?= esc($exam->title) ?></strong>
                                        <?php if ($exam->description): ?>
                                            <br><small class="text-muted"><?= character_limiter(esc($exam->description), 60) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $exam->total_questions ?></td>
                                    <td><?= $exam->duration_minutes ?> min</td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'draft' => 'secondary',
                                            'scheduled' => 'info',
                                            'active' => 'success',
                                            'completed' => 'warning',
                                            'archived' => 'dark'
                                        ];
                                        $class = $badgeClass[$exam->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $class ?>">
                                            <?= ucfirst($exam->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($exam->is_scheduled && $exam->scheduled_start_time): ?>
                                            <small>
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M d, Y H:i', strtotime($exam->scheduled_start_time)) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= esc($exam->creator_name ?? 'Unknown') ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <?php if (auth()->user()->can('exams.create')): ?>
                                                <a href="/admin/exams/edit/<?= $exam->id ?>"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (auth()->user()->can('exams.schedule')): ?>
                                                <a href="/admin/exams/schedule/<?= $exam->id ?>"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Schedule">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (auth()->user()->can('exams.manage')): ?>
                                                <button onclick="deleteExam(<?= $exam->id ?>, '<?= esc($exam->title) ?>')"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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
function deleteExam(id, title) {
    if (!confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
        return;
    }

    $.ajax({
        url: `/admin/exams/delete/${id}`,
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
            alert('An error occurred while deleting the exam');
        }
    });
}
</script>

<?= $this->endSection() ?>
