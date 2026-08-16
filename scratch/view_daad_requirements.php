<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Helmut Schmidt%')->first();
if ($s) {
    echo "Nama: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: \n" . $s->persyaratan . "\n";
    echo "Deskripsi: \n" . $s->deskripsi . "\n";
} else {
    echo "Not found!\n";
}
