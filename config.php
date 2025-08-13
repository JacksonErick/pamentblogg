<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'binarypay');

// ZenoPay API Configuration
define('ZENOPAY_API_KEY', 'aCMAzrOwV81b9Tol-0rWRSwgvooiUj3oyiDhPXgDEZFEOwWucFIghYkcFYyks-KaBjH156mLLsSgUb2L8Lu4hA');
define('ZENOPAY_API_URL', 'https://zenoapi.com/api/payments/mobile_money_tanzania');
define('ZENOPAY_STATUS_URL', 'https://zenoapi.com/api/payments/order-status');

// Email configuration
define('ADMIN_EMAIL', 'kuzamarketonline@gmail.com');
define('FROM_EMAIL', 'noreply@binarypay.com');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>