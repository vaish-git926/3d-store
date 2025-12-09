<?php
require 'config.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ?");
$stmt->execute([$slug]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found");
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $product['id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    }
    header("Location: cart.php");
    exit;
}

$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($product['title']) ?> - 3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>3D Assets Store</h1>
        <p class="tagline">View 3D asset details.</p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Home</a>
        <a href="cart.php" class="nav-link">Cart</a>
        <?php if ($user): ?>
          <span class="user-greeting">Hi, <?= htmlspecialchars($user['name']) ?></span>
          <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
          <a href="login.php" class="nav-link">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="page-main">
    <section class="page-section">
      <div class="page-card">
        <div class="product-layout">
          <div class="product-image-wrap">
            <img src="<?= htmlspecialchars($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
          </div>
          <div class="product-info">
            <h1><?= htmlspecialchars($product['title']) ?></h1>

            <div class="product-price-tag">
              ₹<?= number_format($product['price'], 2) ?>
            </div>

            <p class="product-description">
              <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>

            <div class="product-actions">
              <form method="post" style="display:inline;">
                <button type="submit" class="btn-primary">Add to Cart</button>
              </form>
              <a href="cart.php" class="btn-link">Go to Cart</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="assets/js/main.js"></script>
</body>
</html>
