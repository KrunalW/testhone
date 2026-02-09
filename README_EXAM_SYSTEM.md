# Mock Test Exam Platform

A complete online examination system built with CodeIgniter 4 and Shield authentication, designed for government-style MCQ exams.

## Features

### Exam Management
- **50 MCQ Questions** with 4 options each
- **Subject-wise Distribution** - Configurable questions from different subjects (Maths, Logic, English, GK, General Science)
- **Randomization** - Questions and options can be randomized to prevent cheating
- **Negative Marking** - Configurable penalty for wrong answers
- **Customizable Duration** - Set exam time limits per exam
- **Auto-Save** - Answers saved automatically on selection
- **Auto-Submit** - Exam submitted automatically when time expires

### Security Features
- **Tab Switch Prevention** - Tracks and limits tab switching during exam
- **Configurable Limits** - Set maximum allowed tab switches (e.g., 3 switches before auto-termination)
- **Session-based Isolation** - Each user gets independent exam session
- **Server-side Timer** - Prevents time manipulation

### User Experience
- **Single Page Exam** - All 50 questions on one scrollable page
- **Question Palette** - Easy navigation with color-coded status
  - Green: Answered
  - Red: Visited but not answered
  - Gray: Not visited
- **Subject Grouping** - Questions organized by subject in palette
- **Progress Tracking** - Real-time count of answered/unanswered questions
- **Review Mode** - Change answers before final submission

### Results & Analytics
- **Instant Results** - Score calculated immediately after submission
- **Subject-wise Analysis** - Performance breakdown by subject
- **Visual Charts** - Bar charts showing performance
- **Detailed Metrics** - Accuracy rate, attempt rate, pass/fail status
- **Print Support** - Print-friendly result page
- **Previous Attempts** - View history of past exams

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL/MariaDB database
- Composer

### Step 1: Database Configuration
Edit `.env` file:
```env
database.default.hostname = localhost
database.default.database = analytics_dashboard
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### Step 2: Install Dependencies
Already installed via Composer. If needed:
```bash
composer install
```

### Step 3: Run Migrations
Create all database tables:
```bash
php spark migrate
```

This creates 9 tables:
- `subjects` - Subject master data
- `exams` - Exam configurations
- `exam_subject_distribution` - Weightage per exam
- `questions` - Question bank
- `options` - 4 options per question
- `exam_sessions` - User exam attempts
- `user_answers` - Answer storage
- `exam_results` - Subject-wise results
- `tab_switch_logs` - Security violation tracking

### Step 4: Seed Sample Data
Load sample exam with 50 questions:
```bash
php spark db:seed ExamSystemSeeder
```

This creates:
- 5 subjects (Maths, Logic, English, GK, General Science)
- 1 sample exam: "SSC CGL Tier-1 Mock Test 2024"
- 50 questions (10 from each subject)
- 4 options for each question

### Step 5: Start Server
```bash
php spark serve
```

Access at: **http://localhost:8080**

## Usage

### For Students

1. **Login** - Use Shield authentication to login
2. **Dashboard** - View available exams
3. **Read Instructions** - Review exam rules and marking scheme
4. **Accept Terms** - Check the agreement checkbox
5. **Start Exam** - Click "Start Exam Now"
6. **Take Exam**:
   - Answer questions by selecting options
   - Answers auto-save immediately
   - Use question palette to navigate
   - Watch the timer
   - Avoid switching tabs!
7. **Submit** - Click "Submit Exam" when done
8. **View Results** - See score, subject-wise analysis, and performance charts

### For Administrators

#### Creating a New Exam

**1. Add Subjects (if new)**
```sql
INSERT INTO subjects (name, code, description)
VALUES ('Physics', 'PHY', 'Physics questions');
```

**2. Create Exam**
```sql
INSERT INTO exams (
    title,
    description,
    duration_minutes,
    total_questions,
    has_negative_marking,
    negative_marks_per_question,
    marks_per_question,
    prevent_tab_switch,
    max_tab_switches_allowed,
    status
) VALUES (
    'UPSC Prelims Mock Test',
    'UPSC Civil Services Preliminary Examination Practice',
    120,  -- 2 hours
    100,  -- 100 questions
    1,    -- Has negative marking
    0.33, -- -0.33 per wrong answer
    2.00, -- +2 per correct answer
    1,    -- Prevent tab switching
    5,    -- Allow 5 tab switches
    'active'
);
```

**3. Set Subject Distribution**
```sql
INSERT INTO exam_subject_distribution (exam_id, subject_id, number_of_questions)
VALUES
    (1, 1, 25),  -- 25 Maths questions
    (1, 2, 25),  -- 25 Logic questions
    (1, 3, 25),  -- 25 English questions
    (1, 4, 25);  -- 25 GK questions
