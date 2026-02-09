<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== TESTING QUESTION UPDATE ===\n\n";

// Get a sample question
$stmt = $pdo->query("SELECT * FROM questions LIMIT 1");
$question = $stmt->fetch(PDO::FETCH_OBJ);

if ($question) {
    echo "Question ID: {$question->id}\n";
    echo "Subject ID: {$question->subject_id}\n";
    echo "Question Text: " . substr($question->question_text, 0, 50) . "...\n";
    echo "Question Type: {$question->question_type}\n";
    echo "Question Image Path: " . ($question->question_image_path ?: 'NULL') . "\n";
    echo "Explanation: " . ($question->explanation ?: 'NULL') . "\n\n";

    // Test update
    echo "Testing update of question_type to 'image'...\n";
    $stmt = $pdo->prepare("UPDATE questions SET question_type = 'image', updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$question->id]);

    if ($result) {
        echo "✅ Update executed\n";

        // Verify
        $stmt = $pdo->prepare("SELECT question_type FROM questions WHERE id = ?");
        $stmt->execute([$question->id]);
        $updated = $stmt->fetch(PDO::FETCH_OBJ);

        echo "New question_type: {$updated->question_type}\n";

        if ($updated->question_type === 'image') {
            echo "✅ Question type successfully updated to 'image'\n";
        } else {
            echo "❌ Question type did not update properly\n";
        }

        // Restore original value
        $stmt = $pdo->prepare("UPDATE questions SET question_type = ? WHERE id = ?");
        $stmt->execute([$question->question_type, $question->id]);
        echo "\n✅ Restored original value: {$question->question_type}\n";
    } else {
        echo "❌ Update failed\n";
    }
} else {
    echo "No questions found in database\n";
}
