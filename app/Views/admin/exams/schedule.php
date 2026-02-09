<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/admin/exams" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Exams
            </a>
            <h2>Schedule Exam</h2>
            <p class="text-muted">Set the date and time for: <strong><?= esc($exam->title) ?></strong></p>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Schedule Settings</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/exams/update-schedule/<?= $exam->id ?>" method="POST" id="scheduleForm">
                        <?= csrf_field() ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Important:</strong> Once you set the schedule and activate the exam, students will see a countdown timer until the scheduled start time. The exam will only be accessible during the scheduled time window.
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_start_time" class="form-label">Scheduled Start Time *</label>
                            <input type="datetime-local"
                                   class="form-control"
                                   id="scheduled_start_time"
                                   name="scheduled_start_time"
                                   value="<?= old('scheduled_start_time', $exam->scheduled_start_time ? date('Y-m-d\TH:i', strtotime($exam->scheduled_start_time)) : '') ?>"
                                   required>
                            <small class="text-muted">IST Timezone (Asia/Kolkata)</small>
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_end_time" class="form-label">Scheduled End Time *</label>
                            <input type="datetime-local"
                                   class="form-control"
                                   id="scheduled_end_time"
                                   name="scheduled_end_time"
                                   value="<?= old('scheduled_end_time', $exam->scheduled_end_time ? date('Y-m-d\TH:i', strtotime($exam->scheduled_end_time)) : '') ?>"
                                   required>
                            <small class="text-muted">Students can start the exam only before this time</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Exam Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft" <?= old('status', $exam->status) === 'draft' ? 'selected' : '' ?>>
                                    Draft (Not visible to students)
                                </option>
                                <option value="scheduled" <?= old('status', $exam->status) === 'scheduled' ? 'selected' : '' ?>>
                                    Scheduled (Visible with countdown timer)
                                </option>
                                <option value="active" <?= old('status', $exam->status) === 'active' ? 'selected' : '' ?>>
                                    Active (Available for students to take)
                                </option>
                                <option value="completed" <?= old('status', $exam->status) === 'completed' ? 'selected' : '' ?>>
                                    Completed (Exam finished, results available)
                                </option>
                                <option value="archived" <?= old('status', $exam->status) === 'archived' ? 'selected' : '' ?>>
                                    Archived (Hidden from students)
                                </option>
                            </select>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Status Guide:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Draft:</strong> Exam is being created, not visible to students</li>
                                <li><strong>Scheduled:</strong> Exam is scheduled, students see countdown timer</li>
                                <li><strong>Active:</strong> Exam is currently available for students to take</li>
                                <li><strong>Completed:</strong> Exam period is over, results are available</li>
                                <li><strong>Archived:</strong> Exam is archived and hidden from students</li>
                            </ul>
                        </div>

                        <div id="timeWarning" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Warning:</strong> <span id="timeWarningMessage"></span>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/exams" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check"></i> Save Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Exam Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Total Questions:</dt>
                        <dd class="col-sm-6"><?= $exam->total_questions ?></dd>

                        <dt class="col-sm-6">Duration:</dt>
                        <dd class="col-sm-6"><?= $exam->duration_minutes ?> minutes</dd>

                        <dt class="col-sm-6">Marks per Question:</dt>
                        <dd class="col-sm-6"><?= $exam->marks_per_question ?></dd>

                        <dt class="col-sm-6">Negative Marking:</dt>
                        <dd class="col-sm-6">
                            <?php if ($exam->has_negative_marking): ?>
                                Yes (-<?= $exam->negative_marks_per_question ?>)
                            <?php else: ?>
                                No
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-6">Max Tab Switches:</dt>
                        <dd class="col-sm-6">
                            <?= $exam->max_tab_switches_allowed == 0 ? 'Unlimited' : $exam->max_tab_switches_allowed ?>
                        </dd>

                        <dt class="col-sm-6">Current Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-secondary"><?= ucfirst($exam->status) ?></span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Countdown Preview</h5>
                </div>
                <div class="card-body text-center" id="countdownPreview">
                    <p class="text-muted">Set start time to see countdown preview</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let countdownInterval;

$(document).ready(function() {
    // Validate times
    $('#scheduled_start_time, #scheduled_end_time').on('change', function() {
        validateTimes();
        updateCountdownPreview();
    });

    // Initial validation
    validateTimes();
    updateCountdownPreview();
});

function validateTimes() {
    const startTime = $('#scheduled_start_time').val();
    const endTime = $('#scheduled_end_time').val();

    if (!startTime || !endTime) {
        $('#timeWarning').hide();
        return;
    }

    const start = new Date(startTime);
    const end = new Date(endTime);
    const now = new Date();

    if (end <= start) {
        $('#timeWarningMessage').text('End time must be after start time');
        $('#timeWarning').show();
        return;
    }

    if (start < now) {
        $('#timeWarningMessage').text('Start time is in the past. Students will be able to start immediately if status is set to "Active".');
        $('#timeWarning').show();
        return;
    }

    $('#timeWarning').hide();
}

function updateCountdownPreview() {
    const startTime = $('#scheduled_start_time').val();

    if (!startTime) {
        $('#countdownPreview').html('<p class="text-muted">Set start time to see countdown preview</p>');
        if (countdownInterval) clearInterval(countdownInterval);
        return;
    }

    const targetTime = new Date(startTime).getTime();

    if (countdownInterval) clearInterval(countdownInterval);

    countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance < 0) {
            $('#countdownPreview').html(`
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i>
                    <strong>Exam Time!</strong>
                    <p class="mb-0 mt-2">The scheduled time has arrived.</p>
                </div>
            `);
            clearInterval(countdownInterval);
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $('#countdownPreview').html(`
            <h3 class="mb-3">Time Remaining</h3>
            <div class="row">
                <div class="col-3">
                    <div class="bg-primary text-white rounded p-2">
                        <h4 class="mb-0">${days}</h4>
                        <small>Days</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-primary text-white rounded p-2">
                        <h4 class="mb-0">${hours}</h4>
                        <small>Hours</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-primary text-white rounded p-2">
                        <h4 class="mb-0">${minutes}</h4>
                        <small>Mins</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-primary text-white rounded p-2">
                        <h4 class="mb-0">${seconds}</h4>
                        <small>Secs</small>
                    </div>
                </div>
            </div>
            <p class="text-muted mt-3 mb-0">
                <small>This is how students will see the countdown</small>
            </p>
        `);
    }, 1000);
}

// Cleanup interval when leaving page
$(window).on('beforeunload', function() {
    if (countdownInterval) clearInterval(countdownInterval);
});
</script>
<?= $this->endSection() ?>
