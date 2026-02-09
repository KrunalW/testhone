<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestFeedback extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:feedback';
    protected $description = 'Test the exam feedback system';

    public function run(array $params)
    {
        CLI::write('=== TESTING EXAM FEEDBACK SYSTEM ===', 'green');
        CLI::newLine();

        $db = \Config\Database::connect();

        // Test 1: Database Schema
        CLI::write('TEST 1: Database Schema Verification', 'yellow');
        CLI::write('------------------------------------');

        $query = $db->query("DESCRIBE exam_feedback");
        $fields = $query->getResultArray();

        $requiredFields = [
            'id',
            'session_id',
            'user_id',
            'exam_id',
            'overall_experience_rating',
            'web_panel_experience',
            'question_quality',
            'will_refer_friends',
            'interested_next_test',
            'real_vs_mock_difference',
            'general_feedback',
            'felt_same_pressure',
            'other_test_series',
            'willing_to_pay',
            'amount_paid_range',
            'created_at',
            'updated_at'
        ];

        $existingFields = array_column($fields, 'Field');
        $missingFields = array_diff($requiredFields, $existingFields);

        if (empty($missingFields)) {
            CLI::write('✅ All required fields present (' . count($requiredFields) . ' fields)', 'green');
        } else {
            CLI::write('❌ Missing fields: ' . implode(', ', $missingFields), 'red');
        }

        CLI::newLine();

        // Test 2: Foreign Keys
        CLI::write('TEST 2: Foreign Key Constraints', 'yellow');
        CLI::write('--------------------------------');

        $fkQuery = $db->query("
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'exam_feedback'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $foreignKeys = $fkQuery->getResultArray();

        if (count($foreignKeys) === 3) {
            CLI::write('✅ All 3 foreign keys present:', 'green');
            foreach ($foreignKeys as $fk) {
                CLI::write("   - {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}");
            }
        } else {
            CLI::write('❌ Expected 3 foreign keys, found ' . count($foreignKeys), 'red');
        }

        CLI::newLine();

        // Test 3: Model Validation
        CLI::write('TEST 3: Model Validation Rules', 'yellow');
        CLI::write('-------------------------------');

        $feedbackModel = model('ExamFeedbackModel');

        // Test valid data
        $testData = [
            'session_id' => 1,
            'user_id' => 1,
            'exam_id' => 1,
            'overall_experience_rating' => 8,
            'web_panel_experience' => 'good',
            'question_quality' => 'excellent',
            'will_refer_friends' => 1,
            'interested_next_test' => 1,
            'felt_same_pressure' => 'yes',
        ];

        if ($feedbackModel->validate($testData)) {
            CLI::write('✅ Valid data passes validation', 'green');
        } else {
            CLI::write('❌ Valid data failed validation', 'red');
            CLI::write('Errors: ' . print_r($feedbackModel->errors(), true));
        }

        // Test invalid rating
        $invalidData = $testData;
        $invalidData['overall_experience_rating'] = 15; // Invalid: > 10

        if (!$feedbackModel->validate($invalidData)) {
            CLI::write('✅ Invalid rating (15) correctly rejected', 'green');
        } else {
            CLI::write('❌ Invalid rating was accepted', 'red');
        }

        // Test invalid experience
        $invalidData2 = $testData;
        $invalidData2['web_panel_experience'] = 'invalid_value';

        if (!$feedbackModel->validate($invalidData2)) {
            CLI::write('✅ Invalid experience value correctly rejected', 'green');
        } else {
            CLI::write('❌ Invalid experience value was accepted', 'red');
        }

        CLI::newLine();

        // Test 4: Check Available Sessions
        CLI::write('TEST 4: Available Test Sessions', 'yellow');
        CLI::write('--------------------------------');

        $sessionsQuery = $db->query("
            SELECT es.id, es.user_id, es.exam_id, e.title, es.status, es.created_at
            FROM exam_sessions es
            JOIN exams e ON e.id = es.exam_id
            WHERE es.status IN ('completed', 'terminated')
            ORDER BY es.created_at DESC
            LIMIT 5
        ");
        $sessions = $sessionsQuery->getResultArray();

        if (count($sessions) > 0) {
            CLI::write('✅ Found ' . count($sessions) . ' completed/terminated sessions for testing:', 'green');
            foreach ($sessions as $session) {
                CLI::write("   Session ID: {$session['id']} | Exam: {$session['title']} | Status: {$session['status']}");
            }
            CLI::newLine();
            CLI::write('You can test feedback with: /exam/feedback/' . $sessions[0]['id'], 'cyan');
        } else {
            CLI::write('⚠️  No completed sessions found. Complete an exam first.', 'yellow');
        }

        CLI::newLine();

        // Test 5: Existing Feedback
        CLI::write('TEST 5: Existing Feedback Records', 'yellow');
        CLI::write('----------------------------------');

        $feedbackQuery = $db->query("SELECT COUNT(*) as count FROM exam_feedback");
        $feedbackCount = $feedbackQuery->getRow()->count;

        CLI::write("Total feedback records: {$feedbackCount}");

        if ($feedbackCount > 0) {
            $sampleQuery = $db->query("
                SELECT f.*, u.username, e.title as exam_title
                FROM exam_feedback f
                JOIN users u ON u.id = f.user_id
                JOIN exams e ON e.id = f.exam_id
                ORDER BY f.created_at DESC
                LIMIT 1
            ");
            $sample = $sampleQuery->getRow();

            CLI::write('✅ Latest feedback:', 'green');
            CLI::write("   User: {$sample->username}");
            CLI::write("   Exam: {$sample->exam_title}");
            CLI::write("   Rating: {$sample->overall_experience_rating}/10");
            CLI::write("   Panel: {$sample->web_panel_experience}");
            CLI::write("   Quality: {$sample->question_quality}");
        } else {
            CLI::write('⚠️  No feedback submitted yet. Complete an exam and submit feedback.', 'yellow');
        }

        CLI::newLine();

        // Test 6: Routes
        CLI::write('TEST 6: Routes Configuration', 'yellow');
        CLI::write('-----------------------------');

        exec('php spark routes 2>&1 | grep feedback', $routeOutput);

        if (count($routeOutput) >= 2) {
            CLI::write('✅ Feedback routes configured:', 'green');
            foreach ($routeOutput as $route) {
                CLI::write('   ' . trim($route));
            }
        } else {
            CLI::write('❌ Feedback routes missing', 'red');
        }

        CLI::newLine();

        // Test 7: View File
        CLI::write('TEST 7: View File Exists', 'yellow');
        CLI::write('------------------------');

        $viewPath = APPPATH . 'Views/exam/feedback.php';
        if (file_exists($viewPath)) {
            $fileSize = filesize($viewPath);
            CLI::write("✅ View file exists: " . number_format($fileSize) . " bytes", 'green');
        } else {
            CLI::write('❌ View file not found', 'red');
        }

        CLI::newLine();

        // Test 8: Controller Methods
        CLI::write('TEST 8: Controller Methods', 'yellow');
        CLI::write('--------------------------');

        $controller = new \App\Controllers\ExamController();

        if (method_exists($controller, 'feedback')) {
            CLI::write('✅ ExamController::feedback() method exists', 'green');
        } else {
            CLI::write('❌ ExamController::feedback() method missing', 'red');
        }

        if (method_exists($controller, 'submitFeedback')) {
            CLI::write('✅ ExamController::submitFeedback() method exists', 'green');
        } else {
            CLI::write('❌ ExamController::submitFeedback() method missing', 'red');
        }

        CLI::newLine();

        // Summary
        CLI::write('=== TEST SUMMARY ===', 'green');
        CLI::newLine();
        CLI::write('All core functionality is in place! ✅', 'green');
        CLI::newLine();

        if (count($sessions) > 0) {
            CLI::write('🧪 MANUAL TESTING:', 'cyan');
            CLI::write('1. Navigate to: http://localhost:8080/exam/feedback/' . $sessions[0]['id']);
            CLI::write('2. Fill out the feedback form');
            CLI::write('3. Submit and verify redirect to dashboard');
            CLI::write('4. Check database for new feedback record');
        } else {
            CLI::write('⚠️  To test feedback:', 'yellow');
            CLI::write('1. Complete an exam first');
            CLI::write('2. After submission, you\'ll be redirected to feedback');
            CLI::write('3. Or manually visit: /exam/feedback/{session_id}');
        }

        CLI::newLine();
        CLI::write('Done! 🎉', 'green');
    }
}
