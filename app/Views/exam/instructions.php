<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-info-circle"></i> Exam Instructions</h4>
                </div>
                <div class="card-body">
                    <h5 class="text-primary mb-3"><?= esc($exam->title) ?></h5>

                    <?php if ($exam->description): ?>
                        <p class="text-muted"><?= esc($exam->description) ?></p>
                        <hr>
                    <?php endif; ?>

                    <!-- Exam Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="bi bi-clock-fill text-primary"></i> Duration</h6>
                            <p class="ms-4"><?= $exam->duration_minutes ?> minutes</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-question-circle-fill text-info"></i> Total Questions</h6>
                            <p class="ms-4"><?= $exam->total_questions ?> questions</p>
                        </div>
                    </div>

                    <!-- Subject Distribution -->
                    <?php if (!empty($exam->subject_distribution)): ?>
                        <h6><i class="bi bi-book-fill text-success"></i> Subject Distribution</h6>
                        <ul class="ms-4 mb-4">
                            <?php foreach ($exam->subject_distribution as $dist): ?>
                                <li><?= esc($dist->subject_name) ?>: <strong><?= $dist->number_of_questions ?> questions</strong></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Marking Scheme -->
                    <h6><i class="bi bi-calculator-fill text-warning"></i> Marking Scheme</h6>
                    <ul class="ms-4 mb-4">
                        <li>Correct Answer: <span class="badge bg-success">+<?= $exam->marks_per_question ?> mark(s)</span></li>
                        <?php if ($exam->has_negative_marking): ?>
                            <li>Wrong Answer: <span class="badge bg-danger">-<?= $exam->negative_marks_per_question ?> mark(s)</span></li>
                        <?php else: ?>
                            <li>Wrong Answer: <span class="badge bg-secondary">0 marks</span></li>
                        <?php endif; ?>
                        <li>Unanswered: <span class="badge bg-secondary">0 marks</span></li>
                        <li>Pass Percentage: <span class="badge bg-info"><?= $exam->pass_percentage ?>%</span></li>
                    </ul>

                    <!-- Important Rules -->
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle-fill"></i> Important Rules</h6>
                        <ul class="mb-0">
                            <li><strong>Strict Timing:</strong> The exam will automatically submit when time expires.</li>
                            <li><strong>Auto-Save:</strong> Your answers are automatically saved when you select an option.</li>
                            <li><strong>Single Attempt:</strong> You can attempt each question only once per session.</li>

                            <?php if ($exam->prevent_tab_switch): ?>
                                <li class="text-danger">
                                    <strong>Tab Switching:</strong> Do NOT switch tabs or minimize your browser.
                                    You are allowed maximum <strong><?= $exam->max_tab_switches_allowed ?> tab switches</strong>.
                                    Exceeding this limit will result in automatic submission of your exam.
                                </li>
                            <?php endif; ?>

                            <?php if ($exam->randomize_questions): ?>
                                <li><strong>Randomized:</strong> Questions will appear in random order.</li>
                            <?php endif; ?>

                            <?php if ($exam->randomize_options): ?>
                                <li><strong>Shuffled Options:</strong> Answer options will be in random order.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Navigation Tips -->
                    <div class="alert alert-info">
                        <h6><i class="bi bi-lightbulb-fill"></i> Navigation Tips</h6>
                        <ul class="mb-0">
                            <li>All questions are displayed on a single page for easy navigation.</li>
                            <li>Use the question palette on the left to jump to any question.</li>
                            <li>Questions are color-coded:
                                <ul>
                                    <li><span class="badge bg-success">Green</span> - Answered</li>
                                    <li><span class="badge bg-danger">Red</span> - Visited but not answered</li>
                                    <li><span class="badge bg-secondary">Gray</span> - Not visited</li>
                                </ul>
                            </li>
                            <li>You can review and change your answers before final submission.</li>
                        </ul>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I have read and understood all the instructions above
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="/dashboard" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                        <form action="/exam/start/<?= $exam->id ?>" method="POST" id="startExamForm">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-lg" id="startExamBtn" disabled>
                                <i class="bi bi-play-circle-fill"></i> Start Exam Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Enable start button only when checkbox is checked
document.getElementById('agreeTerms').addEventListener('change', function() {
    document.getElementById('startExamBtn').disabled = !this.checked;
});

// Prevent accidental form resubmission
document.getElementById('startExamForm').addEventListener('submit', function() {
    document.getElementById('startExamBtn').disabled = true;
    document.getElementById('startExamBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Starting...';
});
</script>
<?= $this->endSection() ?>
