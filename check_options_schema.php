<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== OPTIONS TABLE SCHEMA ===\n\n";

$stmt = $pdo->query('DESCRIBE options');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-25s %-20s\n", $row['Field'], '(' . $row['Type'] . ')');
}
