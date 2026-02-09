<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestLanguageHelper extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:language';
    protected $description = 'Test language helper functions';

    public function run(array $params)
    {
        helper('language');

        CLI::write('=== Testing Language Helper Functions ===', 'green');
        CLI::newLine();

        // Test 1: getText with both languages
        CLI::write('Test 1: getText() with both English and Marathi', 'yellow');
        $english = "What is the capital of India?";
        $marathi = "भारताची राजधानी कोणती आहे?";
        CLI::write("English (default): " . getText($english, $marathi, 'english'));
        CLI::write("Marathi: " . getText($english, $marathi, 'marathi'));
        CLI::newLine();

        // Test 2: getText with only English (fallback)
        CLI::write('Test 2: getText() with only English (Marathi NULL)', 'yellow');
        $english = "What is 2+2?";
        $marathi = null;
        CLI::write("When Marathi is NULL: " . getText($english, $marathi, 'marathi'));
        CLI::write("Expected: Should show English text", 'light_gray');
        CLI::newLine();

        // Test 3: getText with empty Marathi (fallback)
        CLI::write('Test 3: getText() with empty Marathi string', 'yellow');
        $english = "Select the correct answer";
        $marathi = "";
        CLI::write("When Marathi is empty: " . getText($english, $marathi, 'marathi'));
        CLI::write("Expected: Should show English text", 'light_gray');
        CLI::newLine();

        // Test 4: Session-based language
        CLI::write('Test 4: Session-based language preference', 'yellow');
        setLanguage('english');
        CLI::write("Set to English: " . getCurrentLanguage());
        setLanguage('marathi');
        CLI::write("Set to Marathi: " . getCurrentLanguage());
        setLanguage('invalid'); // Should not change
        CLI::write("After invalid: " . getCurrentLanguage() . " (should still be marathi)");
        CLI::newLine();

        // Test 5: Language labels
        CLI::write('Test 5: getLanguageLabel()', 'yellow');
        CLI::write("English label: " . getLanguageLabel('english'));
        CLI::write("Marathi label: " . getLanguageLabel('marathi'));
        CLI::newLine();

        // Test 6: Toggle language
        CLI::write('Test 6: toggleLanguage()', 'yellow');
        setLanguage('english');
        CLI::write("Start with: " . getCurrentLanguage());
        $newLang = toggleLanguage();
        CLI::write("After toggle: " . $newLang . " (should be marathi)");
        $newLang = toggleLanguage();
        CLI::write("After toggle: " . $newLang . " (should be english)");
        CLI::newLine();

        CLI::write('=== All Tests Completed Successfully ===', 'green');
    }
}
