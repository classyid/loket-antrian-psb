<?php
require_once 'config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

function getGoogleSheetData() {
    $url = GOOGLE_SHEET_API_URL . '?action=getData';
    $response = @file_get_contents($url);
    if ($response === false) {
        return [];
    }
    $data = json_decode($response, true);
    return $data ?: [];
}

$data = getGoogleSheetData();

if (empty($data)) {
    echo "No data from Google Sheets\n";
    exit;
}

// Urutkan berdasarkan timestamp ascending
usort($data, function($a, $b) {
    return strtotime($a['timestamp']) <=> strtotime($b['timestamp']);
});

// Mulai nomor antrian dari 1 atau cari nomor antrian max di DB
$result = $mysqli->query("SELECT MAX(nomor_antrian) AS max_no FROM pendaftar");
$row = $result->fetch_assoc();
$maxNomor = (int)$row['max_no'];

foreach ($data as $entry) {
    $nama = $mysqli->real_escape_string($entry['nama']);
    $wa = preg_replace('/\D/', '', $entry['whatsapp']);
    if (substr($wa, 0, 1) == '0') {
        $wa = '62' . substr($wa, 1);
    } elseif (substr($wa, 0, 2) != '62') {
        $wa = '62' . $wa;
    }
    $timestamp = date('Y-m-d H:i:s', strtotime($entry['timestamp']));

    // Cek apakah sudah ada data berdasarkan whatsapp dan timestamp
    $checkSql = "SELECT id FROM pendaftar WHERE whatsapp='$wa' AND timestamp='$timestamp'";
    $checkRes = $mysqli->query($checkSql);

    if ($checkRes->num_rows == 0) {
        $maxNomor++;
        $insertSql = "INSERT INTO pendaftar (nama, whatsapp, timestamp, nomor_antrian, status) 
                      VALUES ('$nama', '$wa', '$timestamp', $maxNomor, 'waiting')";
        $mysqli->query($insertSql);
    }
}

echo "Sinkronisasi selesai. Total nomor antrian terakhir: $maxNomor\n";
$mysqli->close();
