<?php

// Simple test without full CI bootstrap
echo "=== Testing Language Helper Logic ===\n\n";

// Simulate getText function
function testGetText(?string $englishText, ?string $marathiText, string $language): string
{
    if ($language === 'marathi' && !empty($marathiText)) {
        return $marathiText;
    }
    return $englishText ?? '';
}

// Test 1
echo "Test 1: Both languages provided\n";
$result = testGetText("Hello", "नमस्कार", "english");
echo "English: $result (Expected: Hello) - " . ($result === "Hello" ? "✓ PASS" : "✗ FAIL") . "\n";

$result = testGetText("Hello", "नमस्कार", "marathi");
echo "Marathi: $result (Expected: नमस्कार) - " . ($result === "नमस्कार" ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 2
echo "Test 2: Marathi is NULL (fallback)\n";
$result = testGetText("Welcome", null, "marathi");
echo "Result: $result (Expected: Welcome) - " . ($result === "Welcome" ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 3
echo "Test 3: Marathi is empty string (fallback)\n";
$result = testGetText("Good morning", "", "marathi");
echo "Result: $result (Expected: Good morning) - " . ($result === "Good morning" ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 4
echo "Test 4: English is NULL\n";
$result = testGetText(null, "मराठी", "marathi");
echo "Result: $result (Expected: मराठी) - " . ($result === "मराठी" ? "✓ PASS" : "✗ FAIL") . "\n";

$result = testGetText(null, "मराठी", "english");
echo "Result: $result (Expected: empty) - " . ($result === "" ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "=== All Logic Tests Completed ===\n";
