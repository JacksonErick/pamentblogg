<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category = $_POST['category'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $order_id = uniqid('binpay_', true);

    // Prepare data for ZenoPay API
    $payload = [
        'order_id' => $order_id,
        'buyer_email' => $email,
        'buyer_name' => $name,
        'buyer_phone' => $phone,
        'amount' => $amount
    ];

    // Initialize cURL session
    $ch = curl_init(ZENOPAY_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . ZENOPAY_API_KEY
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decode the response
    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'success') {
        // Save transaction to database
        $stmt = $conn->prepare("INSERT INTO transactions (order_id, category, name, email, phone, amount, status) VALUES (?, ?, ?, ?, ?, ?, 'PENDING')");
        $stmt->bind_param("sssssd", $order_id, $category, $name, $email, $phone, $amount);
        $stmt->execute();
        $stmt->close();

        // Send email notification
        $subject = "New Payment Received - Order #" . $order_id;
        $message = "
            <h2>New Payment Received</h2>
            <p><strong>Category:</strong> $category</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Amount:</strong> $amount TZS</p>
            <p><strong>Order ID:</strong> $order_id</p>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: BINARY PAY <" . FROM_EMAIL . ">" . "\r\n";
        
        mail(ADMIN_EMAIL, $subject, $message, $headers);

        // Return success response to client
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'order_id' => $order_id,
            'message' => 'Payment initiated successfully'
        ]);
        exit();
    } else {
        // Return error response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => isset($result['message']) ? $result['message'] : 'Payment failed. Please try again.'
        ]);
        exit();
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
?>