```

**4. Add Questions**
```sql
INSERT INTO questions (subject_id, question_text, explanation)
VALUES (1, 'What is 2 + 2?', 'Basic addition: 2 + 2 = 4');
```

**5. Add Options**
```sql
INSERT INTO options (question_id, option_text, is_correct, display_order)
VALUES
    (1, '3', 0, 1),
    (1, '4', 1, 2),  -- Correct answer
    (1, '5', 0, 3),
    (1, '6', 0, 4);
```

## Architecture

### Database Schema

```
subjects
├── id
├── name (Mathematics, Logic, etc.)
├── code (MATH, LOGIC, etc.)
└── description

exams
├── id
├── title
├── duration_minutes
├── total_questions
├── has_negative_marking
├── negative_marks_per_question
├── marks_per_question
├── randomize_questions
├── randomize_options
├── prevent_tab_switch
└── max_tab_switches_allowed

exam_subject_distribution
├── exam_id → exams.id
├── subject_id → subjects.id
└── number_of_questions

questions
├── id
├── subject_id → subjects.id
├── question_text
├── question_image
├── explanation
└── difficulty_level

options
├── id
├── question_id → questions.id
├── option_text
├── is_correct
└── display_order

exam_sessions
├── id
├── user_id → users.id
├── exam_id → exams.id
├── start_time
├── end_time
├── status (in_progress, completed, terminated)
├── tab_switch_count
├── final_score
└── percentage

user_answers
├── session_id → exam_sessions.id
├── question_id → questions.id
├── selected_option_id → options.id
└── is_correct

exam_results (subject-wise)
├── session_id
├── subject_id
├── correct_answers
├── wrong_answers
└── score_obtained
```

### Controllers

**ExamController.php**
- `instructions()` - Display exam instructions
- `start()` - Create exam session
- `take()` - Display exam interface
- `saveAnswer()` - AJAX: Auto-save answers
- `clearAnswer()` - AJAX: Clear answer
- `logTabSwitch()` - AJAX: Track tab switches
- `submit()` - Submit and calculate results
- `result()` - Display result page

**Dashboard.php**
- `index()` - Show available exams and previous attempts

### Models

- **ExamModel** - Exam configuration and subject distribution
- **QuestionModel** - Questions with randomization support
- **ExamSessionModel** - User exam sessions
- **UserAnswerModel** - Answer storage with auto-save
- **TabSwitchLogModel** - Security violation tracking

### Views

**Dashboard** (`dashboard/index.php`)
- Available exams list
- Previous attempts history
- Start exam button

**Instructions** (`exam/instructions.php`)
- Exam details
- Subject distribution
- Marking scheme
- Important rules
- Agreement checkbox

**Exam Interface** (`exam/take.php`)
- Timer display
- Question palette (left sidebar)
- All 50 questions (single page)
- Auto-save functionality
- Tab switch detection
- Submit confirmation

**Result Page** (`exam/result.php`)
- Score card
- Subject-wise analysis
- Performance charts (Chart.js)
- Accuracy metrics
- Print support

## Configuration

### Exam Settings

Edit exam record in database:

```sql
UPDATE exams SET
    duration_minutes = 90,           -- Exam duration
    has_negative_marking = 1,        -- Enable/disable negative marking
    negative_marks_per_question = 0.25,  -- Penalty per wrong answer
    marks_per_question = 1,          -- Marks per correct answer
    randomize_questions = 1,         -- Randomize question order
    randomize_options = 1,           -- Randomize option order
    prevent_tab_switch = 1,          -- Enable tab switch prevention
    max_tab_switches_allowed = 3     -- Maximum allowed switches
