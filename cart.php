<?php
require 'config.php';

$cart = $_SESSION['cart'] ?? [];

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($cart[$id]);
    $_SESSION['cart'] = $cart;
    header("Location: cart.php");
    exit;
}

$user = current_user();

if (empty($cart)) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Your Cart - 3D Assets Store</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="assets/css/main.css">
    </head>
    <body>
      <div id="snow-container"></div>

      <header class="site-header">
        <div class="header-top">
          <div class="logo">
            <h1>3D Assets Store</h1>
            <p class="tagline">Your cart</p>
          </div>
          <nav class="user-nav">
            <a href="index.php" class="nav-link">Home</a>
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
            <p>Your cart is empty.</p>
            <p class="link-small" style="margin-top:10px;">
              <a href="index.php">Back to store</a>
            </p>
          </div>
        </section>
      </main>

      <script src="assets/js/main.js"></script>
    </body>
    </html>
    <?php
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Your Cart - 3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>3D Assets Store</h1>
        <p class="tagline">Your cart</p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Home</a>
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
      <div class="page-card-wide">
        <h2>Your Cart</h2>

        <div class="admin-table-wrapper">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p):
                    $qty = $cart[$p['id']];
                    $subtotal = $p['price'] * $qty;
                    $total += $subtotal;
              ?>
              <tr>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td>₹<?= number_format($p['price'],2) ?></td>
                <td><?= $qty ?></td>
                <td>₹<?= number_format($subtotal,2) ?></td>
                <td>
                  <a href="cart.php?remove=<?= $p['id'] ?>" class="btn-link-remove">Remove</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="badge-total">
          Total: ₹<?= number_format($total, 2) ?>
        </div>

        <div class="cart-actions">
          <a href="index.php" class="btn-link">Continue shopping</a>

          <?php if ($user): ?>
            <a href="checkout.php" class="btn-primary" style="text-decoration:none;">Proceed to Checkout</a>
          <?php else: ?>
            <span class="link-small">
              You must <a href="login.php">login</a> to checkout.
            </span>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <script src="assets/js/main.js"></script>
</body>
</html>

