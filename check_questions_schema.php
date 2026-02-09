<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== QUESTIONS TABLE SCHEMA ===\n\n";

$stmt = $pdo->query('DESCRIBE questions');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-25s %-20s\n", $row['Field'], '(' . $row['Type'] . ')');
}

echo "\n=== SAMPLE QUESTION DATA ===\n\n";
$stmt = $pdo->query('SELECT * FROM questions LIMIT 1');
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($question) {
    foreach ($question as $field => $value) {
        echo sprintf("%-25s : %s\n", $field, $value);
    }
} else {
    echo "No questions found in database.\n";
}
