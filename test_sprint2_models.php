<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Sprint 2 Models...\n";

// Test JournalEditorialBoard model
try {
    $boardModel = new App\Models\JournalEditorialBoard();
    echo "✓ JournalEditorialBoard model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ JournalEditorialBoard model error: " . $e->getMessage() . "\n";
}

// Test JournalMembership model
try {
    $membershipModel = new App\Models\JournalMembership();
    echo "✓ JournalMembership model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ JournalMembership model error: " . $e->getMessage() . "\n";
}

// Test Article model enhancements
try {
    $articleModel = new App\Models\Article();
    echo "✓ Article model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Article model error: " . $e->getMessage() . "\n";
}

// Test ArticleCategory model enhancements
try {
    $categoryModel = new App\Models\ArticleCategory();
    echo "✓ ArticleCategory model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ ArticleCategory model error: " . $e->getMessage() . "\n";
}

// Test Member model enhancements
try {
    $memberModel = new App\Models\Member();
    echo "✓ Member model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Member model error: " . $e->getMessage() . "\n";
}

echo "\nAll models tested!\n";
