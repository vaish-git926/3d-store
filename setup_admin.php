<?php
require 'config.php';

// Check if any admin already exists
$adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn();

if ($adminCount > 0) {
    die("Admin already exists. Delete this file (setup_admin.php).");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$name || !$email || !$pass) {
        $error = "All fields are required.";
    } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, is_admin) VALUES (?, ?, ?, 1)");
        try {
            $stmt->execute([$name, $email, $hash]);
            $success = "Admin created. You can now log in at login.php";
        } catch (PDOException $e) {
            $error = "DB error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Setup Admin - 3D Store</title>
</head>
<body>
  <h1>First-time Admin Setup</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <p><strong>Important:</strong> Delete this file (setup_admin.php) now.</p>
  <?php else: ?>
    <form method="post">
      <label>
        Name:<br>
        <input type="text" name="name" required>
      </label><br><br>
      <label>
        Email:<br>
        <input type="email" name="email" required>
      </label><br><br>
      <label>
        Password:<br>
        <input type="password" name="password" required>
      </label><br><br>
      <button type="submit">Create Admin</button>
    </form>
  <?php endif; ?>
</body>
</html>
