<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-0">Manage Questions</h2>
            <p class="text-muted">Create and manage exam questions</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="/admin/questions/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Question
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

    <!-- Filter by Subject -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/admin/questions" class="row g-3">
                <div class="col-md-4">
                    <label for="subject_id" class="form-label">Filter by Subject</label>
                    <select name="subject_id" id="subject_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Subjects</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject->id ?>" <?= $selectedSubject == $subject->id ? 'selected' : '' ?>>
                                <?= esc($subject->code) ?> - <?= esc($subject->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($selectedSubject): ?>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="/admin/questions" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($questions)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No questions found. Create your first question to get started.</p>
                    <a href="/admin/questions/create" class="btn btn-primary mt-2">Create Question</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 120px;">Subject</th>
                                <th>Question</th>
                                <th style="width: 100px;">Type</th>
                                <th style="width: 120px;">Created</th>
                                <th class="text-end" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?= $question->id ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= esc($question->subject_code) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="question-preview">
                                            <?= character_limiter(esc($question->question_text), 100) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($question->question_type === 'image'): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-image"></i> Image
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-font"></i> Text
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($question->created_at)) ?></td>
                                    <td class="text-end">
                                        <a href="/admin/questions/edit/<?= $question->id ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteQuestion(<?= $question->id ?>)"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
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
function deleteQuestion(id) {
    if (!confirm('Are you sure you want to delete this question? This will also delete all associated options and student answers.')) {
        return;
    }

    $.ajax({
        url: `/admin/questions/delete/${id}`,
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
            alert('An error occurred while deleting the question');
        }
    });
}
</script>

<?= $this->endSection() ?>
