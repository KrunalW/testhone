<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-0">Welcome, <?= esc(auth()->user()->username) ?>!</h4>
                    <p class="text-muted mb-0">Ready to test your knowledge? Choose an exam below to get started.</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Available Exams -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="bi bi-journal-text"></i> Available Exams</h5>

            <?php if (empty($availableExams)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No exams available at the moment. Please check back later.
                </div>
            <?php else: ?>
                <?php foreach ($availableExams as $exam): ?>
                    <?php $examWithSubjects = model('ExamModel')->getExamWithSubjects($exam->id); ?>
                    <div class="card mb-3 shadow-sm exam-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-2"><?= esc($exam->title) ?></h5>
                                    <?php if ($exam->description): ?>
                                        <p class="card-text text-muted small mb-2"><?= esc($exam->description) ?></p>
                                    <?php endif; ?>

                                    <div class="exam-details">
                                        <span class="badge bg-primary me-2">
                                            <i class="bi bi-clock"></i> <?= $exam->duration_minutes ?> minutes
                                        </span>
                                        <span class="badge bg-info me-2">
                                            <i class="bi bi-question-circle"></i> <?= $exam->total_questions ?> questions
                                        </span>
                                        <span class="badge bg-success me-2">
                                            <i class="bi bi-trophy"></i> Pass: <?= $exam->pass_percentage ?>%
                                        </span>
                                        <?php if ($exam->has_negative_marking): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle"></i> Negative Marking: -<?= $exam->negative_marks_per_question ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($examWithSubjects->subject_distribution)): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Subjects:</strong>
                                                <?php
                                                $subjects = [];
                                                foreach ($examWithSubjects->subject_distribution as $dist) {
                                                    $subjects[] = $dist->subject_name . " (" . $dist->number_of_questions . ")";
                                                }
                                                echo implode(', ', $subjects);
                                                ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <?php
                                    $isScheduled = $exam->is_scheduled && $exam->scheduled_start_time;
                                    $isAvailable = true;
                                    $showCountdown = false;

                                    if ($isScheduled) {
                                        $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
                                        $startTime = new DateTime($exam->scheduled_start_time, new DateTimeZone('Asia/Kolkata'));
                                        $endTime = new DateTime($exam->scheduled_end_time, new DateTimeZone('Asia/Kolkata'));

                                        if ($now < $startTime) {
                                            $isAvailable = false;
                                            $showCountdown = true;
                                        } elseif ($now > $endTime) {
                                            $isAvailable = false;
                                        }
                                    }
                                    ?>

                                    <?php if ($showCountdown): ?>
                                        <div class="countdown-timer"
                                             data-exam-id="<?= $exam->id ?>"
                                             data-start-time="<?= $exam->scheduled_start_time ?>">
                                            <div class="text-muted mb-2">
                                                <small><i class="bi bi-calendar"></i> Exam starts at</small><br>
                                                <strong><?= date('d M Y, h:i A', strtotime($exam->scheduled_start_time)) ?></strong>
                                            </div>
                                            <div class="countdown-display bg-light p-3 rounded">
                                                <div class="row text-center">
                                                    <div class="col-3">
                                                        <h4 class="mb-0 countdown-days">--</h4>
                                                        <small class="text-muted">Days</small>
                                                    </div>
                                                    <div class="col-3">
                                                        <h4 class="mb-0 countdown-hours">--</h4>
                                                        <small class="text-muted">Hours</small>
                                                    </div>
                                                    <div class="col-3">
                                                        <h4 class="mb-0 countdown-mins">--</h4>
                                                        <small class="text-muted">Mins</small>
                                                    </div>
                                                    <div class="col-3">
                                                        <h4 class="mb-0 countdown-secs">--</h4>
                                                        <small class="text-muted">Secs</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn btn-lg btn-secondary mt-2 w-100" disabled>
                                                <i class="bi bi-lock"></i> Not Yet Available
                                            </button>
                                        </div>
                                    <?php elseif (!$isAvailable): ?>
                                        <div class="alert alert-danger mb-0">
                                            <i class="bi bi-x-circle"></i> Exam time has expired
                                        </div>
                                    <?php else: ?>
                                        <a href="/exam/instructions/<?= $exam->id ?>" class="btn btn-lg btn-primary">
                                            <i class="bi bi-play-circle"></i> Start Exam
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Previous Attempts -->
    <?php if (!empty($previousAttempts)): ?>
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="bi bi-clock-history"></i> Previous Attempts</h5>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($previousAttempts as $attempt): ?>
                                        <tr>
                                            <td><?= esc($attempt->exam_title) ?></td>
                                            <td>
                                                <strong><?= number_format($attempt->final_score, 2) ?></strong>
                                                <small class="text-muted">/ <?= $attempt->correct_answers + $attempt->wrong_answers + $attempt->unanswered ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $percentage = $attempt->percentage;
                                                $badgeClass = 'secondary';
                                                if ($percentage >= 75) $badgeClass = 'success';
                                                elseif ($percentage >= 50) $badgeClass = 'warning';
                                                elseif ($percentage >= 40) $badgeClass = 'info';
                                                else $badgeClass = 'danger';
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>"><?= number_format($percentage, 2) ?>%</span>
                                            </td>
                                            <td>
                                                <?php if ($attempt->status === 'completed'): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php elseif ($attempt->status === 'terminated'): ?>
                                                    <span class="badge bg-danger">Terminated</span>
                                                    <small class="text-muted d-block"><?= esc($attempt->terminated_reason) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= date('d M Y, H:i', strtotime($attempt->created_at)) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                // REQUIREMENT 2: Check if result is scheduled
                                                $exam = model('ExamModel')->find($attempt->exam_id);
                                                $canViewResult = true;
                                                $showResultCountdown = false;

                                                if ($exam && $exam->is_result_scheduled && $exam->result_publish_time) {
                                                    $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
                                                    $publishTime = new DateTime($exam->result_publish_time, new DateTimeZone('Asia/Kolkata'));

                                                    if ($now < $publishTime) {
                                                        $canViewResult = false;
                                                        $showResultCountdown = true;
                                                    }
                                                }
                                                ?>

                                                <?php if ($showResultCountdown): ?>
                                                    <div class="result-countdown-timer"
                                                         data-session-id="<?= $attempt->id ?>"
                                                         data-publish-time="<?= $exam->result_publish_time ?>">
                                                        <div class="text-muted small mb-1">
                                                            <i class="bi bi-clock"></i> Results at:<br>
                                                            <strong><?= date('d M, h:i A', strtotime($exam->result_publish_time)) ?></strong>
                                                        </div>
                                                        <div class="result-countdown-display bg-light p-2 rounded small">
                                                            <span class="result-countdown-time">--:--:--</span>
                                                        </div>
                                                    </div>
                                                <?php elseif ($canViewResult): ?>
                                                    <a href="/exam/result/<?= $attempt->id ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View Report
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.exam-card {
    transition: transform 0.2s;
}
.exam-card:hover {
    transform: translateY(-2px);
}
.exam-details .badge {
    font-size: 0.85rem;
    padding: 0.4em 0.6em;
}
.countdown-display h4 {
    color: #0d6efd;
    font-weight: bold;
}
/* REQUIREMENT 2: Result countdown styling */
.result-countdown-display {
    text-align: center;
    min-width: 120px;
}
.result-countdown-time {
    font-weight: bold;
    color: #0d6efd;
    font-size: 0.9rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Exam start countdown timers
    const countdownTimers = document.querySelectorAll('.countdown-timer');

    countdownTimers.forEach(function(timer) {
        const startTime = timer.dataset.startTime;
        const targetDate = new Date(startTime).getTime();

        const daysElem = timer.querySelector('.countdown-days');
        const hoursElem = timer.querySelector('.countdown-hours');
        const minsElem = timer.querySelector('.countdown-mins');
        const secsElem = timer.querySelector('.countdown-secs');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                // Time's up, reload page to show exam is available
                location.reload();
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysElem.textContent = days.toString().padStart(2, '0');
            hoursElem.textContent = hours.toString().padStart(2, '0');
            minsElem.textContent = minutes.toString().padStart(2, '0');
            secsElem.textContent = seconds.toString().padStart(2, '0');
        }

        // Update immediately
        updateCountdown();

        // Update every second
        setInterval(updateCountdown, 1000);
    });

    // REQUIREMENT 2: Result publication countdown timers
    const resultCountdownTimers = document.querySelectorAll('.result-countdown-timer');

    resultCountdownTimers.forEach(function(timer) {
        const publishTime = timer.dataset.publishTime;
        const targetDate = new Date(publishTime).getTime();

        const timeElem = timer.querySelector('.result-countdown-time');

        function updateResultCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                // Results are published, reload page to show View Report button
                location.reload();
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Format as "Dd HH:MM:SS" or "HH:MM:SS"
            let timeString = '';
            if (days > 0) {
                timeString = days + 'd ' + hours.toString().padStart(2, '0') + ':' +
                            minutes.toString().padStart(2, '0') + ':' +
                            seconds.toString().padStart(2, '0');
            } else {
                timeString = hours.toString().padStart(2, '0') + ':' +
                            minutes.toString().padStart(2, '0') + ':' +
                            seconds.toString().padStart(2, '0');
            }

            timeElem.textContent = timeString;
        }

        // Update immediately
        updateResultCountdown();

        // Update every second
        setInterval(updateResultCountdown, 1000);
    });
});
</script>

<?= $this->endSection() ?>
