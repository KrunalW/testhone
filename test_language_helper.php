<?php

// Bootstrap CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';

// Load CodeIgniter
$app = require_once FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once SYSTEMPATH . 'bootstrap.php';

// Create app instance
$app = Config\Services::codeigniter();
$app->initialize();

// Now we can use the helper
helper('language');

echo "=== Testing Language Helper Functions ===\n\n";

// Test 1: getText with both languages
echo "Test 1: getText() with both English and Marathi\n";
$english = "What is the capital of India?";
$marathi = "भारताची राजधानी कोणती आहे?";
echo "English (default): " . getText($english, $marathi, 'english') . "\n";
echo "Marathi: " . getText($english, $marathi, 'marathi') . "\n\n";

// Test 2: getText with only English (fallback)
echo "Test 2: getText() with only English (Marathi NULL)\n";
$english = "What is 2+2?";
$marathi = null;
echo "When Marathi is NULL: " . getText($english, $marathi, 'marathi') . "\n";
echo "Expected: Should show English text\n\n";

// Test 3: getText with empty Marathi (fallback)
echo "Test 3: getText() with empty Marathi string\n";
$english = "Select the correct answer";
$marathi = "";
echo "When Marathi is empty: " . getText($english, $marathi, 'marathi') . "\n";
echo "Expected: Should show English text\n\n";

// Test 4: Session-based language
echo "Test 4: Session-based language preference\n";
setLanguage('english');
echo "Set to English: " . getCurrentLanguage() . "\n";
setLanguage('marathi');
echo "Set to Marathi: " . getCurrentLanguage() . "\n";
setLanguage('invalid'); // Should not change
echo "After invalid: " . getCurrentLanguage() . " (should still be marathi)\n\n";

// Test 5: Language labels
echo "Test 5: getLanguageLabel()\n";
echo "English label: " . getLanguageLabel('english') . "\n";
echo "Marathi label: " . getLanguageLabel('marathi') . "\n\n";

// Test 6: Toggle language
echo "Test 6: toggleLanguage()\n";
setLanguage('english');
echo "Start with: " . getCurrentLanguage() . "\n";
$newLang = toggleLanguage();
echo "After toggle: " . $newLang . " (should be marathi)\n";
$newLang = toggleLanguage();
echo "After toggle: " . $newLang . " (should be english)\n\n";

echo "=== All Tests Completed ===\n";
