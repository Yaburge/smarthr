import { navigate } from '../assets/js/router.js';

export function handleEmployeeForms(e) {
    const form = e.target;
    const formData = new FormData(form);
    const feedback = form.querySelector('#feedback');
    
    const action = form.getAttribute('data-action');
    const endpoint = action === 'update' 
        ? 'actions/employee/update.php'
        : 'actions/employee/create.php';
    
    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(feedback) {
            // Show different message for create vs update
            if (data.success && action === 'create') {
                feedback.innerHTML = `
                    <div class="success-message">
                        <p>${data.message}</p>
                        <p><strong>Login Credentials Created:</strong></p>
                        <p>Username: <strong>${data.username}</strong></p>
                        <p>Password: <strong>${data.temporary_password}</strong></p>
                        <p><small>Please save these credentials and share them with the employee securely.</small></p>
                    </div>
                `;
            } else {
                feedback.innerHTML = data.message;
            }
            feedback.className = data.success ? 'feedback-message success' : 'feedback-message error';
        }
        
        if (data.success) {
            if (action === 'create') {
                form.reset();
                setTimeout(() => {
                    navigate('/employee'); 
                }, 5000); // Increased timeout to read credentials
            } else {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }
    })
    .catch((err) => {
        console.error('Form submission error:', err);
        if(feedback) {
            feedback.innerHTML = 'An error occurred. Please try again';
            feedback.className = 'feedback-message error';
        }
    });
}

export function initEmployeeFilters() {
    const searchInput = document.getElementById('employeeSearchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const applyBtn = document.getElementById('filter-apply-btn');
    const cancelBtn = document.getElementById('filter-cancel-btn');
    
    if (!searchInput) return;
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        const department = departmentFilter ? departmentFilter.value : '';
        const status = statusFilter ? statusFilter.value : '';
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (department) params.append('department', department);
        if (status) params.append('status', status);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/employee/filter.php?${params.toString()}`, { cache: 'no-cache' })
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('.table-container tbody');
                const resultCount = document.querySelector('.tfoot-container p');
                const paginationDiv = document.querySelector('.pagination');
                
                if (tbody) tbody.innerHTML = data.html;
                if (resultCount) resultCount.textContent = `Showing ${data.start}-${data.end} of ${data.total} result(s)`;
                if (paginationDiv) {
                    paginationDiv.innerHTML = data.pagination;
                    setupPaginationListeners(applyFilters);
                }
            })
            .catch(() => {
                console.error('Filter error');
            });
    }
    
    searchInput.addEventListener('input', () => applyFilters(1));
    
    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            applyFilters(1);
            const filterModal = document.getElementById('filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            const filterModal = document.getElementById('filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    setupPaginationListeners(applyFilters);
}

function setupPaginationListeners(callback) {
    document.querySelectorAll('.pagination button').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.disabled) {
                const page = parseInt(this.getAttribute('data-page'));
                if (page > 0) {
                    callback(page);
                }
            }
        });
    });
}

export function handleEmployeeActions() {
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-employee-btn');
        if (deleteBtn) {
            const employeeId = deleteBtn.getAttribute('data-employee-id');
            const employeeName = deleteBtn.getAttribute('data-employee-name');
            
            showConfirmModal(
                'Delete Employee',
                `Are you sure you want to delete ${employeeName}? This will also delete their user account. This action cannot be undone.`,
                'delete_employee',
                employeeId
            );
        }
        
        const deactivateBtn = e.target.closest('.deactivate-employee-btn');
        if (deactivateBtn) {
            const employeeId = deactivateBtn.getAttribute('data-employee-id');
            const employeeName = deactivateBtn.getAttribute('data-employee-name');
            
            showConfirmModal(
                'Deactivate Employee',
                `Are you sure you want to deactivate ${employeeName}? This will also disable their login access.`,
                'deactivate_employee',
                employeeId
            );
        }
        
        const activateBtn = e.target.closest('.activate-employee-btn');
        if (activateBtn) {
            const employeeId = activateBtn.getAttribute('data-employee-id');
            const employeeName = activateBtn.getAttribute('data-employee-name');
            
            showConfirmModal(
                'Activate Employee',
                `Are you sure you want to activate ${employeeName}? This will also enable their login access.`,
                'activate_employee',
                employeeId
            );
        }
    });
}

function showConfirmModal(title, message, action, employeeId) {
    const modal = document.getElementById('confirmModal');
    const modalTitle = modal.querySelector('.modal-title');
    const modalMessage = modal.querySelector('.prompt-message');
    const confirmBtn = modal.querySelector('#confirmPromptBtn');
    
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    confirmBtn.setAttribute('data-action', action);
    confirmBtn.setAttribute('data-id', employeeId);
    
    modal.style.display = 'flex';
}

export function deleteEmployee(id) {
    const formData = new FormData();
    formData.append('employee_id', id);

    fetch('actions/employee/delete.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}

export function activateEmployee(id) {
    const formData = new FormData();
    formData.append('employee_id', id);

    fetch('actions/employee/activate.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}

export function deactivateEmployee(id) {
    const formData = new FormData();
    formData.append('employee_id', id);

    fetch('actions/employee/deactivate.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}