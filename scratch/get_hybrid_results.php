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
    $embedding = $r->invoke($c, $text);
    $searchIds = DB::select("SELECT nama_beasiswa, similarity FROM hybrid_search(?::text, ?::vector, ?::int)", [
        $text, '[' . implode(',', $embedding) . ']', 5
    ]);
    
    echo "SUCCESS\n";
    foreach ($searchIds as $index => $row) {
        echo "RANK " . ($index + 1) . ": " . $row->nama_beasiswa . " | SCORE: " . sprintf("%.6f", $row->similarity) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
