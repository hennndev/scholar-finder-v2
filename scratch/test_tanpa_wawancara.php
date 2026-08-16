<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$message = 'beasiswa tanpa wawancara';

$request = Request::create('/chatbot/ask', 'POST', [
    'message' => $message,
    'rag_enabled' => true
]);

// Set empty session store on the request so session helper works
$sessionStore = $app->make('session')->driver('array');
$request->setLaravelSession($sessionStore);

$controller = new \App\Http\Controllers\ChatbotController();
$response = $controller->ask($request);

echo "Response Status Code: " . $response->getStatusCode() . "\n";
$content = json_decode($response->getContent(), true);
echo "Response Content:\n";
print_r($content);
