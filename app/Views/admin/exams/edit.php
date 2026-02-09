<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/admin/exams" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Exams
            </a>
            <h2>Edit Exam</h2>
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

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/admin/exams/update/<?= $exam->id ?>" method="POST" id="examForm">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Exam Title *</label>
                            <input type="text"
                                   class="form-control"
                                   id="title"
                                   name="title"
                                   value="<?= old('title', $exam->title) ?>"
                                   required
                                   maxlength="200">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      maxlength="500"><?= old('description', $exam->description) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes) *</label>
                                <input type="number"
                                       class="form-control"
                                       id="duration_minutes"
                                       name="duration_minutes"
                                       value="<?= old('duration_minutes', $exam->duration_minutes) ?>"
                                       min="1"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="marks_per_question" class="form-label">Marks per Question *</label>
                                <input type="number"
                                       class="form-control"
                                       id="marks_per_question"
                                       name="marks_per_question"
                                       value="<?= old('marks_per_question', $exam->marks_per_question) ?>"
                                       step="0.25"
                                       min="0.25"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="has_negative_marking" class="form-label">Negative Marking *</label>
                                <select class="form-select"
                                        id="has_negative_marking"
                                        name="has_negative_marking"
                                        required>
                                    <option value="0" <?= old('has_negative_marking', $exam->has_negative_marking) == 0 ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= old('has_negative_marking', $exam->has_negative_marking) == 1 ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="negative_marks_container">
                                <label for="negative_marks_per_question" class="form-label">Negative Marks per Question</label>
                                <input type="number"
                                       class="form-control"
                                       id="negative_marks_per_question"
                                       name="negative_marks_per_question"
                                       value="<?= old('negative_marks_per_question', $exam->negative_marks_per_question) ?>"
                                       step="0.25"
                                       min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="max_tab_switches_allowed" class="form-label">Max Tab Switches Allowed *</label>
                            <input type="number"
                                   class="form-control"
                                   id="max_tab_switches_allowed"
                                   name="max_tab_switches_allowed"
                                   value="<?= old('max_tab_switches_allowed', $exam->max_tab_switches_allowed) ?>"
                                   min="0"
                                   required>
                            <small class="text-muted">0 = unlimited switches</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="randomize_questions" class="form-label">Randomize Questions *</label>
                                <select class="form-select"
                                        id="randomize_questions"
                                        name="randomize_questions"
                                        required>
                                    <option value="0" <?= old('randomize_questions', $exam->randomize_questions) == 0 ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= old('randomize_questions', $exam->randomize_questions) == 1 ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="randomize_options" class="form-label">Randomize Options *</label>
                                <select class="form-select"
                                        id="randomize_options"
                                        name="randomize_options"
                                        required>
                                    <option value="0" <?= old('randomize_options', $exam->randomize_options) == 0 ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= old('randomize_options', $exam->randomize_options) == 1 ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Subject Distribution</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addSubjectBtn">
                            <i class="fas fa-plus"></i> Add Subject
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="subjectDistribution">
                            <!-- Dynamic subject rows will be added here -->
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Select subjects and specify how many questions from each subject should be included in the exam.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Exam Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Questions:</strong>
                            <span id="totalQuestions" class="float-end badge bg-primary">0</span>
                        </div>
                        <div class="mb-3">
                            <strong>Total Marks:</strong>
                            <span id="totalMarks" class="float-end badge bg-success">0</span>
                        </div>
                        <div class="mb-3">
                            <strong>Duration:</strong>
                            <span id="examDuration" class="float-end badge bg-info">0 min</span>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Exam
                            </button>
                            <a href="/admin/exams" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const subjects = <?= json_encode($subjects) ?>;
const distribution = <?= json_encode($distribution) ?>;
let subjectRowCount = 0;

$(document).ready(function() {
    // Show/hide negative marks field
    $('#has_negative_marking').on('change', function() {
        if ($(this).val() === '1') {
            $('#negative_marks_container').show();
        } else {
            $('#negative_marks_container').hide();
        }
    }).trigger('change');

    // Load existing distribution
    if (distribution && distribution.length > 0) {
        distribution.forEach(dist => {
            addSubjectRow(dist.subject_id, dist.number_of_questions);
        });
    } else {
        // Add initial subject row
        addSubjectRow();
    }

    // Add subject button
    $('#addSubjectBtn').on('click', function() {
        addSubjectRow();
    });

    // Update summary when values change
    $('#examForm').on('input change', 'input, select', updateSummary);
    updateSummary();
});

function addSubjectRow(selectedSubjectId = null, questionCount = 10) {
    const rowId = subjectRowCount++;

    let options = '<option value="">Select a subject</option>';
    subjects.forEach(subject => {
        const selected = selectedSubjectId == subject.id ? 'selected' : '';
        options += `<option value="${subject.id}" data-count="${subject.question_count}" ${selected}>
            ${subject.code} - ${subject.name} (${subject.question_count} questions available)
        </option>`;
    });

    const html = `
        <div class="row mb-3 subject-row" data-row="${rowId}">
            <div class="col-md-6">
                <label class="form-label">Subject</label>
                <select class="form-select subject-select" name="subjects[]" required>
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Number of Questions</label>
                <input type="number"
                       class="form-control questions-count"
                       name="questions_count[]"
                       min="1"
                       value="${questionCount}"
                       required>
                <small class="text-muted available-count"></small>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-subject" data-row="${rowId}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    $('#subjectDistribution').append(html);

    // Attach remove event
    $(`.remove-subject[data-row="${rowId}"]`).on('click', function() {
        if ($('.subject-row').length > 1) {
            $(this).closest('.subject-row').remove();
            updateSummary();
        } else {
            alert('At least one subject is required');
        }
    });

    // Update available count when subject changes
    $(`.subject-row[data-row="${rowId}"] .subject-select`).on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const availableCount = selectedOption.data('count') || 0;
        $(this).closest('.subject-row').find('.available-count').text(
            `${availableCount} available`
        );
        updateSummary();
    });

    // Update summary when question count changes
    $(`.subject-row[data-row="${rowId}"] .questions-count`).on('input', updateSummary);

    // Trigger change to show available count for pre-selected subjects
    if (selectedSubjectId) {
        $(`.subject-row[data-row="${rowId}"] .subject-select`).trigger('change');
    }
}

function updateSummary() {
    // Calculate total questions
    let totalQuestions = 0;
    $('.questions-count').each(function() {
        const value = parseInt($(this).val()) || 0;
        totalQuestions += value;
    });

    // Calculate total marks
    const marksPerQuestion = parseFloat($('#marks_per_question').val()) || 0;
    const totalMarks = totalQuestions * marksPerQuestion;

    // Get duration
    const duration = parseInt($('#duration_minutes').val()) || 0;

    // Update summary
    $('#totalQuestions').text(totalQuestions);
    $('#totalMarks').text(totalMarks.toFixed(2));
    $('#examDuration').text(duration + ' min');
}
</script>
<?= $this->endSection() ?>
