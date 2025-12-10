import { navigate } from '../assets/js/router.js';

export function initPayrollFilters() {
    const searchInput = document.getElementById('payrollSearchInput');
    const periodSelect = document.getElementById('periodFilter');
    
    if (!searchInput) return;
    
    function applyFilters(page = 1) {
        const search = searchInput.value;
        const periodId = periodSelect ? periodSelect.value : '';
        
        if (!periodId) return;
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        params.append('period_id', periodId);
        params.append('page', page);
        
        fetch(`/SmartHR/actions/payroll/filter.php?${params.toString()}`, { cache: 'no-cache' })
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('.table-container tbody');
                const resultCount = document.querySelector('.tfoot-container p');
                const paginationDiv = document.querySelector('.pagination');
                const summaryCards = document.querySelectorAll('.payroll-summary-card');
                
                if (tbody) tbody.innerHTML = data.html;
                if (resultCount) resultCount.textContent = `Showing ${data.start}-${data.end} of ${data.total} result(s)`;
                if (paginationDiv) {
                    paginationDiv.innerHTML = data.pagination;
                    setupPaginationListeners(applyFilters);
                }
                
                // Update summary cards
                if (summaryCards.length >= 4 && data.summary) {
                    summaryCards[0].querySelector('.bold').textContent = data.summary.employee_count;
                    summaryCards[1].querySelector('.bold').textContent = `₱${Number(data.summary.total_gross).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                    summaryCards[2].querySelector('.bold').textContent = `₱${Number(data.summary.total_deductions).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                    summaryCards[3].querySelector('.bold').textContent = `₱${Number(data.summary.total_net).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                }
            })
            .catch(err => {
                console.error('Filter error:', err);
            });
    }
    
    searchInput.addEventListener('input', () => applyFilters(1));
    
    if (periodSelect) {
        periodSelect.addEventListener('change', () => applyFilters(1));
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

export function handleAddDeduction(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const feedback = document.getElementById('deduction-feedback');
    
    fetch('/SmartHR/actions/payroll/add-deduction.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (feedback) {
            feedback.innerHTML = data.message;
            feedback.className = data.success ? 'feedback-message success' : 'feedback-message error';
        }
        
        if (data.success) {
            form.reset();
            const modal = document.getElementById('filter-modal');
            if (modal) modal.style.display = 'none';
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    })
    .catch(err => {
        console.error('Add deduction error:', err);
        if (feedback) {
            feedback.innerHTML = 'An error occurred';
            feedback.className = 'feedback-message error';
        }
    });
}

export function processPayroll(payrollId) {
    const formData = new FormData();
    formData.append('payroll_id', payrollId);
    
    fetch('/SmartHR/actions/payroll/process.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error('Process payroll error:', err);
        alert('An error occurred');
    });
}

export function processAllPayroll(periodId) {
    if (!confirm('Are you sure you want to process all payroll for this period? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('period_id', periodId);
    
    fetch('/SmartHR/actions/payroll/process-all.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error('Process all error:', err);
        alert('An error occurred');
    });
}

export function exportPayslip(payrollId) {
    window.open(`/SmartHR/actions/payroll/export-payslip.php?payroll_id=${payrollId}`, '_blank');
}

export function exportAllPayroll(periodId) {
    window.open(`/SmartHR/actions/payroll/export-excel.php?period_id=${periodId}`, '_blank');
}

export function generatePayroll(periodId) {
    if (!confirm('Generate payroll for all employees in this period?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('period_id', periodId);
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
    
    fetch('/SmartHR/actions/payroll/generate.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-calculator"></i> Generate Payroll';
        }
    })
    .catch(err => {
        console.error('Generate error:', err);
        alert('An error occurred');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-calculator"></i> Generate Payroll';
    });
}