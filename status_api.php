<?php
require_once 'functions.php';
session_start();

header('Content-Type: application/json');

$mysqli = getDbConnection();
$current_number = $_SESSION['current_number'] ?? 0;

$total_registrants = $mysqli->query("SELECT COUNT(*) FROM pendaftar")->fetch_row()[0];
$served_count = $mysqli->query("SELECT COUNT(*) FROM pendaftar WHERE status='called' OR status='done'")->fetch_row()[0];
$waiting_count = $total_registrants - $served_count;

// Ambil 5 pendaftar berikutnya (nomor dan nama) yang statusnya 'waiting' dan nomor_antrian > current_number
$result = $mysqli->query("SELECT nomor_antrian, nama FROM pendaftar WHERE nomor_antrian > $current_number AND status='waiting' ORDER BY nomor_antrian ASC LIMIT 5");
$next_registrants = [];
while ($row = $result->fetch_assoc()) {
    $next_registrants[] = $row;
}

echo json_encode([
    'current_number' => $current_number,
    'total_registrants' => $total_registrants,
    'served_count' => $served_count,
    'waiting_count' => $waiting_count,
    'next_registrants' => $next_registrants,
]);
