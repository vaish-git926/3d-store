<?php
require 'config.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>3D Assets Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="3D pictures and objects captured by Vaishnav – digital asset store">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

  <!-- Snow background -->
  <div id="snow-container"></div>

  <header class="site-header">
    <div class="header-top">
      <div class="logo">
        <h1>3D Assets Store</h1>
        <p class="tagline">High-quality 3D pictures & objects captured and created by me.</p>
      </div>

      <nav class="user-nav">
        <?php if ($user): ?>
          <span class="user-greeting">
            Hi, <?= htmlspecialchars($user['name']) ?>
          </span>
          <a href="cart.php" class="nav-link">Cart</a>
          <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
          <a href="login.php" class="nav-link">Login</a>
          <a href="register.php" class="nav-link">Register</a>
          <a href="cart.php" class="nav-link">Cart</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

    <main>

    <!-- Hero section with 3D MP4 on the right -->
    <section class="hero-section">
  <div class="hero-text">
    <h2>Bring 3D scenes to life</h2>
    <p>
      Explore 3D pictures and objects I’ve captured and created.
      Scroll down to see all products.
    </p>
  </div>

    <div class="hero-art">
    <div class="hero-art-glow"></div>
    <div id="hero-3d-container" style="width:100%; height:100%; min-height:300px;"></div>
  </div>
</section>

    <section class="products-section">
      <h2>Available 3D Assets</h2>

      <?php if (empty($products)): ?>
        <p class="empty-state">No products yet. Log in as admin and add some from the admin panel.</p>
      <?php else: ?>
        <div class="grid" id="gallery">
          <?php foreach ($products as $p): ?>
            <a class="card" href="product.php?slug=<?= urlencode($p['slug']) ?>"
               data-full="<?= htmlspecialchars($p['thumbnail']) ?>"
               data-title="<?= htmlspecialchars($p['title']) ?>">
              
              <img src="<?= htmlspecialchars($p['thumbnail']) ?>"
                   alt="<?= htmlspecialchars($p['title']) ?>"
                   loading="lazy">

              <div class="caption">
                <div class="product-title"><?= htmlspecialchars($p['title']) ?></div>
                <div class="product-price">₹<?= number_format($p['price'], 2) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <!-- Image Lightbox -->
  <div class="lightbox" id="lb" role="dialog" aria-hidden="true">
    <button class="close-btn" id="close">Close ✕</button>
    <img id="lbImg" alt="">
  </div>

  <!-- Three.js core -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- Orbit Controls -->
<script src="https://unpkg.com/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<!-- GLTF Loader -->
<script src="https://unpkg.com/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

<script>
  (function() {
    const container = document.getElementById('hero-3d-container');
    if (!container) return; // safety

    const width = container.clientWidth || 400;
    const height = container.clientHeight || 300;

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
      45,
      width / height,
      0.1,
      1000
    );
    camera.position.set(0, 1, 5);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(window.devicePixelRatio || 1);
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.setClearColor(0x000000, 0); // transparent background
    container.appendChild(renderer.domElement);

    // Lights
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.4);
    hemiLight.position.set(0, 20, 0);
    scene.add(hemiLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 1);
    dirLight.position.set(5, 10, 7);
    scene.add(dirLight);

    // Controls
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enableZoom = true;
    controls.enablePan = false;   // can turn on if you like
    controls.autoRotate = true;
    controls.autoRotateSpeed = 1.2;

    // Load glTF model
    const loader = new THREE.GLTFLoader();
    loader.load('models/ghatotkach.zip', function(gltf) {
      const model = gltf.scene;
      model.scale.set(1.2, 1.2, 1.2);    // tweak size
      model.position.set(0, -1.1, 0);    // move a bit down
      scene.add(model);
    }, undefined, function(error) {
      console.error('Error loading glTF:', error);
    });

    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }
    animate();

    // Resize with window
    window.addEventListener('resize', function() {
      const newWidth = container.clientWidth || 400;
      const newHeight = container.clientHeight || 300;
      camera.aspect = newWidth / newHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(newWidth, newHeight);
    });
  })();
</script>

  <script src="assets/js/main.js"></script>

</body>
</html>

