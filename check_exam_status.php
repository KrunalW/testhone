<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== RECENT EXAMS STATUS ===\n\n";

$stmt = $pdo->query("SELECT id, title, status, is_scheduled, scheduled_start_time,
                     scheduled_end_time, created_at
                     FROM exams
                     ORDER BY created_at DESC
                     LIMIT 5");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}\n";
    echo "  Title: {$row['title']}\n";
    echo "  Status: {$row['status']}\n";
    echo "  Is Scheduled: {$row['is_scheduled']}\n";
    echo "  Start Time: {$row['scheduled_start_time']}\n";
    echo "  End Time: {$row['scheduled_end_time']}\n";
    echo "  Created: {$row['created_at']}\n";

    // Check if it would be returned by getActiveExams()
    $isActive = in_array($row['status'], ['active', 'scheduled']);
    echo "  Would show in dashboard: " . ($isActive ? "YES" : "NO") . "\n\n";
}

echo "=== EXPECTED STATUSES FOR DASHBOARD ===\n";
echo "- 'active': Exam is active and available\n";
echo "- 'scheduled': Exam is scheduled (shows with countdown)\n";
echo "\nOther statuses (won't show):\n";
echo "- 'draft': Exam not ready\n";
echo "- 'inactive': Exam disabled\n";
echo "- 'completed': Exam finished\n";
