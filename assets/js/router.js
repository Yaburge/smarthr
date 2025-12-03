import { 
  togglePassword, 
  toggleFilterModal, 
  initMultiStepForm, 
  applyEmployeeViewMode,
  setupVerticalTabs,
  initClock
} from './features.js';
import { closeModal } from './ui.js';
import { handleLogin, logout } from '../../ajax/auth.js';
import { handleDepartmentForms, initDepartmentViewFilters } from '../../ajax/departments.js';
import { initEmployeeFilters, handleEmployeeForms, deleteEmployee, activateEmployee, deactivateEmployee } from '../../ajax/employees.js';
import { handleAttendanceLog, initAttendanceAutoRefresh, initAttendanceFilters } from '../../ajax/attendance.js';
import { handleLeaveRequest } from '../../ajax/leave.js';

const BASE_PATH = '/SmartHR';

const routes = {
  '/404': '/pages/404.php',
  '/login': '/pages/auth/login.php',
  '/dashboard': '/pages/admin/dashboard.php',
  '/employee': '/pages/admin/employee.php',
  '/add-employee': '/pages/admin/employee/add-employee.php',
  '/view-employee': '/pages/admin/employee/view-employee.php',
  '/department': '/pages/admin/departments.php',
  '/view-department': '/pages/admin/department/view.php',
  '/attendance': '/pages/admin/attendance.php',
  '/leaves': '/pages/admin/leaves.php',
  '/payroll': '/pages/admin/payroll.php',
  '/payroll-summary': '/pages/admin/payroll/summary.php',
  '/overtime': '/pages/admin/overtime.php',
  '/holiday': '/pages/admin/holiday.php',
  '/chatbot': '/pages/admin/chatbot.php',
  '/candidates': '/pages/admin/candidates.php',
};

export async function navigate(path) {
  // Split path and query string
  const [cleanPath, queryString] = path.split('?');
  let basePath = cleanPath;
  
  const baseRegex = new RegExp(`^${BASE_PATH}`, 'i'); 

  if (baseRegex.test(basePath)) {
    basePath = basePath.replace(baseRegex, '');
  }

  if (basePath === '' || basePath === '/') {
    basePath = '/home';
  }

  const target = document.getElementById('display');
  target.innerHTML = `<p>Loading...</p>`;
  
  const file = routes[basePath] || routes['/404'];
  
  // Build fetch path with query string
  let fetchPath = `${BASE_PATH}${file}`;
  if (queryString) {
    fetchPath += `?${queryString}`;
  }

  try {
    const res = await fetch(fetchPath, { cache: 'no-cache' });
    if (!res.ok) throw new Error('Failed to load');
    
    // LOAD HTML FIRST
    target.innerHTML = await res.text();

    // THEN INITIALIZE - Use setTimeout to ensure DOM is ready
    setTimeout(() => {
      initPageScripts(basePath);
    }, 100);

    // Update Browser URL - include query string
    let browserPath = `${BASE_PATH}${basePath}`;
    if (queryString) {
      browserPath += `?${queryString}`;
    }
    if (window.location.pathname + window.location.search !== browserPath) {
      history.pushState({ path: basePath, query: queryString }, '', browserPath);
    }

  } catch (err) {
    target.innerHTML = `<p>Error: ${err.message}</p>`;
  }

  // UI UPDATES
  const HIDE_LAYOUT = ['/login'];
  const shouldHideLayout = HIDE_LAYOUT.includes(basePath);

  toggleLayout(shouldHideLayout);

  if (!shouldHideLayout) {
      updateActiveSidebar(basePath);
  }
}

