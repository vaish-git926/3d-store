<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin']
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill both fields.";
    }
}

$current = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - 3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>3D Assets Store</h1>
        <p class="tagline">Login to access your 3D purchases.</p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Home</a>
        <?php if ($current): ?>
          <span class="user-greeting">Hi, <?= htmlspecialchars($current['name']) ?></span>
          <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
          <a href="register.php" class="nav-link">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="page-main">
    <section class="page-section">
      <div class="auth-card">
        <h2 class="auth-title">Login</h2>

        <?php if ($error): ?>
          <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
          <label>
            Email
            <input type="email" name="email" required>
          </label>

          <label>
            Password
            <input type="password" name="password" required>
          </label>

          <button type="submit">Login</button>
        </form>

        <p class="link-small" style="margin-top:10px;">
          New here? <a href="register.php">Create an account</a>
        </p>
      </div>
    </section>
  </main>

  <script src="assets/js/main.js"></script>
</body>
</html>
