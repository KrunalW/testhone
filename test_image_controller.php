<?php
// Test ImageController directly
require __DIR__ . '/vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

// Create controller instance
$controller = new \App\Controllers\ImageController();

// Test serve method
echo "Testing ImageController::serve('questions', '1767774062_71c71d8f6d1fca063aca.png')\n\n";

$response = $controller->serve('questions', '1767774062_71c71d8f6d1fca063aca.png');

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Headers:\n";
foreach ($response->getHeaders() as $name => $header) {
    echo "  {$name}: " . $header->getValue() . "\n";
}

$body = $response->getBody();
echo "\nBody length: " . strlen($body) . " bytes\n";
echo "Is image: " . (strlen($body) > 100 ? 'YES' : 'NO') . "\n";
