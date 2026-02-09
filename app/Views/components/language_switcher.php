<!-- Language Switcher Component -->
<div class="language-switcher-wrapper">
    <div class="btn-group language-toggle" role="group" aria-label="<?= __('language.select') ?>">
        <button type="button"
                class="btn btn-sm lang-toggle-btn <?= getCurrentLanguage() === 'english' ? 'active' : '' ?>"
                data-lang="english"
                onclick="switchGlobalLanguage('english')">
            EN
        </button>
        <button type="button"
                class="btn btn-sm lang-toggle-btn <?= getCurrentLanguage() === 'marathi' ? 'active' : '' ?>"
                data-lang="marathi"
                onclick="switchGlobalLanguage('marathi')"
                style="font-family: 'Noto Sans Devanagari', sans-serif;">
            मर
        </button>
    </div>
</div>

<style>
.language-switcher-wrapper {
    display: inline-block;
}

.language-toggle {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lang-toggle-btn {
    min-width: 45px;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
    border: 2px solid #dee2e6 !important;
    background-color: white;
    color: #495057;
    transition: all 0.3s ease;
}

.lang-toggle-btn:hover {
    background-color: #f8f9fa;
    border-color: #6c757d !important;
}

.lang-toggle-btn.active {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
}

/* Mobile responsive */
@media (max-width: 576px) {
    .lang-toggle-btn {
        min-width: 40px;
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
    }
}
</style>

<script>
function switchGlobalLanguage(language) {
    // Show loading state
    const btn = document.querySelector(`.lang-toggle-btn[data-lang="${language}"]`);
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    btn.disabled = true;

    // AJAX call to switch language
    fetch('/switch-language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            'language': language
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show new language
            window.location.reload();
        } else {
            alert('<?= __('msg.error') ?>: ' + (data.message || 'Failed to switch language'));
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?= __('msg.error') ?>: Failed to switch language');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}
</script>
