<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new \App\Http\Controllers\ChatbotController();
$r = new ReflectionMethod($c, 'generateEmbedding');
$r->setAccessible(true);
$text = 'Saya ingin beasiswa S2 fully funded di Jepang yang masih buka';
try {
    $emb = $r->invoke($c, $text);
    echo "LENGTH: " . count($emb) . "\n";
    for ($i = 0; $i < 10; $i++) {
        echo "INDEX " . ($i + 1) . ": " . sprintf("%.8f", $emb[$i]) . "\n";
    }
    echo "...\n";
    for ($i = 1530; $i < 1536; $i++) {
        echo "INDEX " . ($i + 1) . ": " . sprintf("%.8f", $emb[$i]) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
