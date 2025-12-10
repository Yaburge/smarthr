// ============================================
// FILE PATH: ajax/overtime.js
// ============================================

import { closeModal } from '../assets/js/ui.js';

export function handleOvertimeRequest(e) {
    if (e.target.id === 'overtimeRequestForm') {
        const formData = new FormData(e.target);
        const feedback = e.target.querySelector('#feedback');

        fetch('actions/overtime/request.php', {
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

export function handleOvertimeAction(btn) {
    const id = btn.getAttribute('data-id');
    const status = btn.getAttribute('data-status'); // 'Approved' or 'Rejected'
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = 'Processing...';

    const formData = new FormData();
    formData.append('ot_id', id);
    formData.append('status', status);

    fetch('actions/overtime/update.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            closeModal();
            // Reload page to show new status
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

export function initOvertimeFilters() {
    const searchInput = document.getElementById('overtimeSearchInput');
    const statusFilter = document.getElementById('overtimeStatusFilter');
    const applyBtn = document.getElementById('overtime-filter-apply-btn');
    const cancelBtn = document.getElementById('overtime-filter-cancel-btn');
    const filterBtn = document.getElementById('overtime-filter-btn');
    
    if (!searchInput) return;
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        const status = statusFilter ? statusFilter.value : '';
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/overtime/filter.php?${params.toString()}`, { cache: 'no-cache' })
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
    
    // Filter button - toggle modal
    if (filterBtn) {
        filterBtn.addEventListener('click', () => {
            const filterModal = document.getElementById('overtime-filter-modal');
            if (filterModal) {
                filterModal.style.display = filterModal.style.display === 'flex' ? 'none' : 'flex';
            }
        });
    }
    
    // Apply filter button
    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            applyFilters(1);
            const filterModal = document.getElementById('overtime-filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    // Cancel filter button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            const filterModal = document.getElementById('overtime-filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const filterModal = document.getElementById('overtime-filter-modal');
        if (filterModal && e.target === filterModal) {
            filterModal.style.display = 'none';
        }
    });
    
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