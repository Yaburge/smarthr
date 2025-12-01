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

export function loadDepartments() {
    const tbody = document.getElementById('departmentTableBody');
    const feedback = document.getElementById('tableFeedback');

    if (!tbody) return;

    fetch('actions/department/fetch_all.php')
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            tbody.innerHTML = ''; // Clear "Loading..."
            
            if (data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No departments found</td></tr>';
                return;
            }

            // Loop through data and create rows
            data.data.forEach(dept => {
                const row = `
                    <tr>
                        <td data-cell="ID">#${dept.department_id}</td>
                        <td data-cell="Name" class="bold">${dept.name}</td>
                        <td data-cell="Action">
                            <button class="tableBtn outlineBtn"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="tableBtn outlineBtn"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            if(feedback) feedback.innerHTML = data.message;
        }
    })
    .catch(() => {
        if(feedback) feedback.innerHTML = 'Error loading data';
    });
}