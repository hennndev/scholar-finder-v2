<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = app(App\Http\Controllers\ChatbotController::class);
$ref = new ReflectionMethod($c, 'understandQuery');
$ref->setAccessible(true);
try {
    print_r($ref->invoke($c, 'beasiswa itu syarat skor toeflnya berapa?', 'Beasiswa yang sedang dipilih user: "Beasiswa Master in International and Development Economics (MIDE)" (negara: Jerman, jenjang: S2).'));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