WHERE id = 1;
```

### Security Settings

**Strict Mode** (Zero tolerance):
```sql
UPDATE exams SET prevent_tab_switch = 1, max_tab_switches_allowed = 0;
```

**Warning Mode** (3 warnings):
```sql
UPDATE exams SET prevent_tab_switch = 1, max_tab_switches_allowed = 3;
```

**Logging Only** (No termination):
```sql
UPDATE exams SET prevent_tab_switch = 0;
```

## API Endpoints

All routes protected by `session` filter (requires login):

- `GET /exam/instructions/{id}` - View exam instructions
- `POST /exam/start/{id}` - Start exam session
- `GET /exam/take/{sessionId}` - Exam interface
- `POST /exam/save-answer` - Save answer (AJAX)
- `POST /exam/clear-answer` - Clear answer (AJAX)
- `POST /exam/log-tab-switch` - Log violation (AJAX)
- `POST /exam/submit` - Submit exam
- `GET /exam/result/{sessionId}` - View results

## Performance & Scalability

### Concurrent Users
- Session-based isolation - Each user independent
- Database connection pooling
- Minimal server requests (single page load)
- AJAX for auto-save (non-blocking)

### Optimization
- Indexed foreign keys
- Unique constraints prevent duplicates
- Single query for question loading
- Cached subject data
- Optimized JavaScript (vanilla JS + jQuery)

## Security Features

1. **CSRF Protection** - All forms protected
2. **SQL Injection Prevention** - Prepared statements
3. **XSS Protection** - Input escaping
4. **Session Hijacking Prevention** - Shield auth
5. **Tab Switch Detection** - Multi-layer approach
6. **Server-side Validation** - Timer validation
7. **Unique Answer Constraint** - Prevents duplicate submissions

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Dependencies

- **CodeIgniter 4.6.4** - PHP framework
- **Shield 1.2.0** - Authentication
- **Bootstrap 5.3** - UI framework
- **jQuery 3.7** - AJAX operations
- **Chart.js 4.4** - Result charts
- **Bootstrap Icons 1.11** - Icons

## Troubleshooting

### Exam not appearing on dashboard
- Check `exams.status = 'active'`
- Verify subject distribution configured
- Ensure questions exist for all subjects

### Timer showing incorrect time
- Check server timezone
- Verify `start_time` and `end_time` in session
- Clear browser cache

### Auto-save not working
- Check browser console for errors
- Verify CSRF token
- Check database connection
- Ensure session is `in_progress`

### Tab switch not detected
- Works only on `visibilitychange` event
- Check `exam.prevent_tab_switch = 1`
- Test in regular browser (not incognito on some browsers)

## Future Enhancements

- [ ] Question bookmarking/flagging
- [ ] Export results to PDF
- [ ] Admin dashboard for exam management
- [ ] Question import from Excel/CSV
- [ ] Image upload for questions
- [ ] Video proctoring integration
- [ ] Mobile app version
- [ ] Multi-language support

## Credits

Built with CodeIgniter 4 Framework and Shield Authentication.

## License

This project is for educational purposes.

---

**Server Status:** Running on http://localhost:8080

**First Time Setup:**
1. Access http://localhost:8080/register to create an account
2. Login at http://localhost:8080/login
3. Start taking exams!

Enjoy your mock test platform! 🎓
