<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Helmut Schmidt%')->first();
echo "PERSYARATAN:\n" . $s->persyaratan . "\n\n";
echo "DESKRIPSI:\n" . $s->deskripsi . "\n\n";
echo "BENEFIT:\n" . $s->benefit . "\n\n";

$sectionText = strtolower($s->persyaratan . ' ' . $s->deskripsi . ' ' . $s->benefit);
preg_match_all('/\bjerman\b/u', $sectionText, $matches, PREG_OFFSET_CAPTURE);
print_r($matches);
