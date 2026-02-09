<div class="preview-question">
    <div class="card">
        <div class="card-body">
            <div class="question-section mb-4">
                <h5 class="fw-bold mb-3">Question:</h5>

                <div class="question-content">
                    <p class="mb-3"><?= nl2br(esc($question_text)) ?></p>

                    <?php if (!empty($question_image)): ?>
                        <div class="question-image-container mb-3">
                            <?php
                            // Handle both base64 and URL paths
                            $imgSrc = $question_image;
                            if (!str_starts_with($question_image, 'data:')) {
                                // It's a URL path, ensure it starts with /
                                $imgSrc = '/' . ltrim($question_image, '/');
                            }
                            ?>
                            <img src="<?= esc($imgSrc) ?>"
                                 alt="Question image"
                                 class="img-fluid rounded border"
                                 style="max-width: 100%; max-height: 400px; display: block;"
                                 onerror="console.error('Failed to load image:', this.src);">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="options-section">
                <h6 class="fw-bold mb-3">Options:</h6>
                <?php
                $optionLabels = ['A', 'B', 'C', 'D'];
                foreach ($options as $index => $option):
                    if (empty($option['text'])) continue;
                ?>
                    <div class="form-check mb-3 p-3 border rounded option-item">
                        <div class="d-flex align-items-start">
                            <input class="form-check-input mt-1 me-2" type="radio" name="preview_option" disabled>
                            <div class="option-content flex-grow-1">
                                <label class="form-check-label">
                                    <strong><?= $optionLabels[$index] ?>.</strong>
                                    <?= nl2br(esc($option['text'])) ?>
                                </label>

                                <?php if (!empty($option['image'])): ?>
                                    <div class="option-image-container mt-2">
                                        <?php
                                        // Handle both base64 and URL paths
                                        $optImgSrc = $option['image'];
                                        if (!str_starts_with($option['image'], 'data:')) {
                                            // It's a URL path, ensure it starts with /
                                            $optImgSrc = '/' . ltrim($option['image'], '/');
                                        }
                                        ?>
                                        <img src="<?= esc($optImgSrc) ?>"
                                             alt="Option <?= $optionLabels[$index] ?> image"
                                             class="img-fluid rounded border"
                                             style="max-width: 300px; max-height: 200px; display: block;"
                                             onerror="console.error('Failed to load option image:', this.src);">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-3 mb-0">
        <i class="fas fa-info-circle"></i>
        <strong>Preview Mode:</strong> This is how the question will appear in the actual exam.
        <?php if (!empty($question_image) || array_filter($options, fn($opt) => !empty($opt['image']))): ?>
            <br><small class="text-muted">Images will be uploaded when you save the question.</small>
        <?php endif; ?>
    </div>
</div>

<style>
.preview-question .form-check {
    cursor: pointer;
    transition: all 0.2s;
}

.preview-question .form-check:hover {
    background-color: #f8f9fa;
}

.preview-question .question-text {
    font-size: 1.05rem;
    line-height: 1.6;
}

.preview-question .form-check-label {
    font-size: 0.95rem;
}
</style>
