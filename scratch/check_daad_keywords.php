<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Helmut Schmidt%')->first();
$combined = strtolower($s->nama_beasiswa . ' ' . $s->persyaratan . ' ' . $s->deskripsi);

$patterns = ['jerman', 'german', 'deutsch', 'dsh', 'testdaf', 'goethe'];
foreach ($patterns as $p) {
    if (str_contains($combined, $p)) {
        echo "str_contains matched: $p\n";
    }
    if (preg_match('/\b' . preg_quote($p, '/') . '\b/u', $combined)) {
        echo "preg_match matched: $p\n";
    }
}
