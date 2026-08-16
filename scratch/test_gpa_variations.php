<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Select Curtin University (which has a GPA requirement in structured or unstructured data)
// Actually let's select a scholarship with structured GPA
$s = DB::table('scholarships')->where('persyaratan', 'like', '%GPA%')->first();
if (!$s) {
    $s = DB::table('scholarships')->where('persyaratan', 'like', '%IPK%')->first();
}

$sessionStore = session()->driver();
$sessionStore->put('selected_scholarship', (array)$s);

$messages = [
    'emang berapa syarat ipknya?',
    'butuh ipk berapa',
    'apakah ada syarat IPK?'
];

foreach ($messages as $msg) {
    echo "\n=== Testing GPA query variation: \"$msg\" ===\n";
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
