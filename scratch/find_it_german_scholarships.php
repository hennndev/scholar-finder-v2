<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$all = DB::table('scholarships')->where('negara', 'like', '%Jerman%')->get();
foreach ($all as $s) {
    echo "=========================================\n";
    echo "ID: " . $s->id . "\n";
    echo "Nama: " . $s->nama_beasiswa . "\n";
    echo "Jurusan: " . $s->jurusan . "\n";
}
