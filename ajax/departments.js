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


export function initDepartmentViewFilters() {
    const searchInput = document.getElementById('deptEmployeeSearchInput');
    const designationFilter = document.getElementById('designationFilter');
    const statusFilter = document.getElementById('statusFilter');
    const applyBtn = document.getElementById('filter-apply-btn');
    const cancelBtn = document.getElementById('filter-cancel-btn');
    const filterModal = document.getElementById('filter-modal');
    
    if (!searchInput) return;
    
    // Get department ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const deptId = urlParams.get('id');
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        const designation = designationFilter ? designationFilter.value : '';
        const status = statusFilter ? statusFilter.value : '';
        
        const params = new URLSearchParams();
        params.append('dept_id', deptId);
        if (search) params.append('search', search);
        if (designation) params.append('designation', designation);
        if (status) params.append('status', status);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/employee/filter-by-dept.php?${params.toString()}`, { cache: 'no-cache' })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
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
            .catch((error) => {
                console.error('Filter error:', error);
            });
    }
    
    searchInput.addEventListener('input', () => applyFilters(1));
    
    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            applyFilters(1);
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
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