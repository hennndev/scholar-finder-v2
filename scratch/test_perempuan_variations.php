<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$messages = [
    'berikan beasiswa khusus wanita',
    'berikan beasiswa yang cuma cewe aja'
];

foreach ($messages as $msg) {
    echo "\n=== Testing female query: \"$msg\" ===\n";
    $request = Request::create('/chatbot/ask', 'POST', [
        'message' => $msg,
        'rag_enabled' => true
    ]);

    $sessionStore = $app->make('session')->driver('array');
    $request->setLaravelSession($sessionStore);

    $controller = new \App\Http\Controllers\ChatbotController();
    $response = $controller->ask($request);
    
    $content = json_decode($response->getContent(), true);
    echo "Status: " . ($content['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Bot Response:\n" . $content['answer'] . "\n";
}
