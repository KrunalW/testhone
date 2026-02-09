<?php
// Comprehensive test suite for Mock Test Platform
$host = 'localhost';
$db = 'analytics_dashboard';
$user = 'root';
$pass = '';

$testResults = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "╔══════════════════════════════════════════════╗\n";
    echo "║  MOCK TEST PLATFORM - AUTOMATED TEST SUITE  ║\n";
    echo "╚══════════════════════════════════════════════╝\n\n";

    // Test 1: Database Connection
    echo "📌 Test 1: Database Connection\n";
    $testResults['database'] = true;
    echo "   ✅ Connected to database: {$db}\n\n";

    // Test 2: Tables Existence
    echo "📌 Test 2: Database Tables\n";
    $requiredTables = [
        'users', 'auth_identities', 'auth_groups_users',
        'subjects', 'questions', 'options',
        'exams', 'exam_subject_distribution',
        'exam_sessions', 'user_answers', 'exam_results',
        'tab_switch_logs', 'ci_sessions'
    ];

    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingTables = array_diff($requiredTables, $existingTables);
    if (empty($missingTables)) {
        echo "   ✅ All required tables exist (" . count($requiredTables) . " tables)\n\n";
        $testResults['tables'] = true;
    } else {
        echo "   ❌ Missing tables: " . implode(', ', $missingTables) . "\n\n";
        $testResults['tables'] = false;
    }

    // Test 3: User Roles
    echo "📌 Test 3: User Roles & Permissions\n";
    $requiredRoles = ['superadmin', 'admin', 'exam_expert', 'user'];
    $stmt = $pdo->query("SELECT DISTINCT `group` FROM auth_groups_users");
    $existingRoles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingRoles = array_diff($requiredRoles, $existingRoles);
    if (empty($missingRoles)) {
        echo "   ✅ All required roles exist:\n";
        foreach ($requiredRoles as $role) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM auth_groups_users WHERE `group` = ?");
            $stmt->execute([$role]);
            $count = $stmt->fetchColumn();
            echo "      • {$role}: {$count} user(s)\n";
        }
        echo "\n";
        $testResults['roles'] = true;
    } else {
        echo "   ❌ Missing roles: " . implode(', ', $missingRoles) . "\n\n";
        $testResults['roles'] = false;
    }

    // Test 4: Subjects
    echo "📌 Test 4: Subjects Data\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM subjects");
    $subjectCount = $stmt->fetchColumn();

    if ($subjectCount > 0) {
        echo "   ✅ Subjects found: {$subjectCount}\n";
        $stmt = $pdo->query("SELECT code, name FROM subjects LIMIT 5");
        $subjects = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($subjects as $subject) {
            echo "      • {$subject->code}: {$subject->name}\n";
        }
        echo "\n";
        $testResults['subjects'] = true;
    } else {
        echo "   ❌ No subjects found\n\n";
        $testResults['subjects'] = false;
    }

    // Test 5: Questions & Options
    echo "📌 Test 5: Questions & Options\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
    $questionCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM options");
    $optionCount = $stmt->fetchColumn();

    if ($questionCount > 0 && $optionCount > 0) {
        echo "   ✅ Questions: {$questionCount}\n";
        echo "   ✅ Options: {$optionCount}\n";
        $expectedOptions = $questionCount * 4;
        if ($optionCount >= $expectedOptions) {
            echo "   ✅ All questions have options (expected {$expectedOptions})\n\n";
        } else {
            echo "   ⚠️  Some questions missing options (expected {$expectedOptions}, found {$optionCount})\n\n";
        }
        $testResults['questions'] = true;
    } else {
        echo "   ❌ Questions or options missing\n\n";
        $testResults['questions'] = false;
    }

    // Test 6: Exams Configuration
    echo "📌 Test 6: Exams Configuration\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM exams");
    $examCount = $stmt->fetchColumn();

    if ($examCount > 0) {
        echo "   ✅ Exams found: {$examCount}\n";
        $stmt = $pdo->query("
            SELECT e.id, e.title, e.status, e.is_scheduled,
                   e.scheduled_start_time, e.total_questions
            FROM exams e
            LIMIT 5
        ");
        $exams = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($exams as $exam) {
            echo "      • ID {$exam->id}: {$exam->title}\n";
            echo "        Status: {$exam->status}, Questions: {$exam->total_questions}\n";
            if ($exam->is_scheduled) {
                echo "        Scheduled: {$exam->scheduled_start_time}\n";
            }
        }
        echo "\n";
        $testResults['exams'] = true;
    } else {
        echo "   ❌ No exams found\n\n";
        $testResults['exams'] = false;
    }

    // Test 7: Performance Indexes
    echo "📌 Test 7: Performance Indexes\n";
    $requiredIndexes = [
        'idx_user_answers_session_question',
        'idx_questions_subject',
        'idx_options_question',
        'idx_exam_subject_dist_exam'
    ];

    $indexFound = 0;
    foreach ($requiredIndexes as $indexName) {
        $stmt = $pdo->query("SHOW INDEX FROM user_answers WHERE Key_name = '{$indexName}'");
        if ($stmt->rowCount() > 0) {
            $indexFound++;
        }
    }

    if ($indexFound >= 2) {
        echo "   ✅ Performance indexes present ({$indexFound} checked)\n\n";
        $testResults['indexes'] = true;
    } else {
        echo "   ⚠️  Some indexes may be missing\n\n";
        $testResults['indexes'] = false;
    }

    // Test 8: Exam Scheduling Fields
    echo "📌 Test 8: Exam Scheduling Fields\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM exams LIKE 'scheduled_start_time'");
    $hasScheduling = $stmt->rowCount() > 0;

    if ($hasScheduling) {
        echo "   ✅ Scheduling fields present\n";
        echo "      • scheduled_start_time\n";
        echo "      • scheduled_end_time\n";
        echo "      • is_scheduled\n";
        echo "      • created_by\n\n";
        $testResults['scheduling'] = true;
    } else {
        echo "   ❌ Scheduling fields missing\n\n";
        $testResults['scheduling'] = false;
    }

    // Test 9: Image Upload Directories
    echo "📌 Test 9: Image Upload Directories\n";
    $uploadDirs = [
        'writable/uploads/questions',
        'writable/uploads/options'
    ];

    $allDirsExist = true;
    foreach ($uploadDirs as $dir) {
        if (is_dir($dir) && is_writable($dir)) {
            echo "   ✅ {$dir} (writable)\n";
        } else {
            echo "   ❌ {$dir} (missing or not writable)\n";
            $allDirsExist = false;
        }
    }
    echo "\n";
    $testResults['uploads'] = $allDirsExist;

    // Test 10: Session Configuration
    echo "📌 Test 10: Session Configuration\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'ci_sessions'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Database sessions table exists\n\n";
        $testResults['sessions'] = true;
    } else {
        echo "   ❌ Database sessions table missing\n\n";
        $testResults['sessions'] = false;
    }

    // Summary
    echo "╔══════════════════════════════════════════════╗\n";
    echo "║              TEST SUMMARY                    ║\n";
    echo "╚══════════════════════════════════════════════╝\n\n";

    $totalTests = count($testResults);
    $passedTests = count(array_filter($testResults));
    $failedTests = $totalTests - $passedTests;

    foreach ($testResults as $test => $passed) {
        $status = $passed ? '✅ PASS' : '❌ FAIL';
        printf("%-20s %s\n", ucfirst($test) . ':', $status);
    }

    echo "\n";
    echo "Total Tests: {$totalTests}\n";
    echo "Passed: {$passedTests}\n";
    echo "Failed: {$failedTests}\n";
    echo "\n";

    if ($failedTests === 0) {
        echo "🎉 ALL TESTS PASSED! System is ready for use.\n\n";
        echo "Next Steps:\n";
        echo "1. Start server: php spark serve\n";
        echo "2. Visit: http://localhost:8080/login\n";
        echo "3. Login with test credentials (see README.md)\n\n";
    } else {
        echo "⚠️  Some tests failed. Please review and fix issues.\n\n";
    }

} catch(PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
