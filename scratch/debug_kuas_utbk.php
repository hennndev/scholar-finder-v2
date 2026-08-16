<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Kuas E Scholarship%')->first();
if ($s) {
    echo "Nama: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: \n" . $s->persyaratan . "\n";
    
    $controller = new \App\Http\Controllers\ChatbotController();
    $reflector = new \ReflectionClass($controller);
    
    $getRequirementSectionText = $reflector->getMethod('getRequirementSectionText');
    $getRequirementSectionText->setAccessible(true);
    $targetText = $getRequirementSectionText->invoke($controller, $s, 'dokumen');
    echo "targetText: '$targetText'\n";
    
    $requirementValueMatches = $reflector->getMethod('requirementValueMatches');
    $requirementValueMatches->setAccessible(true);
    $res = $requirementValueMatches->invoke($controller, $s, 'dokumen', 'utbk 650');
    echo "requirementValueMatches: " . ($res ? "TRUE" : "FALSE") . "\n";
} else {
    echo "Not found!\n";
}
