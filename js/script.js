document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successModal = document.getElementById('successModal');
    const returnHome = document.getElementById('returnHome');

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
            if (data.status === 'success') {
                // Show success modal
                successModal.classList.remove('hidden');
            } else {
                // Show error message
                alert(data.message || 'Payment failed. Please try again.');
                // Reset button
                buttonText.textContent = 'PAY NOW';
                loadingSpinner.classList.add('hidden');
                payButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            // Reset button
            buttonText.textContent = 'PAY NOW';
            loadingSpinner.classList.add('hidden');
            payButton.disabled = false;
        });
    });
    
    // Return home button
    returnHome.addEventListener('click', function() {
        successModal.classList.add('hidden');
        paymentForm.reset();
        buttonText.textContent = 'PAY NOW';
        loadingSpinner.classList.add('hidden');
        payButton.disabled = false;
    });
});