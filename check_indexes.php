<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== CHECKING PERFORMANCE INDEXES ===\n\n";

$tables = ['user_answers', 'questions', 'options', 'exam_subject_distribution', 'exam_sessions'];

foreach ($tables as $table) {
    echo "Table: {$table}\n";
    $stmt = $pdo->query("SHOW INDEX FROM {$table}");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $uniqueIndexes = [];
    foreach ($indexes as $index) {
        if (!in_array($index['Key_name'], $uniqueIndexes)) {
            $uniqueIndexes[] = $index['Key_name'];
        }
    }

    foreach ($uniqueIndexes as $indexName) {
        if ($indexName !== 'PRIMARY') {
            echo "  ✅ {$indexName}\n";
        }
    }
    echo "\n";
}
