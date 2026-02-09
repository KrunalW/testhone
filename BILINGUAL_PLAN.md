# Bilingual Implementation Plan - Marathi & English

## Version 2.0.0 (2026-01-07)

---

## Overview
Implement bilingual support allowing questions, options, and exam content to be available in both Marathi and English languages, with a language switcher for students during exams.

---

## What Needs Translation

### 1. **Question Content** (Primary Focus)
- Question text
- Option texts (all 4 options)
- Explanation text

### 2. **Subject Names** (Secondary)
- Subject names
- Subject descriptions

### 3. **Exam Content** (Secondary)
- Exam title
- Exam description
- Instructions

### 4. **UI Elements** (Optional - Phase 2)
- Button labels
- Messages
- Navigation

---

## Database Schema Changes

### Option 1: Separate Language Columns (Recommended)
**Pros:**
- Simple queries
- Fast performance
- Easy to implement
- Both languages always together

**Cons:**
- More columns
- Some data duplication

### Option 2: Separate Translation Tables
**Pros:**
- Normalized structure
- Easy to add more languages later

**Cons:**
- Complex queries
- More joins
- Slower performance

### **Decision: Use Option 1 (Separate Columns)**

---

## Database Changes Required

### 1. **Questions Table**
Add columns:
- `question_text_marathi` TEXT
- `explanation_marathi` TEXT

### 2. **Options Table**
Add column:
- `option_text_marathi` TEXT

### 3. **Subjects Table**
Add columns:
- `name_marathi` VARCHAR(100)
- `description_marathi` TEXT

### 4. **Exams Table**
Add columns:
- `title_marathi` VARCHAR(255)
- `description_marathi` TEXT
- `instructions_marathi` TEXT (new field)
- `instructions_english` TEXT (new field)

### 5. **User Preferences**
Add to users or create new table:
- Default language preference (for future use)

---

## Implementation Steps

### Phase 1: Backend & Database (Core)
1. ✅ Create migration for language columns
2. ✅ Update Models to include new fields
3. ✅ Update QuestionModel allowedFields
4. ✅ Update OptionModel allowedFields
5. ✅ Update SubjectModel allowedFields
6. ✅ Update ExamModel allowedFields

### Phase 2: Admin Panel (Content Creation)
1. ✅ Update Subject forms (create/edit) - dual input
2. ✅ Update Question forms (create/edit) - dual input
3. ✅ Update Exam forms (create/edit) - dual input
4. ✅ Show both languages in preview

### Phase 3: Student Interface (Exam Taking)
1. ✅ Add language selector in exam interface
2. ✅ Store language preference in session
3. ✅ Display questions in selected language
4. ✅ Display options in selected language
5. ✅ Allow switching language during exam
6. ✅ Language toggle button (EN/मर)

### Phase 4: Results & Review
1. ✅ Show results in user's selected language
2. ✅ Show explanations in selected language

---

## UI Design

### Language Switcher Design
```
┌─────────────────────────────────────┐
│ Question 1 of 50        [EN] [मर]  │ ← Toggle buttons
├─────────────────────────────────────┤
│                                     │
│ What is the capital of India?      │ ← English (default)
│                                     │
│ ○ New Delhi                         │
│ ○ Mumbai                            │
│ ○ Kolkata                           │
│ ○ Chennai                           │
└─────────────────────────────────────┘

After clicking [मर]:
┌─────────────────────────────────────┐
│ Question 1 of 50        [EN] [मर]  │ ← Marathi selected
├─────────────────────────────────────┤
│                                     │
│ भारताची राजधानी कोणती आहे?         │ ← Marathi
│                                     │
│ ○ नवी दिल्ली                        │
│ ○ मुंबई                             │
│ ○ कोलकाता                           │
│ ○ चेन्नई                            │
└─────────────────────────────────────┘
```

