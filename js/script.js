document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successModal = document.getElementById('successModal');
    const returnHome = document.getElementById('returnHome');
    
    let statusCheckInterval = null;
    let currentOrderId = null;

    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        buttonText.textContent = 'PROCESSING';
        loadingSpinner.classList.remove('hidden');
        payButton.disabled = true;
        
        // Get form data
        const formData = new FormData(paymentForm);
        
        // Send AJAX request
        fetch('process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'initiated') {
                // Payment initiated successfully, start checking status
                currentOrderId = data.order_id;
                buttonText.textContent = 'WAITING FOR PAYMENT';
                openNotificationTab('WE HAVE SENT A NOTIFICATION IN YOUR PHONE CHECK IT AND VARIFY THE PIN', 'initiated');
            } else {
                openNotificationTab('WE HAVE SENT A NOTIFICATION IN YOUR PHONE CHECK IT AND VARIFY THE PIN', 'initiated');
                openNotificationTab(data.message || 'Payment failed. Please try again.', 'error');
                resetForm();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            openNotificationTab('An error occurred. Please try again.', 'error');
            resetForm();
        });
    });
    
    function openNotificationTab(message, type = 'info') {
        const encodedMessage = encodeURIComponent(message);
        const encodedType = encodeURIComponent(type);
        const url = `notification.php?message=${encodedMessage}&type=${encodedType}`;
        
        // Open in new tab with specific window features for centering
        const width = 600;
        const height = 400;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        window.open(url, '_blank', `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`);
    }
    
    function startStatusCheck(orderId) {
        statusCheckInterval = setInterval(function() {
            fetch(`check_status.php?order_id=${encodeURIComponent(orderId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.payment_status === 'COMPLETED') {
                            // Payment completed successfully
                            clearInterval(statusCheckInterval);
                            statusCheckInterval = null;
                            showSuccessModal();
                        } else if (data.payment_status === 'FAILED' || data.payment_status === 'CANCELLED') {
                            // Payment failed
                            clearInterval(statusCheckInterval);
                            statusCheckInterval = null;
                            openNotificationTab('Payment was not completed. Please try again.', 'error');
                            resetForm();
                        }
                        // If status is still PENDING, continue polling
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                    // Continue polling even if there's an error
                });
        }, 3000); // Check every 3 seconds
        
        // Set a timeout to stop checking after 5 minutes
        setTimeout(function() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
                openNotificationTab('Payment timeout. Please check your transaction status or try again.', 'error');
                resetForm();
            }
        }, 300000); // 5 minutes
    }
    
    function showSuccessModal() {
        loadingSpinner.classList.add('hidden');
        buttonText.textContent = 'PAYMENT COMPLETED';
        successModal.classList.remove('hidden');
        
        // Auto redirect after 3 seconds
        setTimeout(function() {
            returnToHome();
        }, 3000);
    }
    
    function resetForm() {
        buttonText.textContent = 'PAY NOW';
        loadingSpinner.classList.add('hidden');
        payButton.disabled = false;
        currentOrderId = null;
        
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
        }
    }
    
    function returnToHome() {
        successModal.classList.add('hidden');
        paymentForm.reset();
        resetForm();
    }
    
    // Return home button
    returnHome.addEventListener('click', function() {
        returnToHome();
    });
});