// CENTRALIZED PAGE INITIALIZATION
function initPageScripts(basePath) {
    console.log('Initializing scripts for:', basePath);

    if (basePath === '/login') {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', handleLogin);
        }
    }

    if (basePath === '/add-employee') {
        // Initialize multi-step form for ADD mode
        const addForm = document.querySelector('#add-employee-form');
        if (addForm) {
            console.log('Initializing add-employee-form');
            initMultiStepForm('#add-employee-form');
        }

        // Department/Designation filter
        const deptSelect = document.getElementById('department_select');
        const desSelect = document.getElementById('designation_select');
        
        if (deptSelect && desSelect) {
            deptSelect.addEventListener('change', function() {
                const deptId = this.value;
                const options = desSelect.querySelectorAll('option');
                
                options.forEach(opt => {
                    if (opt.dataset.department === deptId) {
                        opt.style.display = 'block';
                    } else if (opt.dataset.department) {
                        opt.style.display = 'none';
                    }
                });
                
                desSelect.value = '';
                desSelect.querySelector('option').textContent = 'Select Designation';
            });
        }
    }

    if (basePath === '/view-employee') {
        // Initialize multi-step form for EDIT mode
        const editForm = document.querySelector('#edit-employee-form');
        if (editForm) {
            console.log('Initializing edit-employee-form');
            initMultiStepForm('#edit-employee-form');
        }

        // Apply view mode and setup vertical tabs
        try { 
            applyEmployeeViewMode(true); // <-- This sets the initial disabled (view) state
            setupVerticalTabs(initMultiStepForm);

            let isEditing = false; 
            const editButton = document.getElementById("editBtn");

            if (editButton) {
                editButton.textContent = "Edit Profile"; 
                editButton.addEventListener("click", () => {
                    if (isEditing) {
                        applyEmployeeViewMode(true); // <-- Click to set disabled (view) state
                        editButton.textContent = "Edit Profile";
                        isEditing = false;
                    } else {
                        applyEmployeeViewMode(false); // <-- Click to set enabled (edit) state
                        editButton.textContent = "Cancel Edit"; 
                        isEditing = true;
                    }
                });
            }
        } catch (err) { 
            console.warn('view-employee error', err); 
        }

        // Department/Designation filter for edit form
        const deptSelect = document.getElementById('department_select');
        const desSelect = document.getElementById('designation_select');
        
        if (deptSelect && desSelect) {
            deptSelect.addEventListener('change', function() {
                const deptId = this.value;
                const options = desSelect.querySelectorAll('option');
                
                options.forEach(opt => {
                    if (opt.dataset.department === deptId) {
                        opt.style.display = 'block';
                    } else if (opt.dataset.department) {
                        opt.style.display = 'none';
                    }
                });
                
                if (desSelect.value && desSelect.querySelector(`option[value="${desSelect.value}"]`)?.style.display === 'none') {
                    desSelect.value = '';
                }
            });
        }
    }

    if (basePath === '/employee') {
        initEmployeeFilters();
    }

    if (basePath === '/view-department') {
        initDepartmentViewFilters();
    }

    // Initialize attendance auto-refresh on attendance or employee view pages
    if (basePath === '/attendance' || basePath === '/view-employee') {
        initAttendanceAutoRefresh();
    }

    if (basePath === '/attendance') {
        initAttendanceFilters();
    }

    if (basePath === '/dashboard') {
        initClock();
    }

}

// HELPER FUNCTIONS
function toggleLayout(hide) {
    const nav = document.getElementById('navigation');
    const sidebar = document.getElementById('side-nav');

    if (hide) {
        if(nav) nav.style.display = 'none';
        if(sidebar) sidebar.style.display = 'none';
    } else {
        if(nav) nav.style.display = 'block';
        if(sidebar) sidebar.style.display = 'block';
    }
}

function updateActiveSidebar(currentPath) {
    const sidebarItems = document.querySelectorAll('#admin-sidebar li[data-route]');
    
    sidebarItems.forEach(item => {
        const route = item.getAttribute('data-route');
        
        if (currentPath === route || (route !== '/home' && currentPath.startsWith(route))) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// BROWSER NAVIGATION - updated to preserve query strings
window.addEventListener('popstate', (e) => {
  const path = e.state?.path || '/home';
  const query = e.state?.query || '';
  navigate(query ? `${path}?${query}` : path);
});

function handleInitialLoad() {
  // Get full path including query string
  let initial = window.location.pathname.replace(BASE_PATH, '') || '/home';
  if (window.location.search) {
    initial += window.location.search;
  }
  navigate(initial);
}

handleInitialLoad();

// GLOBAL EVENT LISTENERS
document.addEventListener('click', function (e) {
  if (e.target.matches('#filter-btn')) {
    toggleFilterModal();
  }

  if (e.target.closest('#cancelPromptBtn')) {
    closeModal();
  }

  const toggleBtn = e.target.closest('.passwordField span');
  if (toggleBtn) {
    togglePassword(toggleBtn);
  }

  const confirmBtn = e.target.closest('#confirmPromptBtn');
    if (confirmBtn) {
        const action = confirmBtn.getAttribute('data-action');
        const id = confirmBtn.getAttribute('data-id');

        if (action === 'logout') {
            logout();
            closeModal();
        }
        
        if (action === 'delete_employee') {
            deleteEmployee(id);
            closeModal();
        }

        if (action === 'activate_employee') {
            activateEmployee(id);
            closeModal();
        }

        if (action === 'deactivate_employee') {
            deactivateEmployee(id);
            closeModal();
        }
    }
    
  if (e.target.closest('.attendance-btn')) {
        handleAttendanceLog(e);
    }

  const logoutBtn = e.target.closest('#logoutBtn'); 
  if (logoutBtn) {
    e.preventDefault();
    logout();
  }
});

document.addEventListener('submit', function (e) {
  if (e.target.id === 'addDepartmentForm') {
    e.preventDefault();
    handleDepartmentForms(e);
  }
  
  // Handle both add and edit employee forms
  if (e.target.id === 'add-employee-form' || e.target.id === 'edit-employee-form') {
      e.preventDefault(); 
      handleEmployeeForms(e);
  }

  if (e.target.id === 'leaveRequestForm') {
      e.preventDefault();
      handleLeaveRequest(e);
  }
});