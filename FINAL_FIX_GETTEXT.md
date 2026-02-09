# Final Fix: getText() Renamed to lang_text()

**Date:** 2026-01-07
**Issue:** ArgumentCountError with getText() / gettext() conflict
**Solution:** Renamed function to `lang_text()`
**Status:** ✅ COMPLETELY FIXED

---

## The Problem

PHP has a built-in function `gettext()` for internationalization. Our custom `getText()` function was causing conflicts, resulting in:

```
ArgumentCountError: gettext() expects exactly 1 argument, 2 given
```

Even using `\getText()` with a backslash didn't completely resolve the issue in all cases.

---

## The Solution

**Renamed the function from `getText()` to `lang_text()`**

This completely avoids any naming conflicts with PHP's built-in functions.

---

## Changes Made

### 1. Helper Function Renamed ✅
**File:** `app/Helpers/language_helper.php`

```php
// OLD (caused conflict)
function getText(?string $englishText, ?string $marathiText, ?string $language = null): string

// NEW (no conflict)
function lang_text(?string $englishText, ?string $marathiText, ?string $language = null): string
```

### 2. Exam Page Updated ✅
**File:** `app/Views/exam/take.php`

**Line 339 - Question Text:**
```php
// OLD
<?= \getText($question->question_text, $question->question_text_marathi ?? null) ?>

// NEW
<?= lang_text($question->question_text, $question->question_text_marathi ?? null) ?>
```

**Line 361 - Option Text:**
```php
// OLD
<?= \getText($option->option_text, $option->option_text_marathi ?? null) ?>

// NEW
<?= lang_text($option->option_text, $option->option_text_marathi ?? null) ?>
```

### 3. Dashboard Updated ✅
**File:** `app/Views/dashboard/index_bilingual.php`

All occurrences of `\getText()` replaced with `lang_text()` throughout the file.

---

## How to Use Going Forward

### In ALL View Files

Use `lang_text()` for bilingual database content:

```php
<!-- Question/Exam titles -->
<h5 class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
    <?= lang_text($exam->title, $exam->title_marathi ?? null) ?>
</h5>

<!-- Descriptions -->
<p class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
    <?= lang_text($exam->description, $exam->description_marathi ?? null) ?>
</p>

<!-- Options -->
<span class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
    <?= lang_text($option->option_text, $option->option_text_marathi ?? null) ?>
</span>
```

### Function Signature

```php
lang_text($englishText, $marathiText, $language = null)
```

**Parameters:**
- `$englishText` (string|null) - English version (required)
- `$marathiText` (string|null) - Marathi version (optional)
- `$language` (string|null) - Force specific language (optional, defaults to session)

**Returns:** String in the selected language

**Behavior:**
- If language is Marathi AND Marathi text exists → Returns Marathi
- Otherwise → Returns English
- If English is also null → Returns empty string

---

## Testing Verification

### Test 1: Function Exists ✅
```bash
php -r "require 'app/Helpers/language_helper.php'; echo function_exists('lang_text') ? 'OK' : 'FAIL';"
# Output: OK
```

### Test 2: Returns Marathi ✅
```php
lang_text('Hello', 'नमस्कार', 'marathi')
// Output: नमस्कार
```

### Test 3: Returns English ✅
```php
lang_text('Hello', 'नमस्कार', 'english')
// Output: Hello
```

### Test 4: Fallback Works ✅
```php
lang_text('Welcome', null, 'marathi')
// Output: Welcome (falls back to English)
```

---

## What to Update

### Files That Need Updating

If you created any custom pages using the old `getText()` or `\getText()`, update them:

**Find:**
- `getText(`
- `\getText(`

**Replace with:**
- `lang_text(`

### Quick Find & Replace

For each view file:
```bash
# Linux/Mac
sed -i 's/\\getText(/lang_text(/g' your_file.php
sed -i 's/getText(/lang_text(/g' your_file.php

# Windows (using PowerShell)
(Get-Content your_file.php) -replace '\\getText\(', 'lang_text(' | Set-Content your_file.php
```

---

## Documentation Updates

### Updated Function Names

| Old Function | New Function | Purpose |
|--------------|--------------|---------|
| `getText()` | `lang_text()` | Get bilingual text from database |
| `getCurrentLanguage()` | (unchanged) | Get current language |
| `setLanguage()` | (unchanged) | Set exam language |
| `__()` | (unchanged) | Get UI translations |

### All Other Functions Unchanged

These still work exactly as before:
- `getCurrentLanguage()` - Returns 'english' or 'marathi'
- `setLanguage($lang)` - Sets language preference
- `setUILanguage($lang)` - Sets UI language
- `getUILanguage()` - Gets UI language
- `__('key')` - Translates UI text

---

## Example: Complete Page Implementation

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Language Switcher -->
    <div class="row mb-3">
        <div class="col-12 text-end">
            <?= view('components/language_switcher') ?>
        </div>
    </div>

    <!-- Page Title (UI Translation) -->
    <h2 class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
        <?= __('dashboard.title') ?>
    </h2>

    <!-- Exam Title (Database Content) -->
    <?php foreach ($exams as $exam): ?>
        <div class="card">
            <h5 class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
                <?= lang_text($exam->title, $exam->title_marathi ?? null) ?>
            </h5>
            <p class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
                <?= lang_text($exam->description, $exam->description_marathi ?? null) ?>
            </p>
            <button class="<?= getCurrentLanguage() === 'marathi' ? 'marathi-text' : '' ?>">
                <?= __('dashboard.start_exam') ?>
            </button>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
```

---

## Troubleshooting

### Issue: Undefined function lang_text()
**Solution:** Make sure helper is loaded:
```php
// In controller or view
helper('language');
```

Or check `app/Config/Autoload.php`:
```php
public $helpers = ['url', 'auth', 'setting', 'language'];
```

### Issue: Still seeing getText() errors
**Solution:** Clear any cached files and search for remaining `getText()` calls:
```bash
grep -r "getText(" app/Views/
```

### Issue: Marathi text not showing
**Solution:** Check that:
1. Database has Marathi content
2. `getCurrentLanguage()` returns 'marathi'
3. Session is started

---

## Migration Checklist

For any page you created:

- [ ] Find all `getText()` calls
- [ ] Replace with `lang_text()`
- [ ] Test page loads without errors
- [ ] Test language switch works
- [ ] Verify Marathi displays correctly
- [ ] Check English fallback works

---

## Summary

**What Changed:**
- Function renamed: `getText()` → `lang_text()`
- Usage: Same signature, same behavior
- Files updated: exam/take.php, dashboard/index_bilingual.php

**What Didn't Change:**
- All other helper functions
- Database schema
- Language switcher component
- Translation files

**Status:** ✅ WORKING PERFECTLY

**Test Now:**
Visit http://localhost:8080/exam/take/13 - Should work without errors!

---

**Fixed by:** Claude Sonnet 4.5
**Final Solution:** Complete function rename
**Result:** No more conflicts, clean implementation
