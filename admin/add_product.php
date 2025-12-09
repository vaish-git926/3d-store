<?php
require __DIR__ . '/../config.php';

$user = current_user();
if (!$user || !$user['is_admin']) {
    die("Access denied. Admins only.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // basic validation
    if (!$title || !$slug || !$price) {
        $error = "Title, slug and price are required.";
    } elseif (!is_numeric($price)) {
        $error = "Price must be a number.";
    } elseif (empty($_FILES['thumbnail']['name']) || empty($_FILES['file']['name'])) {
        $error = "Thumbnail and product file are required.";
    } else {
        // handle thumbnail upload
        $thumbFile = $_FILES['thumbnail'];
        $assetFile = $_FILES['file'];

        $thumbExt = pathinfo($thumbFile['name'], PATHINFO_EXTENSION);
        $assetExt = pathinfo($assetFile['name'], PATHINFO_EXTENSION);

        $thumbName = 'thumb_' . time() . '_' . mt_rand(1000,9999) . '.' . $thumbExt;
        $assetName = 'product_' . time() . '_' . mt_rand(1000,9999) . '.' . $assetExt;

        $thumbPath = 'assets/images/' . $thumbName;         // relative path stored in DB
        $assetPath = 'storage/products/' . $assetName;      // relative path stored in DB

        $thumbDisk = __DIR__ . '/../' . $thumbPath;
        $assetDisk = __DIR__ . '/../' . $assetPath;

        // create folders if missing
        @mkdir(dirname($thumbDisk), 0777, true);
        @mkdir(dirname($assetDisk), 0777, true);

        if (!move_uploaded_file($thumbFile['tmp_name'], $thumbDisk)) {
            $error = "Failed to upload thumbnail.";
        } elseif (!move_uploaded_file($assetFile['tmp_name'], $assetDisk)) {
            $error = "Failed to upload product file.";
        } else {
            // insert into DB
            $stmt = $pdo->prepare("
                INSERT INTO products (title, slug, description, price, thumbnail, file_path)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            try {
                $stmt->execute([
                    $title,
                    $slug,
                    $desc,
                    $price,
                    $thumbPath,
                    $assetPath
                ]);
                $success = "Product added successfully.";
            } catch (PDOException $e) {
                $error = "DB error: " . $e->getMessage();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Product - Admin - 3D Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="admin-body">

  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>Add New Product</h1>
        <p class="tagline">Upload a 3D image/object and its file.</p>
      </div>
      <nav class="user-nav">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="../index.php" class="nav-link">Store</a>
        <a href="../logout.php" class="nav-link">Logout</a>
      </nav>
    </div>
  </header>

  <main class="admin-layout">
    <section class="admin-section">
      <?php if ($error): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <?php if ($success): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="admin-form">
        <div class="form-row">
          <label>
            Title<br>
            <input type="text" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
          </label>
        </div>

        <div class="form-row">
          <label>
            Slug (URL friendly, e.g. spiderman-3d-01)<br>
            <input type="text" name="slug" required value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
          </label>
        </div>

        <div class="form-row">
          <label>
            Price (INR)<br>
            <input type="number" step="0.01" name="price" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
          </label>
        </div>

        <div class="form-row">
          <label>
            Description<br>
            <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </label>
        </div>

        <div class="form-row">
          <label>
            Thumbnail (image shown in store)<br>
            <input type="file" name="thumbnail" accept="image/*" required>
          </label>
        </div>

        <div class="form-row">
          <label>
            Product File (zip/mp4/3D file to deliver)<br>
            <input type="file" name="file" required>
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="primary-btn">Save Product</button>
        </div>
      </form>
    </section>
  </main>

  <script src="../assets/js/main.js"></script>
</body>
</html>
