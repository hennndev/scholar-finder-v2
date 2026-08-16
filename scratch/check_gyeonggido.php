<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$names = [
    'Gyeonggi-do Outstanding Foreign Student Scholarship',
    'Gyeongsang National University Graduate Scholarship'
];

$results = DB::table('scholarships')->whereIn('nama_beasiswa', $names)->get();

foreach ($results as $s) {
    echo "=========================================\n";
    echo "Nama Beasiswa: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: \n" . $s->persyaratan . "\n";
}
