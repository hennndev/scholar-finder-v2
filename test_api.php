<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = app(App\Http\Controllers\ChatbotController::class);

$bulanIndo = [1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april', 5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus', 9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'];
$bulanSekarang = $bulanIndo[(int)date('m')];
$tahunSekarang = date('Y');

$system = <<<PROMPT
Anda adalah parser niat untuk chatbot pencari BEASISWA berbahasa Indonesia. Tugas Anda HANYA mengubah pesan user menjadi objek JSON. JANGAN menjawab pertanyaan user.

[INFORMASI WAKTU SAAT INI]:
- Bulan: $bulanSekarang
- Tahun: $tahunSekarang
(PENTING: Terjemahkan SEMUA acuan waktu relatif ke nilai absolut berdasarkan info di atas: "bulan ini"/"tahun ini"/"sekarang" -> bulan & tahun saat ini; "tahun depan" -> tahun saat ini + 1; "tahun lalu" -> tahun saat ini - 1; "bulan depan"/"bulan lalu" -> bulan terkait. Masukkan hasilnya ke array "bulan"/"tahun" pada output JSON).

Toleransi typo, singkatan (s2=S2, ln=luar negeri, dn=dalam negeri, dll), bahasa gaul, dan bahasa Inggris. Pahami maksud sebenarnya.

KONTEKS PERCAKAPAN SAAT INI:
(Belum ada konteks percakapan sebelumnya.)

Keluarkan HANYA JSON valid dengan skema:
{
  "intent": "search | detail | validation | next_page | back_to_list | out_of_topic | greeting | thanks | acknowledgment",
  "response": "<HANYA untuk intent greeting/thanks/acknowledgment: kalimat balasan ramah Bahasa Indonesia. Intent lain: null>",
  "detail_type": "benefit | syarat | deadline | funding | url | apply | detail | null",
  "ref_number": null,
  "university": null,
  "negara": [],
  "benua": [],
  "jenjang": [],
  "bidang": [],
  "bulan": [],
  "tahun": [],
  "funding": "Fully Funded | Partially Funded | Exchange | null",
  "lokasi_tipe": "luar | dalam | null",
  "sort_deadline": "asc | desc | null",
  "still_open": true,
  "deadline_before": null,
  "benefit_keywords": [],
  "flags": { "tanpa_test_bahasa": false, "ekonomi_lemah": false, "khusus_perempuan": false, "fresh_graduate": false, "tanpa_wawancara": false },
  "req_filters": {
    "usia": "ada | tanpa | null",
    "ipk": "ada | tanpa | null",
    "tes_bahasa_inggris": "ada | tanpa | null",
    "kewarganegaraan": "ada | tanpa | null",
    "bahasa_lain": "ada | tanpa | null",
    "tes_standar": "ada | tanpa | null",
    "dokumen": "ada | tanpa | null",
    "khusus": "ada | tanpa | null"
  },
  "req_values": {
    "usia": null,
    "ipk": null,
    "tes_bahasa_inggris": null,
    "kewarganegaraan": null,
    "bahasa_lain": null,
    "tes_standar": null,
    "dokumen": null,
    "khusus": null
  },
  "query_scope": "new_search | follow_up | null",
  "exclude": { "negara": [], "benua": [], "jenjang": [], "bidang": [], "funding": null }
}

PROMPT;

$ref = new ReflectionMethod($c, 'callChatLLM');
$ref->setAccessible(true);
$raw = $ref->invoke($c, $system, "Beasiswa yang minta skor TOEFL minimal 500", false, 0.0);
echo "RAW OUTPUT:\n$raw\n\n";

$raw2 = trim($raw);
$raw2 = preg_replace('/```(?:json)?/i', '', $raw2);
$raw2 = trim(str_replace('```', '', $raw2));
echo "CLEANED:\n$raw2\n\n";
echo "JSON DECODE: " . (json_decode($raw2, true) ? "SUCCESS" : json_last_error_msg()) . "\n";
