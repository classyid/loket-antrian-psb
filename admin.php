<?php
require_once 'functions.php';
session_start();

// Simple authentication check (bisa kembangkan sesuai kebutuhan)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$mysqli = getDbConnection();
$error = '';
$success = '';

// Proses update status pendaftar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $mysqli->real_escape_string($_POST['status']);
    $allowed_status = ['waiting', 'called', 'done'];
    if (in_array($status, $allowed_status)) {
        $mysqli->query("UPDATE pendaftar SET status='$status' WHERE id=$id");
        if ($mysqli->affected_rows > 0) {
            $success = "Status pendaftar berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui status atau tidak ada perubahan.";
        }
    } else {
        $error = "Status tidak valid.";
    }
}

// Ambil data pendaftar dengan filter pencarian
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM pendaftar";
if ($search !== '') {
    $sql .= " WHERE nama LIKE '%$search%' OR whatsapp LIKE '%$search%' OR nomor_antrian LIKE '%$search%'";
}
$sql .= " ORDER BY nomor_antrian ASC";

$result = $mysqli->query($sql);
if (!$result) {
    die("Query error: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Admin - Data Pendaftar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">

  <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-blue-700">Panel Admin - Data Pendaftar</h1>

    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <form method="get" class="mb-6 flex justify-center">
      <input 
        type="text" 
        name="search" 
        placeholder="Cari nama, WA, atau nomor antrian" 
        value="<?=htmlspecialchars($search)?>" 
        class="border rounded-l px-4 py-2 w-80 focus:outline-blue-500"
      />
      <button type="submit" class="bg-blue-600 text-white px-6 rounded-r hover:bg-blue-700">Cari</button>
    </form>

    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 rounded-lg">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="px-4 py-3 text-left">Nomor Antrian</th>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">Nomor WhatsApp</th>
            <th class="px-4 py-3 text-left">Loket</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows === 0): ?>
            <tr>
              <td colspan="6" class="text-center p-6 text-gray-500 italic">Data pendaftar tidak ditemukan.</td>
            </tr>
          <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3"><?=htmlspecialchars($row['nomor_antrian'])?></td>
                <td class="px-4 py-3"><?=htmlspecialchars($row['nama'])?></td>
                <td class="px-4 py-3"><?=htmlspecialchars($row['whatsapp'])?></td>
                <td class="px-4 py-3"><?=htmlspecialchars($row['loket_id'] ?? '-')?></td>
                <td class="px-4 py-3 capitalize"><?=htmlspecialchars($row['status'])?></td>
                <td class="px-4 py-3 text-center">
                  <form method="post" class="inline-block" onsubmit="return confirm('Yakin ingin ubah status?');">
                    <input type="hidden" name="id" value="<?=htmlspecialchars($row['id'])?>" />
                    <select name="status" class="border rounded px-2 py-1" required>
                      <option value="waiting" <?=($row['status'] === 'waiting' ? 'selected' : '')?>>Waiting</option>
                      <option value="called" <?=($row['status'] === 'called' ? 'selected' : '')?>>Called</option>
                      <option value="done" <?=($row['status'] === 'done' ? 'selected' : '')?>>Done</option>
                    </select>
                    <button type="submit" name="update_status" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 ml-2">Update</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-6 text-center">
      <a href="logout_admin.php" class="text-red-600 hover:underline">Logout Admin</a>
    </div>
  </div>

</body>
</html>
