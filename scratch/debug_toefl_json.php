<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new \App\Http\Controllers\ChatbotController();
$reflector = new \ReflectionClass($controller);
$understandQuery = $reflector->getMethod('understandQuery');
$understandQuery->setAccessible(true);

$sessionContext = "Selected scholarship: Curtin University Curtin Global Merit Scholarship. Available fields: nama_beasiswa, persyaratan, deskripsi, benefit, kategori, deadline, url_asli";

$message = 'skor toefl nya harus berapa';
$res = $understandQuery->invoke($controller, $message, $sessionContext);

echo "Raw LLM output:\n";
print_r($res);
