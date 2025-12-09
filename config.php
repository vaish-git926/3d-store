<?php
session_start();

$host = 'localhost';
$db   = 'threed_store';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Get logged-in user
function current_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

/* ------------------------------
   RAZORPAY PAYMENT CONFIGURATION
-------------------------------- */

// Replace these with your real Razorpay API keys
$razorpay_key = "rzp_test_XXXXXXXXXXXX";     
$razorpay_secret = "XXXXXXXXXXXXXXXXXXXX";

// Load Razorpay PHP SDK
require_once __DIR__ . "/razorpay/Razorpay.php";

use Razorpay\Api\Api;

