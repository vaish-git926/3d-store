<?php
require 'config.php';
use Razorpay\Api\Api;

$success = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_id  = $_POST['payment_id'];
    $order_id    = $_POST['order_id'];
    $signature   = $_POST['signature'];
    $local_order = $_POST['local_order_id'];

    $api = new Api($razorpay_key, $razorpay_secret);

    try {
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $order_id,
            'razorpay_payment_id' => $payment_id,
            'razorpay_signature' => $signature
        ]);

        // Verified — update DB
        $stmt = $pdo->prepare("UPDATE orders SET status='paid', razorpay_payment_id=? 
                               WHERE id=?");
        $stmt->execute([$payment_id, $local_order]);

        // Clear cart
        unset($_SESSION['cart']);

        $success = true;

    } catch (Exception $e) {
        $success = false;
        $errorMessage = $e->getMessage();
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment Status</title>
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div id="snow-container"></div>

<main class="page-main">
  <section class="page-section">
    <div class="payment-status-card">

      <?php if ($success): ?>
        <h2 class="payment-status-title payment-status-ok">Payment Successful ✅</h2>
        <p>Your purchase is now unlocked.</p>
        <p>You can download your 3D assets from your account.</p>
        <a href="index.php" class="btn-secondary" style="margin-top:12px;">Return to Store</a>
      <?php else: ?>
        <h2 class="payment-status-title payment-status-fail">Payment Failed ❌</h2>
        <p>Looks like something went wrong.</p>
        <p><?= htmlspecialchars($errorMessage) ?></p>
        <a href="checkout.php" class="btn-secondary" style="margin-top:12px;">Try Again</a>
      <?php endif; ?>

    </div>
  </section>
</main>

<script src="assets/js/main.js"></script>
</body>
</html>
