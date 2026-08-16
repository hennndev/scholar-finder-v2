<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%ASEAN Asia University%')->first();

$sessionStore = session()->driver();
$sessionStore->put('selected_scholarship', (array)$s);

$message = 'Ada syarat harus bisa bahasa Jepang ga?';

echo "=== Testing ASEAN Asia University Japan validation query ===\n";
$request = Request::create('/chatbot/ask', 'POST', [
    'message' => $message,
    'rag_enabled' => true
]);

// Share session
$request->setLaravelSession($sessionStore);

$controller = new \App\Http\Controllers\ChatbotController();
$response = $controller->ask($request);

$content = json_decode($response->getContent(), true);
print_r($content);
