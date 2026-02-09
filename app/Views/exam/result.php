<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <!-- Print-only Header -->
    <div class="print-header d-none">
        <h3>MOCK TEST PLATFORM - EXAMINATION RESULT</h3>
        <div class="print-header-details">
            <div><strong>Student Name:</strong> <?= esc(auth()->user()->username) ?></div>
            <div><strong>Roll No/ID:</strong> <?= esc(auth()->user()->id) ?></div>
            <div><strong>Exam Title:</strong> <?= esc($exam->title) ?></div>
            <div><strong>Session ID:</strong> <?= $session->id ?></div>
            <div><strong>Exam Date:</strong> <?= date('d M Y', strtotime($session->actual_submit_time)) ?></div>
            <div><strong>Exam Time:</strong> <?= date('H:i A', strtotime($session->actual_submit_time)) ?> IST</div>
        </div>
    </div>

    <!-- Print-optimized Score Card -->
    <div class="score-card-print d-none">
        <h2>EXAMINATION SCORE</h2>
        <div class="display-1 fw-bold"><?= number_format($session->final_score, 2) ?></div>
        <h4>out of <?= $exam->total_questions * $exam->marks_per_question ?> marks</h4>
        <h3><?= number_format($session->percentage, 2) ?>%</h3>
        <p class="mt-3">
            <span class="badge">✓ Correct: <?= $session->correct_answers ?></span>
            <span class="badge">✗ Wrong: <?= $session->wrong_answers ?></span>
            <span class="badge">○ Unanswered: <?= $session->unanswered ?></span>
        </p>
        <?php if ($session->percentage >= $exam->pass_percentage): ?>
            <div style="border: 2px solid #000; padding: 10px; margin-top: 10px;">
                <strong>RESULT: PASS</strong>
            </div>
        <?php else: ?>
            <div style="border: 2px solid #000; padding: 10px; margin-top: 10px;">
                <strong>RESULT: FAIL</strong>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= session('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Result Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-trophy-fill"></i> Exam Result Summary</h4>
                </div>
                <div class="card-body">
                    <h5 class="text-primary"><?= esc($exam->title) ?></h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar"></i> Completed on: <?= date('d M Y, H:i', strtotime($session->actual_submit_time)) ?>
                    </p>
                    <?php if ($session->status === 'terminated'): ?>
                        <div class="alert alert-danger mt-2 mb-0">
                            <i class="bi bi-x-circle-fill"></i> <strong>Exam Terminated:</strong> <?= esc($session->terminated_reason) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Card -->
    <div class="row mb-4 d-print-none">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center py-5">
                    <h2 class="mb-3"><i class="bi bi-award-fill"></i> YOUR SCORE</h2>
                    <div class="display-1 fw-bold mb-2"><?= number_format($session->final_score, 2) ?></div>
                    <h4 class="mb-3">out of <?= $exam->total_questions * $exam->marks_per_question ?></h4>

                    <?php
                    $percentage = $session->percentage;
                    $passed = $percentage >= $exam->pass_percentage;
                    ?>

                    <div class="mb-3">
                        <h1 class="display-3 fw-bold"><?= number_format($percentage, 2) ?>%</h1>
                    </div>

                    <?php if ($passed): ?>
                        <div class="alert alert-success bg-white text-success border-0 shadow">
                            <h5><i class="bi bi-check-circle-fill"></i> Congratulations! You Passed</h5>
                            <p class="mb-0">Pass Percentage: <?= $exam->pass_percentage ?>%</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning bg-white text-warning border-0 shadow">
                            <h5><i class="bi bi-x-circle-fill"></i> Keep Trying!</h5>
                            <p class="mb-0">Pass Percentage: <?= $exam->pass_percentage ?>%</p>
                        </div>
                    <?php endif; ?>

                    <!-- Score Breakdown -->
                    <div class="row text-start bg-white bg-opacity-10 rounded p-3 mt-3">
                        <div class="col-6 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i> <strong>Correct:</strong> <?= $session->correct_answers ?>
                        </div>
                        <div class="col-6 mb-2">
                            <i class="bi bi-x-circle-fill text-danger"></i> <strong>Wrong:</strong> <?= $session->wrong_answers ?>
                        </div>
                        <div class="col-6 mb-2">
                            <i class="bi bi-circle text-secondary"></i> <strong>Unanswered:</strong> <?= $session->unanswered ?>
                        </div>
                        <div class="col-6 mb-2">
                            <i class="bi bi-hourglass-split"></i> <strong>Attempted:</strong> <?= $session->total_questions_attempted ?>
                        </div>
                    </div>

                    <?php if ($exam->has_negative_marking): ?>
                        <div class="mt-3 bg-white bg-opacity-10 rounded p-3">
                            <div class="row text-start">
                                <div class="col-6">
                                    <small>Raw Score:</small><br>
                                    <strong><?= number_format($session->raw_score, 2) ?></strong>
                                </div>
                                <div class="col-6">
                                    <small>Penalty:</small><br>
                                    <strong class="text-danger">-<?= number_format($session->raw_score - $session->final_score, 2) ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise Analysis -->
    <?php if (!empty($subjectResults)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Subject-wise Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Total Questions</th>
                                        <th>Correct</th>
                                        <th>Wrong</th>
                                        <th>Unanswered</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjectResults as $result): ?>
                                        <?php
                                        $maxScoreSubject = $result->total_questions_in_subject * $exam->marks_per_question;
                                        $percentageSubject = ($result->score_obtained / $maxScoreSubject) * 100;
                                        ?>
                                        <tr>
                                            <td><strong><?= esc($result->subject_name) ?></strong></td>
                                            <td><?= $result->total_questions_in_subject ?></td>
                                            <td class="text-success"><i class="bi bi-check-circle-fill"></i> <?= $result->correct_answers ?></td>
                                            <td class="text-danger"><i class="bi bi-x-circle-fill"></i> <?= $result->wrong_answers ?></td>
                                            <td class="text-secondary"><?= $result->unanswered ?></td>
                                            <td><strong><?= number_format($result->score_obtained, 2) ?></strong> / <?= $maxScoreSubject ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'secondary';
                                                if ($percentageSubject >= 75) $badgeClass = 'success';
                                                elseif ($percentageSubject >= 50) $badgeClass = 'warning';
                                                elseif ($percentageSubject >= 40) $badgeClass = 'info';
                                                else $badgeClass = 'danger';
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>"><?= number_format($percentageSubject, 1) ?>%</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar bg-<?= $badgeClass ?>"
                                                         role="progressbar"
                                                         style="width: <?= $percentageSubject ?>%"
                                                         aria-valuenow="<?= $percentageSubject ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        <?= number_format($percentageSubject, 0) ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Visual Chart -->
                        <div class="mt-4 d-print-none">
                            <h6>Performance Comparison</h6>
                            <canvas id="subjectChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Additional Details -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle-fill"></i> Exam Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td><?= $exam->duration_minutes ?> minutes</td>
                        </tr>
                        <tr>
                            <td><strong>Total Questions:</strong></td>
                            <td><?= $exam->total_questions ?></td>
                        </tr>
                        <tr>
                            <td><strong>Marks per Question:</strong></td>
                            <td>+<?= $exam->marks_per_question ?></td>
                        </tr>
                        <?php if ($exam->has_negative_marking): ?>
                            <tr>
                                <td><strong>Negative Marking:</strong></td>
                                <td class="text-danger">-<?= $exam->negative_marks_per_question ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Pass Percentage:</strong></td>
                            <td><?= $exam->pass_percentage ?>%</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-3 mt-md-0">
            <div class="card shadow h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Accuracy Rate</span>
                            <strong><?= $session->total_questions_attempted > 0 ? number_format(($session->correct_answers / $session->total_questions_attempted) * 100, 1) : 0 ?>%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <?php $accuracy = $session->total_questions_attempted > 0 ? ($session->correct_answers / $session->total_questions_attempted) * 100 : 0; ?>
                            <div class="progress-bar bg-success" style="width: <?= $accuracy ?>%"><?= number_format($accuracy, 0) ?>%</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Attempt Rate</span>
                            <strong><?= number_format(($session->total_questions_attempted / $exam->total_questions) * 100, 1) ?>%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <?php $attemptRate = ($session->total_questions_attempted / $exam->total_questions) * 100; ?>
                            <div class="progress-bar bg-info" style="width: <?= $attemptRate ?>%"><?= number_format($attemptRate, 0) ?>%</div>
                        </div>
                    </div>

                    <?php if ($exam->prevent_tab_switch): ?>
                        <hr>
                        <div class="alert alert-<?= $session->tab_switch_count > 0 ? 'warning' : 'success' ?> mb-0">
                            <small>
                                <strong>Tab Switches:</strong> <?= $session->tab_switch_count ?> / <?= $exam->max_tab_switches_allowed ?> allowed
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mb-5 no-print">
        <div class="col-12 text-center">
            <a href="/dashboard" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-house-fill"></i> Back to Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-printer-fill"></i> Print Result
            </button>
        </div>
    </div>

    <!-- Print-only Footer -->
    <div class="print-footer d-none">
        <p style="margin: 0;">
            Generated on: <?= date('d M Y, H:i:s') ?> IST | Mock Test Platform
        </p>
        <p style="margin: 5px 0 0 0; font-size: 8pt;">
            This is a system-generated result. No signature required.
        </p>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js for visual charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Subject-wise performance chart
<?php if (!empty($subjectResults)): ?>
const ctx = document.getElementById('subjectChart').getContext('2d');
const subjectChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($subjectResults, 'subject_name')) ?>,
        datasets: [
            {
                label: 'Correct',
                data: <?= json_encode(array_column($subjectResults, 'correct_answers')) ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            },
            {
                label: 'Wrong',
                data: <?= json_encode(array_column($subjectResults, 'wrong_answers')) ?>,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            },
            {
                label: 'Unanswered',
                data: <?= json_encode(array_column($subjectResults, 'unanswered')) ?>,
                backgroundColor: 'rgba(156, 163, 175, 0.8)',
                borderColor: 'rgb(156, 163, 175)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Questions Distribution by Subject'
            }
        }
    }
});
<?php endif; ?>

