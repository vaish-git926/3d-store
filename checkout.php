<?php
require 'config.php';

$user = current_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($products as $p) {
    $qty = $cart[$p['id']];
    $total += $p['price'] * $qty;
}

// Create Razorpay Order
$api = new Api($razorpay_key, $razorpay_secret);

$razorpayOrder = $api->order->create([
    'receipt' => 'order_'.time(),
    'amount' => $total * 100,  // amount in paise
    'currency' => 'INR'
]);

$razorpay_order_id = $razorpayOrder['id'];

// Insert into orders table
$stmt = $pdo->prepare("INSERT INTO orders (user_id, razorpay_order_id, amount, status)
                       VALUES (?, ?, ?, 'pending')");
$stmt->execute([$user['id'], $razorpay_order_id, $total]);

$order_id = $pdo->lastInsertId();

// Insert into order_items table
foreach ($products as $p) {
    $qty = $cart[$p['id']];
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price)
                           VALUES (?, ?, ?)");
    $stmt->execute([$order_id, $p['id'], $p['price']]);
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Checkout - 3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<div id="snow-container"></div>

<header class="site-header">
  <div class="header-top">
    <div class="logo">
      <h1>3D Assets Store</h1>
      <p class="tagline">Checkout</p>
    </div>
    <nav class="user-nav">
      <a href="index.php" class="nav-link">Home</a>
      <a href="cart.php" class="nav-link">Cart</a>
    </nav>
  </div>
</header>

<main class="page-main">
  <section class="page-section">
    <div class="page-card-wide">

      <h2>Order Summary</h2>
      <table class="checkout-table">
        <tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        <?php foreach ($products as $p): 
          $qty = $cart[$p['id']];
          $subtotal = $qty * $p['price'];
        ?>
        <tr>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td>₹<?= $p['price'] ?></td>
          <td><?= $qty ?></td>
          <td>₹<?= $subtotal ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <div class="checkout-badge-total">
        Payable: ₹<?= number_format($total, 2) ?>
      </div>

      <div class="checkout-actions">
        <a href="cart.php" class="btn-secondary">Back to Cart</a>

        <!-- Razorpay Button -->
        <button id="rzp-button" class="btn-primary">Pay Now</button>
      </div>
    </div>
  </section>
</main>

<script>
var options = {
    "key": "<?= $razorpay_key ?>",
    "amount": "<?= $total * 100 ?>",
    "currency": "INR",
    "name": "3D Assets Store",
    "description": "Order #<?= $order_id ?>",
    "order_id": "<?= $razorpay_order_id ?>",
    "handler": function (response){
        // Submit form to verify payment
        fetch("payment_verify.php", {
          method: "POST",
          headers: {"Content-Type": "application/x-www-form-urlencoded"},
          body: new URLSearchParams({
            payment_id: response.razorpay_payment_id,
            order_id: response.razorpay_order_id,
            signature: response.razorpay_signature,
            local_order_id: "<?= $order_id ?>"
          })
        })
        .then(() => window.location.href = "payment_verify.php?success=1&id=<?= $order_id ?>");
    },
    "theme": {"color": "#4f8cff"}
};

document.getElementById('rzp-button').onclick = function(e){
    var rzp = new Razorpay(options);
    rzp.open();
    e.preventDefault();
}
</script>

<script src="assets/js/main.js"></script>
</body>
</html>
