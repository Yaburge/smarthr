// ============================================
// FILE PATH: ajax/leave.js
// ============================================

import { closeModal } from '../assets/js/ui.js';

export function handleLeaveRequest(e) {
    if (e.target.id === 'leaveRequestForm') {
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/leave/request.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(feedback) feedback.innerHTML = data.message;
            
            if (data.success) {
                e.target.reset();
                
                setTimeout(() => {
                    closeModal();
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(() => {
            if(feedback) {
                feedback.innerHTML = 'An error occurred. Please try again';
            }
        });
    }
}