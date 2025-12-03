// FILE: assets/js/auth.js
import { navigate } from '../assets/js/router.js';
export function handleLogin(e) {
    e.preventDefault(); 

    const form = e.target;
    const formData = new FormData(form);

    fetch('actions/auth/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // THIS IS THE LINE YOU REQUESTED
        document.getElementById("feedback").innerHTML = data.message;
        
        // Add styling based on success/fail (optional but recommended)
        const feedbackEl = document.getElementById("feedback");
        if (data.success) {
            feedbackEl.style.color = "green";
            setTimeout(() => {
                // Navigate first
                navigate('/dashboard');
                // Then reload the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 50); // slight delay to ensure navigate() finishes
            }, 1000);
        } else {
            feedbackEl.style.color = "red";
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById("feedback").innerHTML = "An unexpected error occurred.";
    });
}

export function logout() {
    fetch('actions/auth/logout.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hard redirect to clear client state
            window.location.href = '/SmartHR/login'; 
        }
    })
    .catch(error => {
        console.error('Logout failed:', error);
        window.location.href = '/SmartHR/login';
    });
}