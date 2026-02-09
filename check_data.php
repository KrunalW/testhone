<?php
// Simple database check script
$host = 'localhost';
$db = 'analytics_dashboard';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CHECKING EXISTING DATA ===\n\n";

    // Check subjects
    $stmt = $pdo->query("SELECT * FROM subjects");
    $subjects = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo "SUBJECTS (" . count($subjects) . "):\n";
    foreach ($subjects as $subject) {
        $qStmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE subject_id = ?");
        $qStmt->execute([$subject->id]);
        $qCount = $qStmt->fetchColumn();
        echo "  - {$subject->code}: {$subject->name} ({$qCount} questions)\n";
    }

    echo "\n";

    // Check total questions
    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
    $totalQuestions = $stmt->fetchColumn();
    echo "TOTAL QUESTIONS: {$totalQuestions}\n";

    echo "\n";

    // Check exams
    $stmt = $pdo->query("SELECT * FROM exams");
    $exams = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo "EXAMS (" . count($exams) . "):\n";
    foreach ($exams as $exam) {
        echo "  - ID {$exam->id}: {$exam->title} ({$exam->status})\n";
        if ($exam->is_scheduled) {
            echo "    Scheduled: {$exam->scheduled_start_time} to {$exam->scheduled_end_time}\n";
        }
    }

    echo "\n=== DATA CHECK COMPLETE ===\n";

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
