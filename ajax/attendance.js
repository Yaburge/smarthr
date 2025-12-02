// FILE: ajax/attendance.js
export function handleAttendanceLog(e) {
    const btn = e.target.closest('.attendance-btn');
    if (!btn) return;
    
    e.preventDefault();
    
    const type = btn.getAttribute('data-type'); // 'in' or 'out'
    const empId = btn.getAttribute('data-id'); // For admins
    
    // UI Loading
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Processing...';
    btn.disabled = true;
    
    const formData = new FormData();
    formData.append('type', type);
    if (empId) formData.append('employee_id', empId);
    
    fetch('actions/attendance/log.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message + '\nStatus: ' + data.status);
            // Reload to update the table and swap the button
            window.location.reload();
        } else {
            alert(data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert('System Error');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Auto-refresh function to check for "No Time Out" at midnight
export function initAttendanceAutoRefresh() {
    // Get Manila time
    function checkMidnightAndRefresh() {
        const now = new Date();
        
        // Convert to Manila timezone
        const manilaTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        const hours = manilaTime.getHours();
        const minutes = manilaTime.getMinutes();
        
        // Check if it's exactly 12:00 AM Manila time (with 1-minute window)
        if (hours === 0 && minutes === 0) {
            console.log('Midnight detected - checking for No Time Out statuses');
            
            // Call API to update "No Time Out" statuses
            fetch('actions/attendance/log.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'type=check_no_timeout'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    console.log('Updated ' + data.updated + ' records to No Time Out status');
                    // Refresh the page if on attendance view
                    if (window.location.pathname.includes('attendance') || 
                        window.location.pathname.includes('view-employee')) {
                        window.location.reload();
                    }
                }
            })
            .catch(err => console.error('Auto-refresh error:', err));
        }
    }
    
    // Check every minute (60000 ms)
    setInterval(checkMidnightAndRefresh, 60000);
    
    // Also check immediately on page load
    checkMidnightAndRefresh();
}

// Initialize auto-refresh when attendance page loads
if (window.location.pathname.includes('attendance') || 
    window.location.pathname.includes('view-employee')) {
    document.addEventListener('DOMContentLoaded', function() {
        initAttendanceAutoRefresh();
    });
}


export function initAttendanceFilters() {
    const searchInput = document.getElementById('attendanceSearchInput');
    const dateFilter = document.getElementById('dateFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const applyBtn = document.getElementById('attendance-filter-apply-btn');
    const cancelBtn = document.getElementById('attendance-filter-cancel-btn');
    const filterBtn = document.getElementById('attendance-filter-btn');
    
    if (!searchInput) return;
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        const date = dateFilter ? dateFilter.value : '';
        const department = departmentFilter ? departmentFilter.value : '';
        const status = statusFilter ? statusFilter.value : '';
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (date) params.append('date', date);
        if (department) params.append('department', department);
        if (status) params.append('status', status);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/attendance/filter.php?${params.toString()}`, { cache: 'no-cache' })
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
            .catch((err) => {
                console.error('Filter error:', err);
            });
    }
    
    // Search on input
    searchInput.addEventListener('input', () => applyFilters(1));
    
    // Filter button - toggle modal
    if (filterBtn) {
        filterBtn.addEventListener('click', () => {
            const filterModal = document.getElementById('attendance-filter-modal');
            if (filterModal) {
                filterModal.style.display = filterModal.style.display === 'flex' ? 'none' : 'flex';
            }
        });
    }
    
    // Apply filter button
    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            applyFilters(1);
            const filterModal = document.getElementById('attendance-filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    // Cancel filter button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            const filterModal = document.getElementById('attendance-filter-modal');
            if (filterModal) filterModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const filterModal = document.getElementById('attendance-filter-modal');
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