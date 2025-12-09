<?php
require __DIR__ . '/../config.php';

$user = current_user();
if (!$user || !$user['is_admin']) {
    die("Access denied. Admins only.");
}

// basic stats
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalOrders   = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 10");
$recentProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT o.id, o.amount, o.status, o.created_at, u.name AS user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - 3D Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="admin-body">

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>Admin Dashboard</h1>
        <p class="tagline">Manage products, orders and users.</p>
      </div>
      <nav class="user-nav">
        <span class="user-greeting">
          Admin: <?= htmlspecialchars($user['name']) ?>
        </span>
        <a href="../index.php" class="nav-link">Back to Store</a>
        <a href="../logout.php" class="nav-link">Logout</a>
      </nav>
    </div>
  </header>

  <main class="admin-layout">
    <section class="admin-stats">
      <div class="admin-stat-card">
        <div class="stat-label">Products</div>
        <div class="stat-value"><?= $totalProducts ?></div>
      </div>
      <div class="admin-stat-card">
        <div class="stat-label">Users</div>
        <div class="stat-value"><?= $totalUsers ?></div>
      </div>
      <div class="admin-stat-card">
        <div class="stat-label">Orders</div>
        <div class="stat-value"><?= $totalOrders ?></div>
      </div>
    </section>

    <section class="admin-actions">
      <a href="add_product.php" class="primary-btn">+ Add New Product</a>
    </section>

    <section class="admin-section">
      <h2>Recent Products</h2>
      <?php if (empty($recentProducts)): ?>
        <p class="empty-state">No products found yet.</p>
      <?php else: ?>
        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Title</th>
                <th>Price</th>
                <th>Created</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentProducts as $p): ?>
                <tr>
                  <td><?= $p['id'] ?></td>
                  <td>
                    <?php if (!empty($p['thumbnail'])): ?>
                      <img src="../<?= htmlspecialchars($p['thumbnail']) ?>" class="admin-thumb" alt="">
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($p['title']) ?></td>
                  <td>₹<?= number_format($p['price'], 2) ?></td>
                  <td><?= htmlspecialchars($p['created_at']) ?></td>
                  <td>
                    <a href="../product.php?slug=<?= urlencode($p['slug']) ?>" class="btn-link">View</a>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-link" style="margin-left:8px;">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <section class="admin-section">
      <h2>Recent Orders</h2>
      <?php if (empty($recentOrders)): ?>
        <p class="empty-state">No orders yet.</p>
      <?php else: ?>
        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Placed</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
                <tr>
                  <td>#<?= $o['id'] ?></td>
                  <td><?= htmlspecialchars($o['user_name']) ?></td>
                  <td>₹<?= number_format($o['amount'], 2) ?></td>
                  <td><?= htmlspecialchars($o['status']) ?></td>
                  <td><?= htmlspecialchars($o['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script src="../assets/js/main.js"></script>
</body>
</html>

