<?php
require_once 'functions.php';
header('Content-Type: application/json');

$mysqli = getDbConnection();

// Ambil semua loket unik
$result = $mysqli->query("SELECT DISTINCT loket_id FROM pendaftar WHERE loket_id IS NOT NULL");
$lokets = [];
while ($row = $result->fetch_assoc()) {
    $lokets[] = $row['loket_id'];
}

$nomor_per_loket = [];
foreach ($lokets as $loket) {
    $loket_esc = $mysqli->real_escape_string($loket);
    $res2 = $mysqli->query("SELECT MAX(nomor_antrian) AS nomor_terakhir FROM pendaftar WHERE loket_id='$loket_esc' AND status IN ('called','done')");
    $row2 = $res2->fetch_assoc();
    $nomor_per_loket[$loket] = $row2['nomor_terakhir'] ?? null;
}

// Statistik global
$total_registrants = $mysqli->query("SELECT COUNT(*) FROM pendaftar")->fetch_row()[0];
$served_count = $mysqli->query("SELECT COUNT(*) FROM pendaftar WHERE status='called' OR status='done'")->fetch_row()[0];
$waiting_count = $total_registrants - $served_count;

// Ambil 5 pendaftar berikutnya (status waiting)
$result3 = $mysqli->query("SELECT nomor_antrian, nama FROM pendaftar WHERE status='waiting' ORDER BY nomor_antrian ASC LIMIT 5");
$next_registrants = [];
while ($row = $result3->fetch_assoc()) {
    $next_registrants[] = $row;
}

echo json_encode([
    'nomor_per_loket' => $nomor_per_loket,
    'total_registrants' => $total_registrants,
    'served_count' => $served_count,
    'waiting_count' => $waiting_count,
    'next_registrants' => $next_registrants,
]);
