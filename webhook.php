<?php
include 'config.php';

// Get the raw POST data
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if ($data && isset($data['order_id'])) {
    // Verify the payment status with ZenoPay
    $order_id = $data['order_id'];
    
    $ch = curl_init(ZENOPAY_STATUS_URL . '?order_id=' . urlencode($order_id));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . ZENOPAY_API_KEY
    ]);
    
    $response = curl_exec($ch);
    $status_data = json_decode($response, true);
    curl_close($ch);
    
    if (isset($status_data['data'][0]['payment_status'])) {
        $payment_status = $status_data['data'][0]['payment_status'];
        
        // Update transaction in database
        $stmt = $conn->prepare("UPDATE transactions SET status = ? WHERE order_id = ?");
        $stmt->bind_param("ss", $payment_status, $order_id);
        $stmt->execute();
        $stmt->close();
        
        // If payment is completed, send confirmation email
        if ($payment_status == 'COMPLETED') {
            // Get transaction details
            $stmt = $conn->prepare("SELECT category, name, email, phone, amount FROM transactions WHERE order_id = ?");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $transaction = $result->fetch_assoc();
            $stmt->close();
            
            if ($transaction) {
                // Send confirmation email to admin
                $subject = "Payment Confirmed - Order #" . $order_id;
                $message = "
                    <h2>Payment Confirmed</h2>
                    <p><strong>Category:</strong> " . $transaction['category'] . "</p>
                    <p><strong>Name:</strong> " . $transaction['name'] . "</p>
                    <p><strong>Email:</strong> " . $transaction['email'] . "</p>
                    <p><strong>Phone:</strong> " . $transaction['phone'] . "</p>
                    <p><strong>Amount:</strong> " . $transaction['amount'] . " TZS</p>
                    <p><strong>Order ID:</strong> " . $order_id . "</p>
                    <p><strong>Status:</strong> COMPLETED</p>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: BINARY PAY <" . FROM_EMAIL . ">" . "\r\n";
                
                mail(ADMIN_EMAIL, $subject, $message, $headers);
            }
        }
        
        http_response_code(200);
        echo "Webhook received and processed";
        exit();
    }
}

http_response_code(400);
echo "Invalid webhook data";
?>