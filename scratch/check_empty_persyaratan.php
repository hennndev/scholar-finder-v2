<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$all = DB::table('scholarships')->get();
$emptyCount = 0;
$emptyWithReqsInDesc = 0;

foreach ($all as $s) {
    $persyaratan = trim($s->persyaratan);
    if ($persyaratan === '') {
        $emptyCount++;
        $desc = strtolower($s->deskripsi);
        if (str_contains($desc, 'ielts') || str_contains($desc, 'toefl') || str_contains($desc, 'ipk') || str_contains($desc, 'gpa') || str_contains($desc, 'usia')) {
            $emptyWithReqsInDesc++;
            echo "Empty persyaratan but requirements in desc: " . $s->nama_beasiswa . "\n";
        }
    }
}

echo "Total scholarships: " . count($all) . "\n";
echo "Empty persyaratan count: $emptyCount\n";
echo "Empty persyaratan with requirements in desc count: $emptyWithReqsInDesc\n";
