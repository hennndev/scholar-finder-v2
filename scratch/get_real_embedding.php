<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'] ?? '';

$text = "Saya ingin beasiswa S2 Fully Funded di Jepang yang masih buka.";

// Check cache
$cacheFile = __DIR__ . '/../embedding_cache.json';
if (file_exists($cacheFile)) {
    $cache = json_decode(file_get_contents($cacheFile), true);
    $key = md5($text);
    if (isset($cache[$key])) {
        echo "FOUND IN CACHE!\n";
        $emb = $cache[$key];
        echo "Dimension count: " . count($emb) . "\n";
        echo "First 5:\n";
        for ($i=0; $i<5; $i++) {
            echo sprintf("%5d | %15.8f\n", $i+1, $emb[$i]);
        }
        echo "Last 5:\n";
        for ($i=1531; $i<1536; $i++) {
            echo sprintf("%5d | %15.8f\n", $i+1, $emb[$i]);
        }
        exit;
    }
}

// OpenRouter API call
$ch = curl_init('https://openrouter.ai/api/v1/embeddings');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'openai/text-embedding-3-small',
    'input' => $text
]));
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);

if (isset($data['data'][0]['embedding'])) {
    $emb = $data['data'][0]['embedding'];
    echo "FROM OPENROUTER API:\n";
    echo "Dimension count: " . count($emb) . "\n\n";
    echo "First 5:\n";
    for ($i=0; $i<5; $i++) {
        echo sprintf("| %7d | %15.8f |\n", $i+1, $emb[$i]);
    }
    echo "...\n";
    echo "Last 5:\n";
    for ($i=1531; $i<1536; $i++) {
        echo sprintf("| %7d | %15.8f |\n", $i+1, $emb[$i]);
    }
} else {
    echo "ERROR:\n" . $res . "\n";
}
