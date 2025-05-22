<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['loket_id'])) {
    header('Location: login_loket.php');
    exit;
}

$loket_id = $_SESSION['loket_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['next'])) {
        $nextNumber = getNextNumberGlobal();
        if ($nextNumber !== null) {
            $called = callNumber($nextNumber, $loket_id);
            if ($called) {
                $_SESSION['current_number_' . $loket_id] = $nextNumber;

                $mysqli = getDbConnection();
                $stmt = $mysqli->prepare("SELECT nama, whatsapp FROM pendaftar WHERE nomor_antrian=?");
                $stmt->bind_param("i", $nextNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                $pendaftar = $result->fetch_assoc();
                $stmt->close();

                if ($pendaftar) {
                    // Format nama loket yang lebih ramah, contoh "Loket 1"
                    $loket_display = strtoupper(str_replace('_', ' ', $loket_id));

                    $message = "Halo *" . $pendaftar['nama'] . "* 👋\n\n";
                    $message .= "Nomor antrian Anda adalah *" . $nextNumber . "*.\n";
                    $message .= "Silakan segera menuju *" . $loket_display . "* untuk pendaftaran.\n\n";
                    $message .= "Terima kasih.";

                    $result = sendWhatsappMessage($pendaftar['whatsapp'], $message);

                    // Cek response API berdasarkan 'status' bukan 'success'
                    if (isset($result['status']) && $result['status'] === true) {
                        $success = "Nomor antrian berikutnya: $nextNumber. Pesan WhatsApp berhasil dikirim ke " . $pendaftar['nama'] . ".";
                    } else {
                        $error = "Nomor antrian berikutnya: $nextNumber, tapi gagal mengirim pesan WhatsApp.<br>Response API: " . htmlspecialchars(json_encode($result));
                    }
                } else {
                    $error = "Data pendaftar tidak ditemukan.";
                }
            } else {
                $error = "Nomor antrian sudah dipanggil oleh loket lain, coba lagi.";
            }
        } else {
            $error = "Tidak ada nomor antrian berikutnya.";
        }
    } elseif (isset($_POST['reset'])) {
        $_SESSION = [];
        $mysqli = getDbConnection();
        $mysqli->query("UPDATE pendaftar SET status='waiting', loket_id=NULL");
        $success = "Sistem antrian telah direset. Nomor antrian kembali ke awal.";
    }
}

$currentNumber = $_SESSION['current_number_' . $loket_id] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Kontrol Antrian - <?= htmlspecialchars($loket_id) ?></title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; max-width: 500px; margin: auto; background: #f5f5f5; }
  h1 { color: #005e99; text-align: center; }
  button { width: 100%; padding: 15px; font-size: 1.2rem; margin: 10px 0; cursor: pointer; border: none; border-radius: 5px; }
  button.next { background-color: #28a745; color: white; }
  button.reset { background-color: #dc3545; color: white; }
  .message { margin: 20px 0; text-align: center; font-weight: bold; white-space: pre-wrap; }
  .error { color: #dc3545; }
  .success { color: #28a745; }
  a.logout { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #005e99; }
  a.logout:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>Kontrol Sistem Antrian - <?= htmlspecialchars($loket_id) ?></h1>

<p>Nomor antrian saat ini: <strong><?= htmlspecialchars($currentNumber) ?></strong></p>

<?php if ($error): ?>
    <p class="message error"><?= $error ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="message success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    <button type="submit" name="next" class="next">Panggil Nomor Berikutnya</button>
    <button type="submit" name="reset" class="reset" onclick="return confirm('Yakin ingin reset antrian?')">Reset Sistem</button>
</form>

<a href="logout.php" class="logout">Logout Loket</a>

</body>
</html>
