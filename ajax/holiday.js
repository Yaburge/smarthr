// ============================================
// FILE PATH: ajax/holiday.js
// ============================================

import { closeModal } from '../assets/js/ui.js';

export function handleHolidayCreate(e) {
    if (e.target.id === 'holidayCreateForm') {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/holiday/create.php', {
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
        .catch((err) => {
            console.error('Error:', err);
            if(feedback) {
                feedback.innerHTML = 'An error occurred. Please try again';
            }
        });
    }
}

export function handleHolidayEdit(e) {
    if (e.target.id === 'holidayEditForm') {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/holiday/update.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(feedback) feedback.innerHTML = data.message;
            
            if (data.success) {
                setTimeout(() => {
                    closeModal();
                    window.location.reload();
                }, 1000);
            }
        })
        .catch((err) => {
            console.error('Error:', err);
            if(feedback) {
                feedback.innerHTML = 'An error occurred. Please try again';
            }
        });
    }
}

export function handleHolidayDelete(btn) {
    const id = btn.getAttribute('data-id');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = 'Processing...';

    const formData = new FormData();
    formData.append('holiday_id', id);

    fetch('actions/holiday/delete.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            closeModal();
            window.location.reload(); 
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('System Error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

export function initHolidayFilters() {
    const searchInput = document.getElementById('holidaySearchInput');
    
    if (!searchInput) return;
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/holiday/filter.php?${params.toString()}`, { cache: 'no-cache' })
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('.table-container tbody');
                const resultCount = document.querySelector('.tfoot-container p');
                const paginationDiv = document.querySelector('.pagination');
                
                if (tbody) tbody.innerHTML = data.html;
                if (resultCount) resultCount.textContent = `Showing ${data.start} to ${data.end} of ${data.total} results`;
                if (paginationDiv) {
                    paginationDiv.innerHTML = data.pagination;
                    setupPaginationListeners(applyFilters);
                }
            })
            .catch((err) => {
                console.error('Filter error:', err);
            });
    }
    
    // Search on input
    searchInput.addEventListener('input', () => applyFilters(1));
    
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