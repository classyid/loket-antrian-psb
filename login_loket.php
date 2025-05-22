<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loket_id'])) {
    $_SESSION['loket_id'] = $_POST['loket_id'];
    header('Location: control.php');
    exit;
}

$loket_list = ['loket_1' => 'Loket 1', 'loket_2' => 'Loket 2', 'loket_3' => 'Loket 3', 'loket_4' => 'Loket 4', 'loket_5' => 'Loket 5', 'loket_6' => 'Loket 6']; // Contoh daftar loket
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Pilih Loket</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; max-width: 400px; margin: auto; }
  form { text-align: center; }
  select, button { font-size: 1.2rem; padding: 10px; margin: 10px 0; width: 100%; }
</style>
</head>
<body>
  <h2>Pilih Loket</h2>
  <form method="post">
    <select name="loket_id" required>
      <option value="">-- Pilih Loket --</option>
      <?php foreach ($loket_list as $id => $name): ?>
        <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($name) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Masuk</button>
  </form>
</body>
</html>
