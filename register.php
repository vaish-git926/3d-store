<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name && $email && $pass) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $hash]);
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Email already used or database error.";
        }
    } else {
        $error = "All fields are required.";
    }
}

$current = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - 3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>3D Assets Store</h1>
        <p class="tagline">Create an account to buy and download 3D assets.</p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Home</a>
        <?php if ($current): ?>
          <span class="user-greeting">Hi, <?= htmlspecialchars($current['name']) ?></span>
          <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
          <a href="login.php" class="nav-link">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="page-main">
    <section class="page-section">
      <div class="auth-card">
        <h2 class="auth-title">Create Account</h2>

        <?php if ($error): ?>
          <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
          <label>
            Name
            <input type="text" name="name" required>
          </label>

          <label>
            Email
            <input type="email" name="email" required>
          </label>

          <label>
            Password
            <input type="password" name="password" required>
          </label>

          <button type="submit">Register</button>
        </form>

        <p class="link-small" style="margin-top:10px;">
          Already have an account? <a href="login.php">Login</a>
        </p>
      </div>
    </section>
  </main>

  <script src="assets/js/main.js"></script>
</body>
</html>

