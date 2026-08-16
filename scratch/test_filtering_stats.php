<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new \App\Http\Controllers\ChatbotController();

$rUnderstand = new ReflectionMethod($c, 'understandQuery');
$rUnderstand->setAccessible(true);

$rMap = new ReflectionMethod($c, 'mapLlmToCriteria');
$rMap->setAccessible(true);

$rEmbed = new ReflectionMethod($c, 'generateEmbedding');
$rEmbed->setAccessible(true);

$text = 'Saya memiliki IPK 3,50 dan ingin beasiswa S2 fully funded di Jepang';

try {
    // 1. Dapatkan criteria dari LLM
    $llm = $rUnderstand->invoke($c, $text, "(Belum ada konteks percakapan sebelumnya.)");
    $criteria = $rMap->invoke($c, $llm);
    
    // override still_open untuk melihat data tanpa pembatasan waktu
    $criteria['still_open'] = false;
    // tapi set IPK ke 3.50 sesuai input
    $criteria['req_values'] = ['ipk' => '3.50'];

    // 2. Lakukan Hybrid Search (1000 data teratas)
    $embedding = $rEmbed->invoke($c, $text);
    $searchIds = DB::select("SELECT id FROM hybrid_search(?::text, ?::vector, ?::int)", [
        $text, '[' . implode(',', $embedding) . ']', 1000
    ]);
    $ids = array_map(fn($r) => $r->id, $searchIds);
    
    $rawResults = DB::table('scholarships')
        ->whereIn('id', $ids)
        ->select(['id', 'nama_beasiswa', 'benua', 'negara', 'jenjang', 'deskripsi', 'deadline', 'kategori', 'jurusan', 'benefit', 'persyaratan'])
        ->get()
        ->all();

    $idMap = array_flip($ids);
    usort($rawResults, function($a, $b) use ($idMap) {
        return ($idMap[$a->id] ?? 999) - ($idMap[$b->id] ?? 999);
    });

    $totalRaw = count($rawResults);
    echo "Hasil Awal (Hybrid Search): " . $totalRaw . " dokumen\n";

    // 1. Filter Negara
    $afterNegara = array_filter($rawResults, function($r) use ($criteria) {
        if (empty($criteria['negara'])) return true;
        $m = false;
        $rowNegara = strtolower($r->negara ?? '');
        foreach ($criteria['negara'] as $c) {
            if (preg_match('/\b' . preg_quote($c, '/') . '\b/i', $rowNegara)) {
                $m = true;
                break;
            }
        }
        return $m;
    });
    echo "Setelah Filter Negara (Jepang): " . count($afterNegara) . " dokumen\n";

    // 2. Filter Jenjang
    $afterJenjang = array_filter($afterNegara, function($r) use ($criteria) {
        if (empty($criteria['jenjang'])) return true;
        $m = false;
        foreach ($criteria['jenjang'] as $l) {
            if (str_contains(strtoupper($r->jenjang ?? ''), $l)) {
                $m = true;
                break;
            }
        }
        return $m;
    });
    echo "Setelah Filter Jenjang (S2): " . count($afterJenjang) . " dokumen\n";

    // 3. Filter Funding
    $afterFunding = array_filter($afterJenjang, function($r) use ($criteria) {
        if (empty($criteria['funding'])) return true;
        $target = strtolower($criteria['funding']);
        $actual = strtolower($r->kategori ?? '');
        if ($target === 'exchange') {
            if (!str_contains($actual, 'exchange') && !str_contains($actual, 'pertukaran')) return false;
        } elseif ($target === 'partially funded') {
            if (!str_contains($actual, 'partially') && !str_contains($actual, 'sebagian') && !str_contains($actual, 'partial')) return false;
        } else {
            if (!str_contains($actual, 'fully') && !str_contains($actual, 'penuh')) return false;
        }
        return true;
    });
    echo "Setelah Filter Funding (Fully Funded): " . count($afterFunding) . " dokumen\n";

    // 4. Filter IPK / Persyaratan
    $rApply = new ReflectionMethod($c, 'applyStrictFilters');
    $rApply->setAccessible(true);
    
    $finalResults = $rApply->invoke($c, $afterFunding, $criteria);
    echo "Setelah Filter IPK (Nilai IPK >= Persyaratan / IPK 3.50): " . count($finalResults) . " dokumen\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
