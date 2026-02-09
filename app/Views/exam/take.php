<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($exam->title) ?> - Mock Test Platform</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }
        .exam-header {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .timer {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .timer.warning {
            color: #ff6b6b;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .exam-container {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }
        .question-palette {
            position: sticky;
            top: 80px;
            width: 300px;
            height: fit-content;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .palette-btn {
            width: 45px;
            height: 45px;
            margin: 5px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }
        .palette-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .palette-btn.answered {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        .palette-btn.visited {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }
        .palette-btn.not-visited {
            background: #9ca3af;
            color: white;
            border-color: #9ca3af;
        }
        .questions-section {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .question-card {
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
            scroll-margin-top: 100px;
        }
        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .subject-badge {
            background: #3b82f6;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }
        .option-label {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin: 0.75rem 0;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }
        .option-label:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: translateX(5px);
        }
        .option-label input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
            cursor: pointer;
        }
        .option-label input[type="radio"]:checked + .option-text {
            font-weight: 600;
            color: #1e40af;
        }
        .option-label.selected {
            border-color: #10b981;
            background: #d1fae5;
        }
        .clear-btn {
            margin-top: 1rem;
        }
        .palette-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }
        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        .summary-box {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        .tab-switch-warning {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        .submit-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 1.5rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            border-radius: 10px 10px 0 0;
            margin-top: 2rem;
        }
        .lang-btn {
            font-weight: bold;
            padding: 0.5rem 1.5rem;
            font-size: 1.1rem;
            border-width: 2px !important;
        }
        .lang-btn.active {
            background-color: white !important;
            color: #667eea !important;
            border-color: white !important;
        }
        .lang-btn:hover {
            background-color: rgba(255,255,255,0.2) !important;
            border-color: white !important;
        }
        .marathi-text {
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 1.1rem;
        }
        @media (max-width: 768px) {
            .exam-container {
                flex-direction: column;
            }
            .question-palette {
                position: relative;
                top: 0;
                width: 100%;
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Exam Header -->
    <div class="exam-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> <?= esc($exam->title) ?></h5>
                </div>
                <div class="col-md-4 text-center mt-2 mt-md-0">
                    <div class="btn-group" role="group" aria-label="Language Toggle">
                        <button type="button" class="btn btn-outline-light lang-btn <?= getCurrentLanguage() === 'english' ? 'active' : '' ?>" data-lang="english">
                            EN
                        </button>
                        <button type="button" class="btn btn-outline-light lang-btn <?= getCurrentLanguage() === 'marathi' ? 'active' : '' ?>" data-lang="marathi" style="font-family: 'Noto Sans Devanagari', sans-serif;">
                            मर
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <div class="timer" id="timer">
                        <i class="bi bi-clock-fill"></i> <span id="timeDisplay">--:--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Switch Warning (Hidden by default) -->
    <div id="tabSwitchWarning" class="tab-switch-warning" style="display: none;"></div>

    <!-- Main Exam Container -->
    <div class="exam-container">
        <!-- Question Palette (Left Panel) -->
        <div class="question-palette">
            <h6 class="mb-3"><i class="bi bi-grid-3x3-gap-fill"></i> Question Palette</h6>

            <!-- Legend -->
            <div class="palette-legend">
                <div class="legend-item">
                    <div class="legend-box" style="background: #10b981;"></div>
                    <span>Answered</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box" style="background: #ef4444;"></div>
                    <span>Visited</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box" style="background: #9ca3af;"></div>
                    <span>Not Visited</span>
                </div>
            </div>

            <!-- Summary -->
            <div class="summary-box">
                <div class="summary-item">
                    <span>Answered:</span>
                    <strong id="answeredCount">0</strong>
                </div>
                <div class="summary-item">
                    <span>Not Answered:</span>
                    <strong id="notAnsweredCount"><?= count($questions) ?></strong>
                </div>
            </div>

            <!-- Palette Buttons by Subject -->
            <?php
            $questionsBySubject = [];
            foreach ($questions as $index => $q) {
                $questionsBySubject[$q->subject->name][] = ['index' => $index, 'id' => $q->id];
            }
            ?>

            <?php foreach ($questionsBySubject as $subjectName => $subjectQuestions): ?>
                <div class="mb-3">
                    <h6 class="text-muted small mb-2"><?= esc($subjectName) ?></h6>
                    <div>
                        <?php foreach ($subjectQuestions as $item): ?>
                            <?php
                            $isAnswered = isset($answeredMap[$questions[$item['index']]->id]);
                            $statusClass = $isAnswered ? 'answered' : 'not-visited';
                            ?>
                            <button
                                class="palette-btn <?= $statusClass ?>"
                                data-question-index="<?= $item['index'] ?>"
                                data-question-id="<?= $questions[$item['index']]->id ?>"
                                onclick="scrollToQuestion(<?= $item['index'] ?>)">
                                <?= $item['index'] + 1 ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Questions Section (Right Panel) -->
        <div class="questions-section">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card" id="question-<?= $index ?>" data-question-id="<?= $question->id ?>">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="question-number">Question <?= $index + 1 ?></span>
                            <span class="subject-badge"><?= esc($question->subject->name) ?></span>
                        </div>
                    </div>

                    <div class="question-text mb-4">
                        <h6 class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
                            <?= lang_text($question->question_text, $question->question_text_marathi ?? null) ?>
                        </h6>
                        <?php if ($question->question_image_path ?? null): ?>
                            <img src="/<?= $question->question_image_path ?>" alt="Question Image" class="img-fluid mt-2" style="max-width: 500px;">
                        <?php endif; ?>
                    </div>

                    <div class="options">
                        <?php foreach ($question->options as $optIndex => $option): ?>
                            <?php
                            $optionLabel = chr(65 + $optIndex); // A, B, C, D
                            $isSelected = isset($answeredMap[$question->id]) && $answeredMap[$question->id] == $option->id;
                            ?>
                            <label class="option-label <?= $isSelected ? 'selected' : '' ?>"
                                   data-question-id="<?= $question->id ?>"
                                   data-option-id="<?= $option->id ?>">
                                <input type="radio"
                                       name="question_<?= $question->id ?>"
                                       value="<?= $option->id ?>"
                                       <?= $isSelected ? 'checked' : '' ?>
                                       onchange="saveAnswer(<?= $question->id ?>, <?= $option->id ?>, <?= $index ?>)">
                                <span class="option-text <?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
                                    <strong><?= $optionLabel ?>.</strong> <?= lang_text($option->option_text, $option->option_text_marathi ?? null) ?>
                                    <?php if ($option->option_image): ?>
                                        <br><img src="<?= $option->option_image ?>" alt="Option Image" class="img-fluid mt-2" style="max-width: 300px;">
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button class="btn btn-outline-secondary btn-sm clear-btn" onclick="clearAnswer(<?= $question->id ?>, <?= $index ?>)">
                        <i class="bi bi-x-circle"></i> Clear Response
                    </button>
                </div>
            <?php endforeach; ?>

            <!-- Submit Section -->
            <div class="submit-section">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Review your answers before submitting!</strong>
                    You have answered <strong id="finalAnsweredCount">0</strong> out of <?= count($questions) ?> questions.
                </div>
                <button class="btn btn-success btn-lg w-100" onclick="confirmSubmit()">
                    <i class="bi bi-check-circle-fill"></i> Submit Exam
                </button>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Confirm Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to submit the exam?</p>
                    <div class="alert alert-warning mb-0">
                        <div><strong>Answered:</strong> <span id="modalAnswered">0</span></div>
                        <div><strong>Not Answered:</strong> <span id="modalNotAnswered">0</span></div>
                        <div class="mt-2"><strong>Remaining Time:</strong> <span id="modalTime">--:--</span></div>
                    </div>
                    <p class="mt-3 mb-0 text-danger"><strong>You cannot change your answers after submission!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="/exam/submit" method="POST" id="submitForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="session_id" value="<?= $session->id ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle"></i> Yes, Submit Exam
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        // Exam Configuration
        const sessionId = <?= $session->id ?>;
        const endTime = <?= strtotime($session->end_time) * 1000 ?>;
        const startTime = <?= strtotime($session->start_time) * 1000 ?>;
        const preventTabSwitch = <?= $exam->prevent_tab_switch ? 'true' : 'false' ?>;
        const maxTabSwitches = <?= $exam->max_tab_switches_allowed ?>;
        let currentTabSwitches = <?= $session->tab_switch_count ?>;
        let answeredQuestions = new Set(<?= json_encode(array_keys($answeredMap)) ?>);

        // Debug logs (IST Timezone)
        console.log('=== Exam Session Info (IST) ===');
        console.log('Session ID:', sessionId);
        console.log('Start Time:', '<?= $session->start_time ?> IST');
        console.log('End Time:', '<?= $session->end_time ?> IST');
        console.log('Duration:', <?= $exam->duration_minutes ?>, 'minutes');
        console.log('Current Time:', new Date().toLocaleString('en-IN', {timeZone: 'Asia/Kolkata'}));
        console.log('Time Remaining:', Math.floor((endTime - Date.now()) / 1000 / 60), 'minutes');
        console.log('===============================');

        // Timer Update Function
        function updateTimer() {
            const now = Date.now();
            const remaining = endTime - now;

            if (remaining <= 0) {
                console.log('⏰ Timer expired! Auto-submitting exam...');
                autoSubmit('time_expired');
                return;
            }

            const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
            const display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            $('#timeDisplay').text(display);

            // Warning when 5 minutes left
            if (remaining < 5 * 60 * 1000) {
                $('#timer').addClass('warning');
            }
        }

        // Start timer
        setInterval(updateTimer, 1000);
        updateTimer();

        // Save Answer (AJAX with jQuery)
        function saveAnswer(questionId, optionId, questionIndex) {
            $.ajax({
                url: '/exam/save-answer',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    session_id: sessionId,
                    question_id: questionId,
                    option_id: optionId
                },
                success: function(response) {
                    if (response.success) {
                        answeredQuestions.add(questionId);
                        updatePalette(questionIndex, 'answered');
                        updateCounts();

                        // Update selected styling using jQuery
                        $(`#question-${questionIndex} .option-label`).removeClass('selected');
                        $(`[data-option-id="${optionId}"]`).addClass('selected');
                    } else if (response.expired) {
                        alert('⏰ Exam time has expired! Your exam will be submitted automatically.');
                        autoSubmit('time_expired');
                    }
                },
                error: function() {
                    console.error('Failed to save answer');
                    alert('❌ Failed to save answer. Please try again.');
                }
            });
        }

        // Clear Answer (jQuery optimized)
        function clearAnswer(questionId, questionIndex) {
            $.ajax({
                url: '/exam/clear-answer',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    session_id: sessionId,
                    question_id: questionId
                },
                success: function(response) {
                    if (response.success) {
                        answeredQuestions.delete(questionId);
                        updatePalette(questionIndex, 'visited');
                        updateCounts();

                        // Clear selection using jQuery
                        $(`#question-${questionIndex} input[type="radio"]`).prop('checked', false);
                        $(`#question-${questionIndex} .option-label`).removeClass('selected');
                    }
                }
            });
        }

        // Update Palette Button (jQuery)
        function updatePalette(questionIndex, status) {
            const questionId = $(`#question-${questionIndex}`).data('question-id');
            $(`.palette-btn[data-question-id="${questionId}"]`)
                .removeClass('answered visited not-visited')
                .addClass(status);
        }

        // Update Counts (jQuery)
        function updateCounts() {
            const answered = answeredQuestions.size;
            const total = <?= count($questions) ?>;
            const notAnswered = total - answered;

            $('#answeredCount, #finalAnsweredCount').text(answered);
            $('#notAnsweredCount').text(notAnswered);
        }

        // Scroll to Question (jQuery smooth scroll)
        function scrollToQuestion(index) {
            const $element = $(`#question-${index}`);
            $('html, body').animate({
                scrollTop: $element.offset().top - 100
            }, 500);

            // Mark as visited if not answered
            const questionId = parseInt($element.data('question-id'));
            if (!answeredQuestions.has(questionId)) {
                updatePalette(index, 'visited');
            }
        }

        // Tab Switch Detection
        if (preventTabSwitch) {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    logTabSwitch();
                }
            });

            window.addEventListener('blur', function() {
                if (document.hasFocus() === false) {
                    logTabSwitch();
                }
            });
        }

        // Log Tab Switch
        function logTabSwitch() {
            $.ajax({
                url: '/exam/log-tab-switch',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    session_id: sessionId
                },
                success: function(response) {
                    if (response.terminate) {
                        showTabSwitchTermination(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 3000);
                    } else if (response.success) {
                        showTabSwitchWarning(response.message, response.remaining);
                    }
                }
            });
        }

        // Show Tab Switch Warning (jQuery)
        function showTabSwitchWarning(message, remaining) {
            $('#tabSwitchWarning').html(`
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>⚠️ Tab Switch Detected!</strong><br>
                    ${message}<br>
                    <strong>Remaining: ${remaining}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `).show();

            setTimeout(() => $('#tabSwitchWarning').fadeOut(), 5000);
        }

        // Show Termination Message (jQuery)
        function showTabSwitchTermination(message) {
            $('#tabSwitchWarning').html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-x-circle-fill"></i>
                    <strong>🚫 Exam Terminated!</strong><br>
                    ${message}
                </div>
            `).show();
        }

        // Confirm Submit (jQuery)
        function confirmSubmit() {
            const answered = answeredQuestions.size;
            const total = <?= count($questions) ?>;
            const notAnswered = total - answered;

            $('#modalAnswered').text(answered);
            $('#modalNotAnswered').text(notAnswered);
            $('#modalTime').text($('#timeDisplay').text());

            new bootstrap.Modal($('#submitModal')[0]).show();
        }

        // Auto Submit
        function autoSubmit(reason) {
            console.log('Auto-submitting exam. Reason:', reason);
            $('#submitForm').submit();
        }

        // Language Switcher
        $('.lang-btn').on('click', function() {
            const targetLang = $(this).data('lang');
            const currentLang = $('.lang-btn.active').data('lang');

            if (targetLang === currentLang) {
                return; // Already in this language
            }

            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            // AJAX call to switch language
            $.ajax({
                url: '/exam/switch-language',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    language: targetLang
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to show content in new language
                        window.location.reload();
                    } else {
                        alert('Failed to switch language');
                        $('.lang-btn').prop('disabled', false);
                        $('.lang-btn[data-lang="' + targetLang + '"]').text(targetLang === 'english' ? 'EN' : 'मर');
                    }
                },
                error: function() {
                    alert('Failed to switch language');
                    $('.lang-btn').prop('disabled', false);
                    $('.lang-btn[data-lang="' + targetLang + '"]').text(targetLang === 'english' ? 'EN' : 'मर');
                }
            });
        });

        // Initialize
        $(document).ready(function() {
            updateCounts();
            console.log('✅ Exam interface loaded successfully (IST)');
            console.log('Current language:', $('.lang-btn.active').data('lang'));
        });
    </script>
</body>
</html>
