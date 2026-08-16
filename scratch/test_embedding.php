<?php
use Illuminate\Support\Facades\Http;

$apiKey = env('OPENAI_API_KEY');
$text = "Cari beasiswa S1 di benua Australia";

$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->post('https://api.openai.com/v1/embeddings', [
    'model' => 'text-embedding-ada-002',
    'input' => $text,
]);

$data = $response->json();
if (isset($data['data'][0]['embedding'])) {
    $embedding = $data['data'][0]['embedding'];
    echo "Dimensi 1: " . $embedding[0] . "\n";
    echo "Dimensi 2: " . $embedding[1] . "\n";
    echo "Dimensi 3: " . $embedding[2] . "\n";
    echo "Dimensi 4: " . $embedding[3] . "\n";
    echo "Dimensi 5: " . $embedding[4] . "\n";
    echo "...\n";
    echo "Dimensi 1534: " . $embedding[1533] . "\n";
    echo "Dimensi 1535: " . $embedding[1534] . "\n";
    echo "Dimensi 1536: " . $embedding[1535] . "\n";
} else {
    echo "Gagal mengambil embedding: " . print_r($data, true);
}
