import { navigate } from '../assets/js/router.js';

export function handleEmployeeForms(e) {
    
    if (e.target.id === 'add-employee-form') {
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/employee/create.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(feedback) feedback.innerHTML = data.message;
            
            if (data.success) {
                e.target.reset();
                
                setTimeout(() => {
                    navigate('/employee'); 
                }, 1000);
            }
        })
        .catch(() => {
            if(feedback) feedback.innerHTML = 'An error occurred. Please try again';
        });
    }
}

// Initialize employee filters
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
    
    // Search on input
    searchInput.addEventListener('input', () => applyFilters(1));
    
    // Apply filter button
    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            applyFilters(1);
            // Close filter modal
            const filterModal = document.getElementById('filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    // Cancel filter button
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