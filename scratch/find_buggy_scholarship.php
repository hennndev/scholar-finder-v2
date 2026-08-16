<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$all = DB::table('scholarships')->get();
foreach ($all as $s) {
    if (str_contains($s->persyaratan, 'sertifikat pajak Jepang') || str_contains($s->persyaratan, 'residence card')) {
        echo "ID: " . $s->id . "\n";
        echo "Nama: " . $s->nama_beasiswa . "\n";
        echo "Persyaratan: " . $s->persyaratan . "\n";
        echo "---------------------------------\n";
    }
}
