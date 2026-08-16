<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Nagoya University Of Commerce%')->first();
if ($s) {
    echo "Nama: " . $s->nama_beasiswa . "\n";
    echo "Persyaratan: \n" . $s->persyaratan . "\n";
    
    $controller = new \App\Http\Controllers\ChatbotController();
    $reflector = new \ReflectionClass($controller);
    
    $getNumericSectionText = $reflector->getMethod('getNumericSectionText');
    $getNumericSectionText->setAccessible(true);
    $numText = $getNumericSectionText->invoke($controller, $s, 'ipk');
    echo "getNumericSectionText: '$numText'\n";
    
    $isNegatedRequirement = $reflector->getMethod('isNegatedRequirement');
    $isNegatedRequirement->setAccessible(true);
    $isNeg = $isNegatedRequirement->invoke($controller, $numText);
    echo "isNegatedRequirement: " . ($isNeg ? "YES" : "NO") . "\n";
    
    $gpaEligible = $reflector->getMethod('gpaEligible');
    $gpaEligible->setAccessible(true);
    $res = $gpaEligible->invoke($controller, $numText, '3.25', $numText === '' || $isNeg);
    echo "gpaEligible: " . ($res ? "TRUE" : "FALSE") . "\n";
} else {
    echo "Not found!\n";
}
