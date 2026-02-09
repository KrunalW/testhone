<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid feedback-container">
    <div class="row justify-content-center py-5">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="feedback-header text-center mb-5">
                <div class="icon-wrapper mb-3">
                    <i class="bi bi-chat-left-heart-fill"></i>
                </div>
                <h2 class="mb-2">Your Feedback Matters!</h2>
                <p class="text-muted">Help us improve your exam experience by sharing your thoughts</p>
            </div>

            <?php if (session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach (session('errors') as $error): ?>
                        <?= esc($error) ?><br>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Feedback Form -->
            <form action="/exam/submit-feedback" method="POST" id="feedbackForm" class="feedback-form">
                <?= csrf_field() ?>
                <input type="hidden" name="session_id" value="<?= $session->id ?>">
                <input type="hidden" name="exam_id" value="<?= $session->exam_id ?>">
                <input type="hidden" name="user_id" value="<?= auth()->user()->id ?>">

                <!-- Section 1: Overall Experience -->
                <div class="feedback-section">
                    <div class="section-header">
                        <i class="bi bi-star-fill"></i>
                        <h4>Overall Experience</h4>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label fw-bold mb-3">
                                How would you rate your overall exam experience?
                                <span class="text-danger">*</span>
                            </label>
                            <div class="rating-scale">
                                <div class="rating-labels">
                                    <span class="text-danger">Least Satisfied</span>
                                    <span class="text-success">Most Satisfied</span>
                                </div>
                                <div class="rating-options">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <label class="rating-option">
                                            <input type="radio" name="overall_experience_rating" value="<?= $i ?>" required>
                                            <span class="rating-number"><?= $i ?></span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Platform Experience -->
                <div class="feedback-section">
                    <div class="section-header">
                        <i class="bi bi-laptop"></i>
                        <h4>Platform Experience</h4>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Web Panel Experience -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Web Panel Experience <span class="text-danger">*</span></label>
                                    <div class="experience-options">
                                        <label class="exp-option poor">
                                            <input type="radio" name="web_panel_experience" value="poor" required>
                                            <span class="icon">😞</span>
                                            <span class="text">Poor</span>
                                        </label>
                                        <label class="exp-option below-avg">
                                            <input type="radio" name="web_panel_experience" value="below_average">
                                            <span class="icon">😐</span>
                                            <span class="text">Below Average</span>
                                        </label>
                                        <label class="exp-option average">
                                            <input type="radio" name="web_panel_experience" value="average">
                                            <span class="icon">🙂</span>
                                            <span class="text">Average</span>
                                        </label>
                                        <label class="exp-option good">
                                            <input type="radio" name="web_panel_experience" value="good">
                                            <span class="icon">😊</span>
                                            <span class="text">Good</span>
                                        </label>
                                        <label class="exp-option excellent">
                                            <input type="radio" name="web_panel_experience" value="excellent">
                                            <span class="icon">😍</span>
                                            <span class="text">Excellent</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Question Quality -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Question Quality <span class="text-danger">*</span></label>
                                    <div class="experience-options">
                                        <label class="exp-option poor">
                                            <input type="radio" name="question_quality" value="poor" required>
                                            <span class="icon">😞</span>
                                            <span class="text">Poor</span>
                                        </label>
                                        <label class="exp-option below-avg">
                                            <input type="radio" name="question_quality" value="below_average">
                                            <span class="icon">😐</span>
                                            <span class="text">Below Average</span>
                                        </label>
                                        <label class="exp-option average">
                                            <input type="radio" name="question_quality" value="average">
                                            <span class="icon">🙂</span>
                                            <span class="text">Average</span>
                                        </label>
                                        <label class="exp-option good">
                                            <input type="radio" name="question_quality" value="good">
                                            <span class="icon">😊</span>
                                            <span class="text">Good</span>
                                        </label>
                                        <label class="exp-option excellent">
                                            <input type="radio" name="question_quality" value="excellent">
                                            <span class="icon">😍</span>
                                            <span class="text">Excellent</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Intent Questions -->
                <div class="feedback-section">
                    <div class="section-header">
                        <i class="bi bi-question-circle-fill"></i>
                        <h4>Your Intent</h4>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Refer Friends -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Will you refer this mock test to your friends? <span class="text-danger">*</span></label>
                                    <div class="yes-no-options">
                                        <label class="yn-option yes">
                                            <input type="radio" name="will_refer_friends" value="1" required>
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                            <span>Yes</span>
                                        </label>
                                        <label class="yn-option no">
                                            <input type="radio" name="will_refer_friends" value="0">
                                            <i class="bi bi-hand-thumbs-down-fill"></i>
                                            <span>No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Interest in Next Test -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Interested in the next mock test? <span class="text-danger">*</span></label>
                                    <div class="yes-no-options">
                                        <label class="yn-option yes">
                                            <input type="radio" name="interested_next_test" value="1" required>
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                            <span>Yes</span>
                                        </label>
                                        <label class="yn-option no">
                                            <input type="radio" name="interested_next_test" value="0">
                                            <i class="bi bi-hand-thumbs-down-fill"></i>
                                            <span>No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Felt Same Pressure -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Did you feel the same pressure as the actual online exam? <span class="text-danger">*</span></label>
                                    <div class="yes-no-maybe-options">
                                        <label class="ynm-option yes">
                                            <input type="radio" name="felt_same_pressure" value="yes" required>
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>Yes</span>
                                        </label>
                                        <label class="ynm-option no">
                                            <input type="radio" name="felt_same_pressure" value="no">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <span>No</span>
                                        </label>
                                        <label class="ynm-option maybe">
                                            <input type="radio" name="felt_same_pressure" value="maybe">
                                            <i class="bi bi-question-circle-fill"></i>
                                            <span>Maybe</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Detailed Feedback -->
                <div class="feedback-section">
                    <div class="section-header">
                        <i class="bi bi-pencil-square"></i>
                        <h4>Share Your Thoughts</h4>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Real vs Mock Difference -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">
                                        What difference did you feel while giving the real exam and current mock test?
                                    </label>
                                    <textarea class="form-control" name="real_vs_mock_difference" rows="4"
                                              placeholder="Share your experience comparing real exam with this mock test..."></textarea>
                                </div>

                                <!-- General Feedback -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">General Feedback</label>
                                    <textarea class="form-control" name="general_feedback" rows="4"
                                              placeholder="Any suggestions, issues, or compliments? We'd love to hear from you..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Market Research -->
                <div class="feedback-section">
                    <div class="section-header">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h4>Market Research</h4>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Other Test Series -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Which other offline/online mock test series have you enrolled in?</label>
                                    <input type="text" class="form-control" name="other_test_series"
                                           placeholder="e.g., Testbook, Adda247, etc.">
                                </div>

                                <!-- Willing to Pay -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Will you be willing to pay for an expert generated mock test?</label>
                                    <div class="yes-no-options">
                                        <label class="yn-option yes">
                                            <input type="radio" name="willing_to_pay" value="1">
                                            <i class="bi bi-cash-coin"></i>
                                            <span>Yes</span>
                                        </label>
                                        <label class="yn-option no">
                                            <input type="radio" name="willing_to_pay" value="0">
                                            <i class="bi bi-x-circle"></i>
                                            <span>No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Amount Paid -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">
                                        How much have you paid for test series?
                                        <span class="badge bg-primary ms-2" id="amountDisplay">₹ 299</span>
                                    </label>
                                    <div class="range-slider-wrapper">
                                        <input type="range" class="form-range" name="amount_paid_range"
                                               id="amountRange" min="99" max="499" value="299" step="50">
                                        <div class="range-labels">
                                            <span>₹99</span>
                                            <span>₹299</span>
                                            <span>₹499</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="text-center pt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-send-fill"></i> Submit Feedback
                    </button>
                    <a href="/dashboard" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                        <i class="bi bi-arrow-left"></i> Skip for Now
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.feedback-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.feedback-header .icon-wrapper {
    display: inline-block;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.feedback-header .icon-wrapper i {
    font-size: 3rem;
    color: #667eea;
}

.feedback-header h2 {
    color: white;
    font-weight: 700;
}

.feedback-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
}

.feedback-section {
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: white;
    border-radius: 10px 10px 0 0;
    border-bottom: 3px solid #667eea;
}

.section-header i {
    font-size: 1.5rem;
    color: #667eea;
}

.section-header h4 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 0 0 10px 10px;
}

