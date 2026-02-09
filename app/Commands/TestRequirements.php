<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestRequirements extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:requirements';
    protected $description = 'Test the 4 exam requirements implementation';

    public function run(array $params)
    {
        CLI::write('=== TESTING 4 EXAM REQUIREMENTS ===', 'green');
        CLI::newLine();

        $db = \Config\Database::connect();

        // Test 1: Database Schema
        CLI::write('TEST 1: Database Schema', 'yellow');
        CLI::write('------------------------');

        $query = $db->query("DESCRIBE exams");
        $fields = $query->getResultArray();
        $hasResultPublishTime = false;
        $hasIsResultScheduled = false;

        foreach ($fields as $field) {
            if ($field['Field'] === 'result_publish_time') {
                $hasResultPublishTime = true;
                CLI::write('✅ result_publish_time field exists: ' . $field['Type'], 'green');
            }
            if ($field['Field'] === 'is_result_scheduled') {
                $hasIsResultScheduled = true;
                CLI::write('✅ is_result_scheduled field exists: ' . $field['Type'], 'green');
            }
        }

        if (!$hasResultPublishTime || !$hasIsResultScheduled) {
            CLI::write('❌ Migration fields missing!', 'red');
            return;
        }

        CLI::newLine();

        // Test 2: Current Exam Data
        CLI::write('TEST 2: Current Exam Data', 'yellow');
        CLI::write('-------------------------');

        $query = $db->query("SELECT id, title, duration_minutes, is_scheduled,
                            scheduled_start_time, scheduled_end_time,
                            is_result_scheduled, result_publish_time, status
                            FROM exams LIMIT 3");
        $exams = $query->getResultArray();

        foreach ($exams as $exam) {
            CLI::write("Exam ID {$exam['id']}: {$exam['title']}", 'cyan');
            CLI::write("  Duration: {$exam['duration_minutes']} minutes");
            CLI::write("  Status: {$exam['status']}");
            CLI::write("  Is Scheduled: " . ($exam['is_scheduled'] ? 'Yes' : 'No'));
            if ($exam['is_scheduled']) {
                CLI::write("  Start: {$exam['scheduled_start_time']}");
                CLI::write("  End: {$exam['scheduled_end_time']}");
            }
            CLI::write("  Result Scheduled: " . ($exam['is_result_scheduled'] ? 'Yes' : 'No'));
            if ($exam['is_result_scheduled']) {
                CLI::write("  Result Publish: {$exam['result_publish_time']}");
            }
            CLI::newLine();
        }

        // Test 3: Exam Sessions
        CLI::write('TEST 3: Exam Sessions (One Attempt Check)', 'yellow');
        CLI::write('------------------------------------------');

        $query = $db->query("SELECT user_id, exam_id, status, created_at
                            FROM exam_sessions
                            WHERE status IN ('completed', 'terminated')
                            ORDER BY created_at DESC LIMIT 5");
        $sessions = $query->getResultArray();

        if (count($sessions) > 0) {
            CLI::write('Recent completed/terminated sessions:');
            foreach ($sessions as $session) {
                CLI::write("  User {$session['user_id']} - Exam {$session['exam_id']} - {$session['status']} - {$session['created_at']}");
            }
        } else {
            CLI::write('  No completed sessions yet', 'light_gray');
        }

        CLI::newLine();

        // Test 4: Setup Result Scheduling
        CLI::write('TEST 4: Setting Up Result Scheduling Test', 'yellow');
        CLI::write('------------------------------------------');

        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $publishTime = clone $now;
        $publishTime->add(new \DateInterval('PT2M')); // 2 minutes from now

        $db->query("UPDATE exams SET is_result_scheduled = 1,
                    result_publish_time = ? WHERE id = 1",
                   [$publishTime->format('Y-m-d H:i:s')]);

        CLI::write('✅ Exam ID 1 configured for result scheduling:', 'green');
        CLI::write('   Result publishes at: ' . $publishTime->format('Y-m-d H:i:s') . ' IST', 'cyan');
        CLI::write('   (2 minutes from now)');
        CLI::newLine();

        // Test 5: Setup Late Join
        CLI::write('TEST 5: Setting Up Late Join Test', 'yellow');
        CLI::write('----------------------------------');

        $startTime = clone $now;
        $startTime->sub(new \DateInterval('PT5M')); // 5 minutes ago

        $endTime = clone $now;
        $endTime->add(new \DateInterval('PT25M')); // 25 minutes from now

        $db->query("UPDATE exams SET is_scheduled = 1,
                    scheduled_start_time = ?,
                    scheduled_end_time = ?,
                    duration_minutes = 30,
                    status = 'active'
                    WHERE id = 2", [
                    $startTime->format('Y-m-d H:i:s'),
                    $endTime->format('Y-m-d H:i:s')
                ]);

        CLI::write('✅ Exam ID 2 configured for late join test:', 'green');
        CLI::write('   Started: ' . $startTime->format('Y-m-d H:i:s') . ' IST (5 min ago)', 'cyan');
        CLI::write('   Ends: ' . $endTime->format('Y-m-d H:i:s') . ' IST (25 min from now)', 'cyan');
        CLI::write('   Duration: 30 minutes');
        CLI::write('   Expected: New users get only 25 minutes', 'light_gray');
        CLI::newLine();

        // Summary
        CLI::write('=== SETUP COMPLETE ===', 'green');
        CLI::newLine();
        CLI::write('Manual Testing Instructions:', 'yellow');
        CLI::write('----------------------------');
        CLI::newLine();

        CLI::write('TEST REQUIREMENT 1 & 2: Dashboard Redirect + Result Countdown', 'cyan');
        CLI::write('1. Login as any student');
        CLI::write('2. Complete Exam ID 1');
        CLI::write('   ✅ Should redirect to dashboard (not result page)');
        CLI::write('   ✅ Success message should appear');
        CLI::write('3. Check "Previous Attempts" section');
        CLI::write('   ✅ Should show countdown timer (2 minutes)');
        CLI::write('   ✅ Should NOT show "View Report" button');
        CLI::write('4. Wait 2 minutes, refresh dashboard');
        CLI::write('   ✅ Countdown expires, "View Report" button appears');
        CLI::newLine();

        CLI::write('TEST REQUIREMENT 3: One Attempt Per Exam', 'cyan');
        CLI::write('1. Try to start Exam ID 1 again (same student)');
        CLI::write('   ✅ Should redirect to dashboard with error');
        CLI::write('   ✅ Error: "You have already attempted this exam..."');
        CLI::newLine();

        CLI::write('TEST REQUIREMENT 4: Late Join Time Adjustment', 'cyan');
        CLI::write('1. Login as DIFFERENT student (who hasn\'t taken Exam 2)');
        CLI::write('2. Start Exam ID 2');
        CLI::write('   ✅ Should start successfully');
        CLI::write('3. Check exam timer (top-right corner)');
        CLI::write('   ✅ Should show 25:00 minutes (not 30:00)');
        CLI::write('   ✅ Time was reduced by 5 minutes (late join penalty)');
        CLI::newLine();

        CLI::write('All tests configured successfully! 🎉', 'green');
    }
}
