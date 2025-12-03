import { initPromptListeners } from './features.js';

const BASE_PATH = '/SmartHR';

// --- Sidebar Toggling Logic ---
window.closeAdminSidebar = function () {
  const sidebar = document.getElementById('admin-sidebar');
  if (sidebar && sidebar.classList.contains('open')) {
      window.toggleAdminSidebar();
  }
};

window.toggleAdminSidebar = function () {
  const sidebar = document.getElementById('admin-sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  const display = document.getElementById('display');
  const navigation = document.getElementById('navigation');

  if (!sidebar) return;

  const isOpening = !sidebar.classList.contains('open');

  sidebar.classList.toggle('open', isOpening);
  if (backdrop) backdrop.classList.toggle('open', isOpening);
  if (display) display.classList.toggle('blurred', isOpening);
  if (navigation) navigation.classList.toggle('blurred', isOpening);

  localStorage.setItem('adminSidebarOpen', isOpening);

  if (backdrop) {
      backdrop.onclick = toggleAdminSidebar;
  }
};

// Handle initial state on load
document.addEventListener('DOMContentLoaded', () => {
  const isOpen = localStorage.getItem('adminSidebarOpen') === 'true';
  const sidebar = document.getElementById('admin-sidebar');
  const display = document.getElementById('display');
  
  if (isOpen && sidebar) {
      sidebar.classList.add('open');
      if (display) display.classList.add('blurred');
      // Add backdrop logic here if needed immediately on load
  }
});


// MODAL
export function modal(title, wide = null, msg, onClose = null, onOpen = null) { 
    var modalData = new FormData();
    modalData.append('modalTitle', title);
    modalData.append('modalClass', wide);
    modalData.append('modalSend', msg);

    fetch(`${BASE_PATH}/ajax/modal.php`, {
        method: 'POST',
        body: modalData
    })
    .then(response => response.text())
    .then(mData => {
        const modalContainer = document.getElementById('modal');
        modalContainer.innerHTML = mData;
        document.body.classList.add('no-scroll');

        document.getElementById('closeModal').onclick = function () {
            closeModal(onClose); 
        };

        // --- THE CRITICAL FIX ---
        // Defer the execution of onOpen() to the next execution cycle.
        // This guarantees the browser has finished parsing the mData HTML 
        // and created the button elements before initPromptListeners runs.
        if (typeof onOpen === "function") {
            window.requestAnimationFrame(() => {
                onOpen(); // ✅ Should run correctly
            });
        }
        // --- END OF CRITICAL FIX ---
    })
    .catch(err => console.error('Modal Error:', err));
}

export function closeModal(onClose) {
  document.getElementById('modal').innerHTML = ""; 
  document.body.classList.remove('no-scroll'); 

  if (typeof onClose === "function") {
    onClose();
  }
}

// TRIGGERING MODAL
export function initGlobalModalListener() {
  document.body.addEventListener('click', function(e) {
    const trigger = e.target.closest('[data-trigger="modal"]');
    if (!trigger) return;

    e.preventDefault(); 
    const title = trigger.dataset.title || 'Alert';
    const url = trigger.dataset.url;
    const wide = trigger.dataset.wide || ''; 

    let onOpenCallback = null;
    if (url.includes('prompt.php')) {
      onOpenCallback = initPromptListeners;
    }

    fetch(`${BASE_PATH}/${url}`)
    .then(response => response.text())
    .then(data => {
      modal(title, wide, data, null, onOpenCallback); 
    })
    .catch(err => console.error('Modal load error:', err));
  });
}