/* Rating Scale */
.rating-scale {
    padding: 1rem;
}

.rating-labels {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.rating-options {
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.rating-option {
    flex: 1;
    min-width: 50px;
    cursor: pointer;
}

.rating-option input[type="radio"] {
    display: none;
}

.rating-number {
    display: block;
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s;
    background: #f7fafc;
}

.rating-option:hover .rating-number {
    border-color: #667eea;
    transform: translateY(-2px);
}

.rating-option input[type="radio"]:checked + .rating-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    transform: scale(1.1);
}

/* Experience Options */
.experience-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.exp-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f7fafc;
}

.exp-option input[type="radio"] {
    display: none;
}

.exp-option .icon {
    font-size: 1.5rem;
}

.exp-option .text {
    font-weight: 500;
}

.exp-option:hover {
    border-color: #667eea;
    transform: translateX(5px);
}

.exp-option input[type="radio"]:checked + .icon + .text,
.exp-option input[type="radio"]:checked ~ .text {
    font-weight: 700;
}

.exp-option.poor input[type="radio"]:checked ~ * {
    color: #e53e3e;
}

.exp-option.poor:has(input:checked) {
    border-color: #e53e3e;
    background: #fff5f5;
}

.exp-option.below-avg:has(input:checked) {
    border-color: #ed8936;
    background: #fffaf0;
}

