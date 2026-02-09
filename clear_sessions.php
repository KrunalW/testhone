<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$result = $db->query("UPDATE exam_sessions SET status = 'expired' WHERE status = 'in_progress'");

echo "✅ Old exam sessions cleared successfully!\n";
echo "Affected rows: " . $db->affectedRows() . "\n";
