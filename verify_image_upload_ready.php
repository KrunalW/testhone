<?php
$pdo = new PDO('mysql:host=localhost;dbname=analytics_dashboard', 'root', '');

echo "=== IMAGE UPLOAD READINESS CHECK ===\n\n";

// Check questions table
echo "1. Questions Table:\n";
$stmt = $pdo->query("SHOW COLUMNS FROM questions LIKE 'question_type'");
$hasQuestionType = $stmt->rowCount() > 0;
echo "   question_type: " . ($hasQuestionType ? "✅ EXISTS" : "❌ MISSING") . "\n";

$stmt = $pdo->query("SHOW COLUMNS FROM questions LIKE 'question_image_path'");
$hasQuestionImagePath = $stmt->rowCount() > 0;
echo "   question_image_path: " . ($hasQuestionImagePath ? "✅ EXISTS" : "❌ MISSING") . "\n";

// Check options table
echo "\n2. Options Table:\n";
$stmt = $pdo->query("SHOW COLUMNS FROM options LIKE 'option_image_path'");
$hasOptionImagePath = $stmt->rowCount() > 0;
echo "   option_image_path: " . ($hasOptionImagePath ? "✅ EXISTS" : "❌ MISSING") . "\n";

// Check upload directories
echo "\n3. Upload Directories:\n";
$questionDir = 'writable/uploads/questions';
$optionDir = 'writable/uploads/options';

echo "   $questionDir: " . (is_dir($questionDir) && is_writable($questionDir) ? "✅ READY" : "❌ NOT READY") . "\n";
echo "   $optionDir: " . (is_dir($optionDir) && is_writable($optionDir) ? "✅ READY" : "❌ NOT READY") . "\n";

// Overall status
echo "\n=== OVERALL STATUS ===\n";
$allReady = $hasQuestionType && $hasQuestionImagePath && $hasOptionImagePath &&
            is_dir($questionDir) && is_writable($questionDir) &&
            is_dir($optionDir) && is_writable($optionDir);

if ($allReady) {
    echo "✅ SYSTEM READY FOR IMAGE UPLOADS\n";
    echo "\nYou can now:\n";
    echo "- Upload question images (max 2MB)\n";
    echo "- Upload option images (max 1MB each)\n";
    echo "- Create text or image-based questions\n";
    echo "- Use the live preview feature\n";
} else {
    echo "❌ SYSTEM NOT READY - Fix missing components above\n";
}