.exp-option.average:has(input:checked) {
    border-color: #ecc94b;
    background: #fffff0;
}

.exp-option.good:has(input:checked) {
    border-color: #48bb78;
    background: #f0fff4;
}

.exp-option.excellent:has(input:checked) {
    border-color: #38b2ac;
    background: #e6fffa;
}

/* Yes/No Options */
.yes-no-options, .yes-no-maybe-options {
    display: flex;
    gap: 1rem;
}

.yn-option, .ynm-option {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f7fafc;
}

.yn-option input[type="radio"],
.ynm-option input[type="radio"] {
    display: none;
}

.yn-option i, .ynm-option i {
    font-size: 2rem;
}

.yn-option span, .ynm-option span {
    font-weight: 600;
    font-size: 1.1rem;
}

.yn-option:hover, .ynm-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.yn-option.yes:has(input:checked), .ynm-option.yes:has(input:checked) {
    border-color: #48bb78;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.yn-option.no:has(input:checked), .ynm-option.no:has(input:checked) {
    border-color: #e53e3e;
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
    color: white;
}

.ynm-option.maybe:has(input:checked) {
    border-color: #ecc94b;
    background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%);
    color: white;
}

/* Range Slider */
.range-slider-wrapper {
    padding: 1rem;
}

.range-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-weight: 600;
    color: #667eea;
}

.form-range {
    width: 100%;
    height: 8px;
}

.form-range::-webkit-slider-thumb {
    width: 24px;
    height: 24px;
    background: #667eea;
    cursor: pointer;
}

.form-range::-moz-range-thumb {
    width: 24px;
    height: 24px;
    background: #667eea;
    cursor: pointer;
}

/* Textarea */
.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-outline-secondary {
    border: 2px solid #cbd5e0;
    border-radius: 50px;
    font-weight: 600;
}

.btn-outline-secondary:hover {
    background: #cbd5e0;
}

/* Responsive */
@media (max-width: 768px) {
    .rating-options {
        justify-content: center;
    }

    .rating-option {
        min-width: 45px;
    }

    .yes-no-options, .yes-no-maybe-options {
        flex-direction: column;
    }

    .yn-option, .ynm-option {
        flex-direction: row;
        justify-content: center;
    }
}
</style>

<script>
// Range slider value display
document.getElementById('amountRange').addEventListener('input', function() {
    document.getElementById('amountDisplay').textContent = '₹ ' + this.value;
});

// Form submission confirmation
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    const rating = document.querySelector('input[name="overall_experience_rating"]:checked');
    if (!rating) {
        e.preventDefault();
        alert('Please provide an overall experience rating');
        return false;
    }
});
</script>

<?= $this->endSection() ?>
