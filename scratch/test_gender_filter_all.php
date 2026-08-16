<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$all = DB::table('scholarships')->get();

echo "Scholarships matching 'perempuan' filter:\n";
$count = 0;
foreach ($all as $r) {
    $content = strtolower(($r->nama_beasiswa ?? '') . ' ' . ($r->persyaratan ?? '') . ' ' . ($r->deskripsi ?? ''));
    
    // Female keywords check
    $hasFemale = str_contains($content, 'perempuan') || str_contains($content, 'wanita') || str_contains($content, 'mahasiswi') || str_contains($content, 'putri') || str_contains($content, 'siswi');
    
    // Unisex / male keywords check
    $isUnisexOrMale = preg_match('/\b(pria|laki[-‑\s]*laki|putra)\b/ui', $content) ||
                      preg_match('/siswa\s*(dan|atau|\/|-)?\s*siswi/ui', $content);
    
    if ($hasFemale && !$isUnisexOrMale) {
        $count++;
        echo "{$count}. {$r->nama_beasiswa}\n";
    }
}
