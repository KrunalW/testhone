<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== EXAMS TABLE - STATUS COLUMN ===\n\n";

$stmt = $pdo->query('DESCRIBE exams');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['Field'] == 'status') {
        print_r($row);
        break;
    }
}

echo "\n=== UPDATE EXAMS TO ACTIVE STATUS ===\n\n";
echo "Would you like to update all draft/empty exams to 'active' status?\n";
echo "This will make them visible on the dashboard.\n\n";

// Update exams with empty/draft status to active
$stmt = $pdo->prepare("UPDATE exams SET status = 'active' WHERE status IS NULL OR status = '' OR status = 'draft'");
$result = $stmt->execute();

$affected = $stmt->rowCount();
echo "Updated {$affected} exam(s) to 'active' status.\n\n";

// Show updated exams
$stmt = $pdo->query("SELECT id, title, status FROM exams");
echo "=== ALL EXAMS AFTER UPDATE ===\n";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID {$row['id']}: {$row['title']} - Status: {$row['status']}\n";
}
