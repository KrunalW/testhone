<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== DEBUG IMAGE PATHS ===\n\n";

// Check questions with images
$stmt = $pdo->query("SELECT id, question_text, question_type, question_image_path
                     FROM questions
                     WHERE question_image_path IS NOT NULL
                     LIMIT 3");
$questions = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "Questions with images:\n";
foreach ($questions as $q) {
    echo "\nQuestion #{$q->id}:\n";
    echo "  Text: " . substr($q->question_text, 0, 50) . "...\n";
    echo "  Type: {$q->question_type}\n";
    echo "  Path in DB: {$q->question_image_path}\n";
    echo "  Expected URL: http://localhost:8080/{$q->question_image_path}\n";

    // Check if file exists
    $filePath = str_replace('uploads/', 'writable/uploads/', $q->question_image_path);
    echo "  File path: {$filePath}\n";
    echo "  File exists: " . (file_exists($filePath) ? "YES" : "NO") . "\n";
}

// Check options with images
$stmt = $pdo->query("SELECT o.id, o.option_text, o.option_image_path, q.id as question_id
                     FROM options o
                     JOIN questions q ON o.question_id = q.id
                     WHERE o.option_image_path IS NOT NULL
                     LIMIT 3");
$options = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "\n\nOptions with images:\n";
foreach ($options as $opt) {
    echo "\nOption #{$opt->id} (Question #{$opt->question_id}):\n";
    echo "  Text: " . substr($opt->option_text, 0, 50) . "...\n";
    echo "  Path in DB: {$opt->option_image_path}\n";
    echo "  Expected URL: http://localhost:8080/{$opt->option_image_path}\n";

    // Check if file exists
    $filePath = str_replace('uploads/', 'writable/uploads/', $opt->option_image_path);
    echo "  File path: {$filePath}\n";
    echo "  File exists: " . (file_exists($filePath) ? "YES" : "NO") . "\n";
}

echo "\n\n=== TEST IMAGE CONTROLLER ===\n";
echo "Try accessing these URLs in browser:\n";
foreach ($questions as $q) {
    if ($q->question_image_path) {
        echo "http://localhost:8080/{$q->question_image_path}\n";
    }
}
foreach ($options as $opt) {
    if ($opt->option_image_path) {
        echo "http://localhost:8080/{$opt->option_image_path}\n";
    }
}
