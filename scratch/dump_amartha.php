<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Amartha%')->first();
if ($s) {
    echo "Nama: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: " . $s->persyaratan . "\n";
    echo "Deskripsi: " . $s->deskripsi . "\n";
    echo "Benefit: " . $s->benefit . "\n";
} else {
    echo "Amartha not found\n";
}
