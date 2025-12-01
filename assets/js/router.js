import { 
  togglePassword, 
  toggleFilterModal, 
  initMultiStepForm, 
  applyEmployeeViewMode,
  setupVerticalTabs  
} from './features.js';
// At the top of assets/js/router.js
import { closeModal } from './ui.js';
import { handleLogin, logout } from '../../ajax/auth.js';
import { handleDepartmentForms, loadDepartments } from '../../ajax/departments.js';

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
};

export async function navigate(path) {
  let cleanPath = path;
  const baseRegex = new RegExp(`^${BASE_PATH}`, 'i'); 

  if (baseRegex.test(cleanPath)) {
    cleanPath = cleanPath.replace(baseRegex, '');
  }

  if (cleanPath === '' || cleanPath === '/') {
    cleanPath = '/home';
  }

  

  const target = document.getElementById('display');
  target.innerHTML = `<p>Loading...</p>`;
  
  const file = routes[cleanPath] || routes['/404'];
  const fetchPath = `${BASE_PATH}${file}`;

  try {
    const res = await fetch(fetchPath, { cache: 'no-cache' });
    if (!res.ok) throw new Error('Failed to load');
    target.innerHTML = await res.text();

    // --- PAGE SPECIFIC INIT LOGIC ---

    if (cleanPath === '/login') {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            // 2. Attach the listener
            loginForm.addEventListener('submit', handleLogin);
        }
    }

    if (cleanPath === '/department') {
        loadDepartments();
    }

    if (['/add-employee','/view-employee'].includes(cleanPath)) {
      try { 
          initMultiStepForm('#add-employee-form'); 
      } catch (err) { 
          console.warn('initMultiStepForm error', err); 
      }
    }

    if (['/view-employee'].includes(cleanPath)) {
       // ... (Keep your existing view-employee logic here) ...
       try { 
        applyEmployeeViewMode(true); 
        setupVerticalTabs(initMultiStepForm);

        let isEditing = false; 
        const editButton = document.getElementById("editBtn");

        if (editButton) {
          editButton.textContent = "Edit Profile"; 
          editButton.addEventListener("click", () => {
            if (isEditing) {
              applyEmployeeViewMode(true); 
              editButton.textContent = "Edit Profile";
              isEditing = false;
            } else {
              applyEmployeeViewMode(false); 
              editButton.textContent = "Cancel Edit"; 
              isEditing = true;
            }
          });
        }
      } catch (err) { 
        console.warn('view-employee error', err); 
      }
    }

    // Update Browser URL
    const browserPath = `${BASE_PATH}${cleanPath}`;
    if (window.location.pathname !== browserPath) {
      history.pushState({ path: cleanPath }, '', browserPath);
    }

  } catch (err) {
    target.innerHTML = `<p>Error: ${err.message}</p>`;
  }

  // --- UI UPDATES (Replaces the old loadSidebar calls) ---
  
  const HIDE_LAYOUT = ['/login'];
  const shouldHideLayout = HIDE_LAYOUT.includes(cleanPath);

  toggleLayout(shouldHideLayout);

  if (!shouldHideLayout) {
      updateActiveSidebar(cleanPath);
  }
}

// --- HELPER FUNCTIONS ---

/**
 * Toggles the visibility of the Sidebar and Navbar
 * by adding/removing a hidden class or style.
 */
function toggleLayout(hide) {
    const nav = document.getElementById('navigation');
    const sidebar = document.getElementById('side-nav'); // Wrapper of the sidebar

    if (hide) {
        if(nav) nav.style.display = 'none';
        if(sidebar) sidebar.style.display = 'none';
    } else {
        if(nav) nav.style.display = 'block'; // or 'flex' depending on your css
        if(sidebar) sidebar.style.display = 'block';
    }
}

/**
 * Finds the sidebar list item with the matching data-route
 * and adds the 'active' class, removing it from others.
 */
function updateActiveSidebar(currentPath) {
    const sidebarItems = document.querySelectorAll('#admin-sidebar li[data-route]');
    
    sidebarItems.forEach(item => {
        const route = item.getAttribute('data-route');
        
        // Exact match or checks if the current path starts with the route 
        // (useful for sub-pages like /view-employee vs /employee)
        if (currentPath === route || (route !== '/home' && currentPath.startsWith(route))) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// back/forward handlers
window.addEventListener('popstate', (e) => {
  navigate(e.state?.path || '/home');
});

function handleInitialLoad() {
  let initial = window.location.pathname.replace(BASE_PATH, '') || '/home';
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

        // Logic for Logout
        if (action === 'logout') {
            logout();     // Call the function from auth.js
            closeModal(); // Close the modal
        }
        
        // Future Logic (e.g., Delete Department)
        // if (action === 'delete_department') { ... }
    }

  const logoutBtn = e.target.closest('#logoutBtn'); 
  if (logoutBtn) {
      e.preventDefault();
      logout();
  }

});


document.addEventListener('submit', function (e) {
  // Department Forms
  if (e.target.id === 'addDepartmentForm') {
    e.preventDefault(); // Prevent default HERE
    handleDepartmentForms(e);
  }
  

  // Add other forms here as needed
});