// Enhanced Print styles
const style = document.createElement('style');
style.textContent = `
    @media print {
        /* Hide non-print elements */
        .navbar, .sidebar, .btn, .no-print,
        .alert, .dropdown, .modal {
            display: none !important;
        }

        /* Page setup */
        @page {
            size: A4;
            margin: 2cm 1.5cm;
        }

        /* Body and layout */
        body {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            color: #000;
            background: white;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100%;
        }

        .container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        /* Print header */
        .print-header {
            display: block !important;
            border: 2px solid #333;
            padding: 15px;
            margin-bottom: 20px;
            page-break-after: avoid;
        }

        .print-header h3 {
            text-align: center;
            margin: 0 0 10px 0;
            font-size: 16pt;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .print-header-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 11pt;
        }

        /* Score card for print */
        .score-card-print {
            display: block !important;
            border: 3px solid #333;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #f8f9fa !important;
        }

        .score-card-print .display-1 {
            font-size: 48pt;
            color: #000;
            margin: 10px 0;
        }

        .score-card-print .badge {
            display: inline-block;
            padding: 8px 12px;
            border: 2px solid #000;
            color: #000;
            background: white;
            margin: 0 5px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th, table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #e9ecef;
            font-weight: bold;
        }

        /* Cards */
        .card {
            border: 1px solid #333;
            margin-bottom: 15px;
            page-break-inside: avoid;
            box-shadow: none !important;
        }

        .card-header {
            background: #e9ecef !important;
            color: #000 !important;
            border-bottom: 2px solid #333;
            padding: 10px;
            font-weight: bold;
        }

        .card-body {
            padding: 15px;
        }

        /* Progress bars */
        .progress {
            border: 1px solid #333;
            background: white !important;
        }

        .progress-bar {
            background: #333 !important;
            color: white !important;
        }

        /* Subject-wise table */
        .table-responsive {
            overflow: visible;
        }

        /* Performance metrics */
        .alert {
            border: 1px solid #333;
            background: white !important;
            color: #000 !important;
            padding: 10px;
        }

        /* Page breaks */
        .page-break {
            page-break-after: always;
        }

        .no-page-break {
            page-break-inside: avoid;
        }

        /* Print footer */
        .print-footer {
            display: block !important;
            text-align: center;
            font-size: 9pt;
            padding-top: 20px;
            margin-top: 30px;
            border-top: 1px solid #333;
        }

        /* Remove gradient backgrounds */
        .bg-primary, .bg-success, .bg-info, .bg-warning, .bg-danger {
            background: #f8f9fa !important;
            color: #000 !important;
        }

        /* Icons in print - keep for better readability */
        .bi {
            font-size: 0.9em;
        }

        /* Additional refinements */
        h1, h2, h3, h4, h5, h6 {
            page-break-after: avoid;
        }

        .row {
            page-break-inside: avoid;
        }
    }
`;
document.head.appendChild(style);
</script>
<?= $this->endSection() ?>
