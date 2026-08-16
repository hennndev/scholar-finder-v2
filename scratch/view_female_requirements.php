<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$names = [
    'Beasiswa ASEAN Women in STEM',
    'Beasiswa DJITU 2026',
    'Beasiswa Glow and Lovely 2026 Kuliah S1 untuk Lulusan SMA/Sederajat dan mahasiswi On Going',
    'Beasiswa Amartha STEAM Fellowship',
    'Beasiswa Pendidikan LUMINA 2026',
    'Beasiswa U-Go Scholarship Program',
    'Beasiswa S1, S2 & S3 Toeti Heraty',
    'Beasiswa Mahasiswi Kabupaten Sragen Tahun 2026'
];

$results = DB::table('scholarships')->whereIn('nama_beasiswa', $names)->get();

foreach ($results as $s) {
    echo "=========================================\n";
    echo "Nama Beasiswa: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: \n" . $s->persyaratan . "\n";
    echo "Deskripsi: \n" . $s->deskripsi . "\n";
}
