<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$message = 'Rekomendasi beasiswa S1 dengan IPK minimal 3.25';

echo "=== Testing S1 GPA 3.25 query ===\n";
$request = Request::create('/chatbot/ask', 'POST', [
    'message' => $message,
    'rag_enabled' => true
]);

$sessionStore = session()->driver();
$request->setLaravelSession($sessionStore);

$controller = new \App\Http\Controllers\ChatbotController();
$response = $controller->ask($request);

$content = json_decode($response->getContent(), true);
echo "Status: " . ($content['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Bot Response:\n" . $content['answer'] . "\n";
