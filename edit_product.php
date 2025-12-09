<?php
// admin/edit_product.php
require '../config.php';

if (!current_user() || !current_user()['is_admin']) {
    die("Access denied. Admins only.");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid product ID.");
}

// Fetch existing product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $slug === '' || $price === '') {
        $error = "Title, slug and price are required.";
    } else {
        // Keep old paths by default
        $thumbPath = $product['thumbnail'];
        $filePath  = $product['file_path'];

        // Handle new thumbnail upload (optional)
        if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $thumbName = time() . '_' . basename($_FILES['thumbnail']['name']);
            $thumbRel  = 'assets/images/' . $thumbName;
            $thumbDest = '../' . $thumbRel;

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbDest)) {
                $thumbPath = $thumbRel;
            } else {
                $error = "Failed to upload new thumbnail.";
            }
        }

        // Handle new product file upload (optional, e.g. new ZIP)
        if (!$error && !empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $fileRel  = 'storage/products/' . $fileName;
            $fileDest = '../' . $fileRel;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fileDest)) {
                $filePath = $fileRel;
            } else {
                $error = "Failed to upload new product file.";
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("
                UPDATE products
                SET title = ?, slug = ?, description = ?, price = ?, thumbnail = ?, file_path = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title,
                $slug,
                $description,
                (float)$price,
                $thumbPath,
                $filePath,
                $id
            ]);

            // Refresh product data for the form
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Product updated successfully.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Product - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>Admin Panel</h1>
        <p class="tagline">Edit product #<?= htmlspecialchars($product['id']) ?></p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="../index.php" class="nav-link">Back to Store</a>
        <a href="../logout.php" class="nav-link">Logout</a>
      </nav>
    </div>
  </header>

  <main class="page-main">
    <section class="page-section">
      <div class="page-card-wide">
        <h2>Edit Product</h2>

        <?php if ($error): ?>
          <p style="color:#ff6b6b;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
          <p style="color:#4ade80;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="admin-form">
          <label class="form-label">
            Title<br>
            <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
          </label>

          <label class="form-label">
            Slug (URL friendly, e.g. vk-wp-3d)<br>
            <input type="text" name="slug" value="<?= htmlspecialchars($product['slug']) ?>" required>
          </label>

          <label class="form-label">
            Price (INR)<br>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
          </label>

          <label class="form-label">
            Description (optional)<br>
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
          </label>

          <div class="form-row">
            <div>
              <p>Current Thumbnail:</p>
              <img src="../<?= htmlspecialchars($product['thumbnail']) ?>" alt="" style="max-width:120px;border-radius:8px;">
            </div>
            <div>
              <label class="form-label">
                Replace Thumbnail (optional)<br>
                <input type="file" name="thumbnail" accept="image/*">
              </label>
            </div>
          </div>

          <div class="form-row">
            <div>
              <p>Current Product File:</p>
              <code><?= htmlspecialchars($product['file_path']) ?></code>
            </div>
            <div>
              <label class="form-label">
                Replace Product File (optional, e.g. new ZIP)<br>
                <input type="file" name="file">
              </label>
            </div>
          </div>

          <button type="submit" class="btn-primary" style="margin-top:14px;">Save Changes</button>
        </form>
      </div>
    </section>
  </main>

  <script src="../assets/js/main.js"></script>
</body>
</html>
