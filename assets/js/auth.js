// assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = loginForm.querySelector('input[type="submit"]');
            const originalBtnText = submitBtn.value;
            submitBtn.value = 'Signing in...';
            submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch('actions/auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success: Redirect to dashboard
                    window.location.href = 'index.php'; // or navigate('/dashboard')
                } else {
                    // Error: Show alert
                    alert(data.message);
                    submitBtn.value = originalBtnText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                submitBtn.value = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
});