<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$all = DB::table('scholarships')->get();
$femaleScholarships = [];

foreach ($all as $s) {
    $content = strtolower($s->nama_beasiswa . ' ' . $s->persyaratan . ' ' . $s->deskripsi);
    
    // Check if contains female keywords
    if (str_contains($content, 'perempuan') || str_contains($content, 'wanita') || str_contains($content, 'mahasiswi') || str_contains($content, 'putri') || str_contains($content, 'siswi')) {
        
        // Exclude unisex
        $unisex = false;
        $unisexPatterns = [
            '/pria\s*(dan|atau|\/|-)?\s*wanita/i',
            '/laki\s*(?:-\s*laki)?\s*(dan|atau|\/|-)?\s*perempuan/i',
            '/putra\s*(dan|atau|\/|-)?\s*putri/i',
            '/siswa\s*(dan|atau|\/|-)?\s*siswi/i',
        ];
        foreach ($unisexPatterns as $pat) {
            if (preg_match($pat, $content)) {
                $unisex = true;
                break;
            }
        }
        if (preg_match('/\b(pria|laki[-‑\s]*laki|putra)\b/ui', $content)) {
            $unisex = true;
        }

        if (!$unisex) {
            $femaleScholarships[] = $s->nama_beasiswa;
        }
    }
}

echo "Total khusus perempuan: " . count($femaleScholarships) . "\n";
print_r($femaleScholarships);
