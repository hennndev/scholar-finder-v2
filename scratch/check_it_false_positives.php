<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$names = [
    'Daad Epos International Education Management Inema Ph Ludwigsburg Helwan University',
    'Erasmus Mundus Joint Master Social Psychology Of Transformation Spot',
];

$results = DB::table('scholarships')->whereIn('nama_beasiswa', $names)->get();

foreach ($results as $s) {
    echo "=========================================\n";
    echo "Nama Beasiswa: " . $s->nama_beasiswa . "\n";
    $persyaratan = strtolower($s->persyaratan ?? '');
    
    preg_match_all('/\bit\b/ui', $persyaratan, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[0] as $m) {
        $offset = $m[1];
        $snippet = substr($persyaratan, max(0, $offset - 20), 40);
        echo "Match: '{$m[0]}' at offset $offset. Context: '...$snippet...'\n";
    }
}
