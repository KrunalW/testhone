<?php

$writable = 'writable/uploads';

// Create directories if they don't exist
if (!is_dir($writable . '/questions')) {
    mkdir($writable . '/questions', 0777, true);
    echo "✅ Created questions directory\n";
} else {
    echo "✓ Questions directory exists\n";
}

if (!is_dir($writable . '/options')) {
    mkdir($writable . '/options', 0777, true);
    echo "✅ Created options directory\n";
} else {
    echo "✓ Options directory exists\n";
}

echo "\n=== DIRECTORY PERMISSIONS ===\n";
echo "questions: " . (is_writable($writable . '/questions') ? '✅ writable' : '❌ NOT writable') . "\n";
echo "options: " . (is_writable($writable . '/options') ? '✅ writable' : '❌ NOT writable') . "\n";

echo "\n=== DIRECTORY PATHS ===\n";
echo "Full path (questions): " . realpath($writable . '/questions') . "\n";
echo "Full path (options): " . realpath($writable . '/options') . "\n";

// Check if WRITEPATH constant works
define('WRITEPATH', realpath('writable') . DIRECTORY_SEPARATOR);
echo "\n=== WRITEPATH ===\n";
echo "WRITEPATH: " . WRITEPATH . "\n";
echo "uploads/questions: " . WRITEPATH . 'uploads/questions' . "\n";
echo "uploads/options: " . WRITEPATH . 'uploads/options' . "\n";
