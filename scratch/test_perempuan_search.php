<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;

$controller = new ChatbotController();

$request = Request::create('/api/chatbot', 'POST', [
    'message' => "berikan beasiswa khusus perempuan",
    'rag_enabled' => true
]);

// Set empty session store on the request so session helper works
$sessionStore = $app->make('session')->driver('array');
$request->setLaravelSession($sessionStore);

$response = $controller->ask($request);
$data = json_decode($response->getContent(), true);

echo "Chatbot Response:\n";
echo $data['answer'] ?? 'No answer';
echo "\n";
