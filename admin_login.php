<?php
session_start();

if (isset($_POST['username'], $_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Ganti sesuai username & password yang aman (bisa pakai hashing)
    if ($user === 'admin' && $pass === 'admin123') {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

  <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-700">Login Admin</h1>

    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold" for="username">Username</label>
        <input id="username" name="username" type="text" required class="w-full border rounded px-3 py-2 focus:outline-blue-500" />
      </div>
      <div>
        <label class="block mb-1 font-semibold" for="password">Password</label>
        <input id="password" name="password" type="password" required class="w-full border rounded px-3 py-2 focus:outline-blue-500" />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">Login</button>
    </form>
  </div>

</body>
</html>