### Admin Panel - Question Creation
```
┌─────────────────────────────────────┐
│ Create Question                     │
├─────────────────────────────────────┤
│ Subject: Mathematics                │
│                                     │
│ Question (English) *                │
│ ┌─────────────────────────────────┐ │
│ │ What is 2 + 2?                  │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Question (Marathi) * मराठी          │
│ ┌─────────────────────────────────┐ │
│ │ २ + २ = किती?                   │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Option 1 (English) *                │
│ ┌─────────────────────────────────┐ │
│ │ 4                               │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Option 1 (Marathi) *                │
│ ┌─────────────────────────────────┐ │
│ │ ४                               │ │
│ └─────────────────────────────────┘ │
│                                     │
│ [... Options 2-4 similar ...]      │
│                                     │
│ [Create Question]                   │
└─────────────────────────────────────┘
```

---

## Technical Implementation

### 1. Migration File Structure
```php
// Add language columns to questions
ALTER TABLE questions
  ADD question_text_marathi TEXT NULL AFTER question_text,
  ADD explanation_marathi TEXT NULL AFTER explanation;

// Add language column to options
ALTER TABLE options
  ADD option_text_marathi TEXT NULL AFTER option_text;

// Add language columns to subjects
ALTER TABLE subjects
  ADD name_marathi VARCHAR(100) NULL AFTER name;

// Add language columns to exams
ALTER TABLE exams
  ADD title_marathi VARCHAR(255) NULL AFTER title,
  ADD description_marathi TEXT NULL AFTER description;
```

### 2. Model Updates
```php
// QuestionModel
protected $allowedFields = [
    // ... existing fields
    'question_text_marathi',
    'explanation_marathi'
];

// OptionModel
protected $allowedFields = [
    // ... existing fields
    'option_text_marathi'
];
```

### 3. Controller Logic
```php
// In ExamController
public function take($sessionId) {
    $language = session()->get('exam_language') ?? 'english';

    // Get questions with language-specific text
    $questionText = ($language === 'marathi')
        ? $question->question_text_marathi
        : $question->question_text;
}
```

### 4. View Helper Function
```php
// Helper function for language switching
function getText($englishText, $marathiText, $language = null) {
    $lang = $language ?? session()->get('exam_language') ?? 'english';
    return ($lang === 'marathi' && !empty($marathiText))
        ? $marathiText
        : $englishText;
}
```

---

## Validation Rules

### Admin Panel
- English text: REQUIRED
- Marathi text: OPTIONAL (but recommended)
- If Marathi is empty, fall back to English during exam

### Exam Interface
- Must support UTF-8 for Devanagari script
- Character encoding: UTF-8
- Font: System fonts supporting Devanagari

---

## Data Flow

### Question Creation Flow:
1. Exam Expert enters English question ✓
2. Exam Expert enters Marathi translation ✓
3. Both saved in same row ✓
4. Preview shows both languages side-by-side ✓

### Exam Taking Flow:
1. Student starts exam (default: English) ✓
2. Language stored in session ✓
3. Questions displayed in selected language ✓
4. Student can toggle language anytime ✓
5. Toggle is instant (AJAX or page refresh) ✓

---

## Performance Considerations

1. **No Additional Queries:** Both languages in same row
2. **Session Storage:** Language preference cached
3. **No Translation API:** All content pre-translated
4. **Indexing:** No need for language-specific indexes

---

## Migration Strategy

### For Existing Data:
1. Run migration (adds columns, allows NULL)
2. Existing data continues to work (English only)
3. Gradually add Marathi translations
4. System gracefully falls back to English if Marathi missing

---

## Future Enhancements (Post Phase 1)

1. ✅ Add more languages (Hindi, Gujarati, etc.)
2. ✅ Auto-translation integration (Google Translate API)
3. ✅ Bulk import translations (CSV/Excel)
4. ✅ UI language switching (entire interface)
5. ✅ Language-specific reports
6. ✅ RTL support (if needed for Urdu, etc.)

---

## Testing Checklist

- [ ] Create question with both languages
- [ ] Create question with English only
- [ ] Create question with Marathi only
- [ ] Take exam in English
- [ ] Take exam in Marathi
- [ ] Switch language mid-exam
- [ ] Verify font rendering (Devanagari)
- [ ] Check results page language
- [ ] Test with image questions
- [ ] Verify explanation display

---

**Next Step:** Create database migration and begin implementation.
