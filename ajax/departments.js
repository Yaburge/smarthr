// FILE: ajax/departments.js
import { closeModal } from '../assets/js/ui.js'; 
import { navigate } from '../assets/js/router.js';

export function handleDepartmentForms(e) {
    
    // Add Department Form
    if (e.target.id === 'addDepartmentForm') {
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/department/create.php', {
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
                    navigate('/department');
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
