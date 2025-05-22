<?php
require_once 'config.php';

function getDbConnection() {
    static $mysqli = null;
    if ($mysqli === null) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_errno) {
            die("Failed to connect MySQL: " . $mysqli->connect_error);
        }
    }
    return $mysqli;
}

function getAllRegistrants() {
    $mysqli = getDbConnection();
    $res = $mysqli->query("SELECT * FROM pendaftar ORDER BY nomor_antrian ASC");
    if (!$res) {
        die("Query Error: " . $mysqli->error);
    }
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Ambil nomor antrian berikutnya global terkecil dengan status waiting
function getNextNumberGlobal() {
    $mysqli = getDbConnection();
    $res = $mysqli->query("SELECT nomor_antrian FROM pendaftar WHERE status='waiting' ORDER BY nomor_antrian ASC LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row['nomor_antrian'];
    }
    return null;
}

// Update status jadi called dan set loket pemanggil untuk nomor tertentu
function callNumber($nomor, $loket_id) {
    $mysqli = getDbConnection();
    $nomor = (int)$nomor;
    $loket_id = $mysqli->real_escape_string($loket_id);
    $mysqli->query("UPDATE pendaftar SET status='called', loket_id='$loket_id' WHERE nomor_antrian=$nomor AND status='waiting'");
    return $mysqli->affected_rows > 0;
}

function updateStatus($nomor, $status) {
    $mysqli = getDbConnection();
    $nomor = (int)$nomor;
    $status = $mysqli->real_escape_string($status);
    $mysqli->query("UPDATE pendaftar SET status='$status' WHERE nomor_antrian=$nomor");
}

function sendWhatsappMessage($number, $message) {
    $url = "<URL-WA-GATEWAY>";

    $data = [
        "api_key" => API_KEY,
        "sender" => SENDER,
        "number" => $number,
        "message" => $message,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
