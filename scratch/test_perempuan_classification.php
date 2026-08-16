<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ChatbotController;

$controller = new ChatbotController();
$reflector = new ReflectionClass(ChatbotController::class);

$message = "beasiswa khusus perempuan";

echo "Testing message: '{$message}'\n";

try {
    $understandQuery = $reflector->getMethod('understandQuery');
    $understandQuery->setAccessible(true);
    
    $sessionContext = "Beasiswa yang sedang dipilih user: null";
    $result = $understandQuery->invoke($controller, $message, $sessionContext);
    
    echo "Classification result:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
