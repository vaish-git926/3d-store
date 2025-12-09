<?php
require 'config.php';

$user = current_user();
if (!$user) {
    die("Login required.");
}

$productId = (int)($_GET['product_id'] ?? 0);

// Check if user has paid order for this product
$stmt = $pdo->prepare("
    SELECT oi.id 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.user_id = ? 
      AND o.status = 'paid'
      AND oi.product_id = ?
");
$stmt->execute([$user['id'], $productId]);
$owns = $stmt->fetch();

if (!$owns) {
    die("You don't own this product.");
}

// Get product file_path
$stmt = $pdo->prepare("SELECT file_path, title FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$file = $product['file_path'];
if (!file_exists($file)) {
    die("File not found.");
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
