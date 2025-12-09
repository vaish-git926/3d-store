<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>3D Krishna glTF Test</title>
    <style>
        body {
            margin: 0;
            background: #020924;
            overflow: hidden;
        }
        #viewer {
            width: 100vw;
            height: 100vh;
        }
    </style>
</head>
<body>

<div id="viewer"></div>

<!-- Three.js core -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- Orbit Controls -->
<script src="https://unpkg.com/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<!-- GLTF Loader -->
<script src="https://unpkg.com/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

<script>
    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
        75,
        window.innerWidth / window.innerHeight,
        0.1,
        1000
    );
    camera.position.set(0, 1, 5);

    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setClearColor(0x020924);
    renderer.outputEncoding = THREE.sRGBEncoding;
    document.getElementById('viewer').appendChild(renderer.domElement);

    // Lights
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.5);
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
    controls.enablePan = true;
    controls.autoRotate = true;
    controls.autoRotateSpeed = 1.5;

    // Load glTF model
    const loader = new THREE.GLTFLoader();
    loader.load('models/krishna/Krishna_Model.glb', function(gltf) {
        const model = gltf.scene;
        model.scale.set(1, 1, 1);     // adjust if too big/small
        model.position.set(0, -1, 0); // move slightly down
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

    window.addEventListener('resize', function() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
</script>

</body>
</html>

