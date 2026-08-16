<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = app(\App\Http\Controllers\ChatbotController::class);

// Access private method using reflection
$reflection = new ReflectionClass(get_class($controller));
$method = $reflection->getMethod('generateEmbedding');
$method->setAccessible(true);

try {
    $text = "Cari beasiswa S1 di benua Australia";
    $embedding = $method->invokeArgs($controller, [$text]);
    
    echo "Dimensi 1: " . $embedding[0] . "\n";
    echo "Dimensi 2: " . $embedding[1] . "\n";
    echo "Dimensi 3: " . $embedding[2] . "\n";
    echo "Dimensi 4: " . $embedding[3] . "\n";
    echo "Dimensi 5: " . $embedding[4] . "\n";
    echo "...\n";
    echo "Dimensi 1534: " . $embedding[1533] . "\n";
    echo "Dimensi 1535: " . $embedding[1534] . "\n";
    echo "Dimensi 1536: " . $embedding[1535] . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
