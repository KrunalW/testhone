# Image Preview Feature - Testing Guide

## Version 1.3.6 Changes

### What's New
✅ **Live preview now shows images** for both questions and options
✅ **Instant preview** when selecting image files (using FileReader API)
✅ **Existing images** displayed in edit mode
✅ **Responsive sizing** - images won't break the UI

---

## How Image Preview Works

### Question Images
- **Display:** Full-width block below question text
- **Max Size:** 100% width, 400px height
- **Location:** Between question text and options section

### Option Images
- **Display:** Inline after option text
- **Max Size:** 300px width, 200px height
- **Location:** Below each option label

---

## Testing Steps

### Test 1: Create New Question with Images

1. Go to: http://localhost:8080/admin/questions/create
2. Fill in question text and all 4 options
3. Set **Question Type** to "With Image"
4. Select a question image file
5. **Expected:** Image appears instantly in live preview (right panel)
6. Select option images for any options
7. **Expected:** Option images appear instantly below respective options
8. Click "Create Question"
9. **Verify:** Question saved with all images

### Test 2: Edit Existing Question - View Saved Images

1. Go to: http://localhost:8080/admin/questions
2. Click "Edit" on any question with images
3. **Expected:**
   - Existing question image displays in edit form
   - Existing question image displays in live preview
   - All existing option images display in preview

### Test 3: Replace Images in Edit Mode

1. Open edit page for question with images
2. Select a new question image
3. **Expected:** Preview updates instantly with new image
4. Change an option image
5. **Expected:** Preview updates instantly
6. Click "Update Question"
7. **Verify:** New images saved, old images deleted

### Test 4: UI Responsiveness

1. Create/edit question with very large image (2MB)
2. **Expected:**
   - Image scales down to fit container
   - No horizontal scrolling
   - No layout breakage
   - Preview panel stays within bounds

### Test 5: Mixed Content (Text + Images)

1. Create question with:
   - Question image
   - Option 1: Text only
   - Option 2: Text + Image
   - Option 3: Text only
   - Option 4: Text + Image
2. **Expected:**
   - All content displays correctly
   - Images align properly
   - Layout stays clean

---

## Technical Details

### Image Display Specifications

**Question Images:**
```css
max-width: 100%;
max-height: 400px;
display: block;
```

**Option Images:**
```css
max-width: 300px;
max-height: 200px;
display: block;
```

### Preview Data Flow

1. **Edit Mode:**
   - Existing images: Database path → ImageController → Display
   - New files: File input → FileReader API → Base64 → Display

2. **Create Mode:**
   - New files: File input → FileReader API → Base64 → Display

3. **AJAX Request:**
   ```javascript
   {
     question_text: "...",
     question_image_path: "uploads/questions/..." or base64,
     option_1_text: "...",
     option_1_image_path: "uploads/options/..." or base64,
     // ... etc
   }
   ```

---

## Expected Behavior Summary

| Scenario | Expected Behavior |
|----------|------------------|
| Select question image | Preview updates instantly |
| Select option image | Preview updates instantly |
| Edit with existing images | Images show immediately on page load |
| Upload large image | Image scales to fit, no UI break |
| Remove image (clear file input) | Image removed from preview |
| Save question | All images uploaded to server |

---

## Troubleshooting

**Images not showing in preview?**
- Check browser console for JavaScript errors
- Verify FileReader API is supported (all modern browsers)
- Check that image path is correct (use browser dev tools)

**Images not saving?**
- Verify writable/uploads/ directories exist and are writable
- Check error logs: writable/logs/log-2026-01-07.log
- Verify ImageController route is working: /uploads/questions/test.jpg

**UI breaking with images?**
- Check CSS is loading properly
- Verify Bootstrap 5 is included
- Test with different image sizes

---

## Files Modified in v1.3.6

1. **app/Views/admin/questions/preview.php**
   - Added image display sections with responsive sizing

2. **app/Views/admin/questions/create.php**
   - Added FileReader handlers for instant preview

3. **app/Views/admin/questions/edit.php**
   - Added FileReader handlers
   - Initialized with existing image paths

4. **app/Controllers/Admin/QuestionController.php**
   - Updated preview() method to accept image data

---

**Last Updated:** 2026-01-07
**Version:** 1.3.6
