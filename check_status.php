<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get transaction status from database
    $stmt = $conn->prepare("SELECT status FROM transactions WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    $stmt->close();
    
    if ($transaction) {
        echo json_encode([
            'status' => 'success',
            'payment_status' => $transaction['status']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Transaction not found'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}
?>