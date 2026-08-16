<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$s = DB::table('scholarships')->where('nama_beasiswa', 'like', '%Kuas E Scholarship%')->first();

$sessionStore = session()->driver();
$sessionStore->put('selected_scholarship', (array)$s);

$messages = [
    'apakah ada syarat UTBK?',
    'berapa syarat minimal nilai UTBK-nya?'
];

foreach ($messages as $msg) {
    echo "\n=== Testing KUAS query: \"$msg\" ===\n";
    $request = Request::create('/chatbot/ask', 'POST', [
        'message' => $msg,
        'rag_enabled' => true
    ]);
    $request->setLaravelSession($sessionStore);

    $controller = new \App\Http\Controllers\ChatbotController();
    $response = $controller->ask($request);
    
    $content = json_decode($response->getContent(), true);
    echo "Status: " . ($content['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Bot Response:\n" . $content['answer'] . "\n";
}
