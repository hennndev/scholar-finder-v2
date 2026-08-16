<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$all = DB::table('scholarships')->get();
echo "Total scholarships: " . count($all) . "\n";

$wawancaraCount = 0;
$tanpaWawancaraCount = 0;
foreach ($all as $s) {
    $content = strtolower($s->nama_beasiswa . ' ' . $s->persyaratan . ' ' . $s->deskripsi);
    if (str_contains($content, 'wawancara')) {
        $wawancaraCount++;
        if (str_contains($content, 'tanpa wawancara')) {
            $tanpaWawancaraCount++;
            echo "Tanpa wawancara: " . $s->nama_beasiswa . "\n";
        }
    }
}
echo "Mentions 'wawancara': $wawancaraCount\n";
echo "Mentions 'tanpa wawancara': $tanpaWawancaraCount\n";
echo "No mention of 'wawancara': " . (count($all) - $wawancaraCount) . "\n";
