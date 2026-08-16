<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$controller = app(\App\Http\Controllers\ChatbotController::class);
$reflection = new ReflectionClass(get_class($controller));
$method = $reflection->getMethod('generateEmbedding');
$method->setAccessible(true);

try {
    $searchQuery = "Cari beasiswa S1 di benua Australia yang partial";
    $embedding = $method->invokeArgs($controller, [$searchQuery]);
    
    $results = DB::select("SELECT id, nama_beasiswa, similarity FROM hybrid_search(?::text, ?::vector, ?::int)", [
        $searchQuery, '[' . implode(',', $embedding) . ']', 1000
    ]);
    
    $targets = [
        "Curtin University John Curtin Global Excellence Scholarship",
        "Deakin University Vice Chancellor S International Scholarship",
        "The University Of Sydney Vice Chancellor S International Scholarships",
        "University Of Waikato Vice Chancellor S International Excellence Scholarship For South East Asia",
        "The University Of New South Wales Unsw Unsw Scholarships For International Students"
    ];
    
    echo "Raw Hybrid Search Order for our targets:\n";
    $rank = 1;
    foreach ($results as $row) {
        $name = strtolower(trim($row->nama_beasiswa));
        foreach ($targets as $target) {
            if ($name == strtolower(trim($target))) {
                echo "Final Rank $rank | Original DB Rank " . (array_search($row, $results) + 1) . " | " . $target . " | " . number_format($row->similarity, 3, ',', '.') . "\n";
                $rank++;
            }
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
