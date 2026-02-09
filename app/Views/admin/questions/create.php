<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="/admin/questions" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Questions
            </a>
            <h2>Create New Question</h2>
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
        <!-- Question Form -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form action="/admin/questions/store" method="POST" enctype="multipart/form-data" id="questionForm">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject *</label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <option value="">Select a subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject->id ?>" <?= old('subject_id') == $subject->id ? 'selected' : '' ?>>
                                        <?= esc($subject->code) ?> - <?= esc($subject->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text (English) *</label>
                            <textarea class="form-control"
                                      id="question_text"
                                      name="question_text"
                                      rows="4"
                                      required><?= old('question_text') ?></textarea>
                            <small class="text-muted">Enter the question text in English (minimum 10 characters)</small>
                        </div>

                        <div class="mb-3">
                            <label for="question_text_marathi" class="form-label">Question Text (Marathi) <span class="badge bg-secondary">Optional</span></label>
                            <textarea class="form-control"
                                      id="question_text_marathi"
                                      name="question_text_marathi"
                                      rows="4"
                                      style="font-family: 'Noto Sans Devanagari', sans-serif; font-size: 1.1rem;"><?= old('question_text_marathi') ?></textarea>
                            <small class="text-muted">प्रश्नाचे मराठीत भाषांतर प्रविष्ट करा</small>
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type *</label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="text" <?= old('question_type') === 'text' ? 'selected' : '' ?>>Text Only</option>
                                <option value="image" <?= old('question_type') === 'image' ? 'selected' : '' ?>>With Image</option>
                            </select>
                        </div>

                        <div class="mb-3" id="question_image_container" style="display: none;">
                            <label for="question_image" class="form-label">Question Image</label>
                            <input type="file"
                                   class="form-control"
                                   id="question_image"
                                   name="question_image"
                                   accept="image/*">
                            <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Options</h5>

                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Option <?= $i ?></h6>

                                    <div class="mb-3">
                                        <label for="option_<?= $i ?>_text" class="form-label">Option Text (English) *</label>
                                        <input type="text"
                                               class="form-control option-text"
                                               id="option_<?= $i ?>_text"
                                               name="option_<?= $i ?>_text"
                                               data-option="<?= $i ?>"
                                               value="<?= old("option_{$i}_text") ?>"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="option_<?= $i ?>_text_marathi" class="form-label">Option Text (Marathi) <span class="badge bg-secondary">Optional</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="option_<?= $i ?>_text_marathi"
                                               name="option_<?= $i ?>_text_marathi"
                                               value="<?= old("option_{$i}_text_marathi") ?>"
                                               style="font-family: 'Noto Sans Devanagari', sans-serif; font-size: 1.1rem;">
                                    </div>

                                    <div class="mb-3">
                                        <label for="option_<?= $i ?>_image" class="form-label">Option Image (Optional)</label>
                                        <input type="file"
                                               class="form-control"
                                               id="option_<?= $i ?>_image"
                                               name="option_<?= $i ?>_image"
                                               accept="image/*">
                                        <small class="text-muted">Max size: 1MB</small>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <div class="mb-3">
                            <label for="correct_option" class="form-label">Correct Answer *</label>
                            <select class="form-select" id="correct_option" name="correct_option" required>
                                <option value="">Select the correct option</option>
                                <option value="1" <?= old('correct_option') == 1 ? 'selected' : '' ?>>Option 1</option>
                                <option value="2" <?= old('correct_option') == 2 ? 'selected' : '' ?>>Option 2</option>
                                <option value="3" <?= old('correct_option') == 3 ? 'selected' : '' ?>>Option 3</option>
                                <option value="4" <?= old('correct_option') == 4 ? 'selected' : '' ?>>Option 4</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="explanation" class="form-label">Explanation (English) <span class="badge bg-secondary">Optional</span></label>
                            <textarea class="form-control"
                                      id="explanation"
                                      name="explanation"
                                      rows="3"><?= old('explanation') ?></textarea>
                            <small class="text-muted">Provide an explanation for the correct answer in English</small>
                        </div>

                        <div class="mb-3">
                            <label for="explanation_marathi" class="form-label">Explanation (Marathi) <span class="badge bg-secondary">Optional</span></label>
                            <textarea class="form-control"
                                      id="explanation_marathi"
                                      name="explanation_marathi"
                                      rows="3"
                                      style="font-family: 'Noto Sans Devanagari', sans-serif; font-size: 1.1rem;"><?= old('explanation_marathi') ?></textarea>
                            <small class="text-muted">योग्य उत्तराचे मराठीत स्पष्टीकरण द्या</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/questions" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="col-lg-5">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h5>
                </div>
                <div class="card-body" id="previewContainer">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-eye-slash fa-3x mb-3"></i>
                        <p>Start typing to see preview...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Show/hide question image upload based on type
    $('#question_type').on('change', function() {
        if ($(this).val() === 'image') {
            $('#question_image_container').show();
        } else {
            $('#question_image_container').hide();
            $('#question_image').val('');
        }
    }).trigger('change');

    // Live preview update
    let previewTimeout;
    let questionImagePreview = '';
    let optionImagePreviews = { 1: '', 2: '', 3: '', 4: '' };

    // Handle question image file selection for preview
    $('#question_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                questionImagePreview = e.target.result;
                updatePreview();
            };
            reader.readAsDataURL(file);
        } else {
            questionImagePreview = '';
            updatePreview();
        }
    });

    // Handle option image file selections for preview
    for (let i = 1; i <= 4; i++) {
        $(`#option_${i}_image`).on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    optionImagePreviews[i] = e.target.result;
                    updatePreview();
                };
                reader.readAsDataURL(file);
            } else {
                optionImagePreviews[i] = '';
                updatePreview();
            }
        });
    }

    function updatePreview() {
        clearTimeout(previewTimeout);

        const questionText = $('#question_text').val().trim();
        const option1 = $('#option_1_text').val().trim();
        const option2 = $('#option_2_text').val().trim();
        const option3 = $('#option_3_text').val().trim();
        const option4 = $('#option_4_text').val().trim();

        if (!questionText || !option1 || !option2 || !option3 || !option4) {
            $('#previewContainer').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-eye-slash fa-3x mb-3"></i>
                    <p>Fill in all required fields to see preview...</p>
                </div>
            `);
            return;
        }

        previewTimeout = setTimeout(function() {
            $.ajax({
                url: '/admin/questions/preview',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    question_text: questionText,
                    question_type: $('#question_type').val(),
                    question_image_path: questionImagePreview,
                    option_1_text: option1,
                    option_2_text: option2,
                    option_3_text: option3,
                    option_4_text: option4,
                    option_1_image_path: optionImagePreviews[1],
                    option_2_image_path: optionImagePreviews[2],
                    option_3_image_path: optionImagePreviews[3],
                    option_4_image_path: optionImagePreviews[4]
                },
                success: function(response) {
                    if (response.success) {
                        $('#previewContainer').html(response.html);
                    }
                }
            });
        }, 500);
    }

    // Bind events for live preview
    $('#question_text, .option-text, #question_type').on('input change', updatePreview);

    // Form validation
    $('#questionForm').on('submit', function(e) {
        const questionText = $('#question_text').val().trim();

        if (questionText.length < 10) {
            e.preventDefault();
            alert('Question text must be at least 10 characters long');
            return false;
        }

        // Check if at least one option is filled for each
        for (let i = 1; i <= 4; i++) {
            const optionText = $(`#option_${i}_text`).val().trim();
            if (!optionText) {
                e.preventDefault();
                alert(`Option ${i} text is required`);
                return false;
            }
        }

        const correctOption = $('#correct_option').val();
        if (!correctOption) {
            e.preventDefault();
            alert('Please select the correct answer');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
