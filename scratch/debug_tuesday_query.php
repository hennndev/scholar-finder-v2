<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new \App\Http\Controllers\ChatbotController();
$reflector = new \ReflectionClass($controller);
$understandQuery = $reflector->getMethod('understandQuery');
$understandQuery->setAccessible(true);

$sessionContext = "Selected scholarship: null";

$message = 'Tampilkan beasiswa yang tutup hari Selasa minggu depan';
$res = $understandQuery->invoke($controller, $message, $sessionContext);

echo "Raw LLM output:\n";
print_r($res);
