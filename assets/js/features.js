import { closeModal } from './ui.js';

export const BASE_PATH = '/SmartHR';

export function toggleFilterModal() {
    const filterModal = document.getElementById('filter-modal');
    
    if (filterModal) {
        // Toggle display
        if (filterModal.style.display === 'block') {
            filterModal.style.display = 'none';
        } else {
            filterModal.style.display = 'block';
        }
    }
}

export function processAction(endpoint, id) {
    if (typeof BASE_PATH === 'undefined') {
        console.error("BASE_PATH is not defined in the global scope.");
        return; 
    }

    if (!endpoint.includes('.')) { 
        window.location.href = `${BASE_PATH}/${endpoint}`;
        return;
    }

    closeModal();

    const formData = new FormData();
    formData.append('id', id);

    fetch(`${BASE_PATH}/${endpoint}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Server Response:", data);
        
        window.location.reload(); 
    })
    .catch(err => {
        console.error("Action failed", err);
    });
}

export function initPromptListeners() {
    const cancelBtn = document.getElementById('cancelPromptBtn');
    const confirmBtn = document.getElementById('confirmPromptBtn');
    
    if (!cancelBtn || !confirmBtn) {
        console.warn("Prompt buttons not found. Modal not yet loaded?");
        return;
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            closeModal();
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            const action = this.dataset.action;
            
            processAction(action, id);
        });
    }
}

export function togglePassword(toggleButton) {
  const passwordFieldDiv = toggleButton.parentElement;
  const passwordInput = passwordFieldDiv.querySelector('input');

  const showIcon = toggleButton.querySelector('.show-icon');
  const hideIcon = toggleButton.querySelector('.hide-icon');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';

    showIcon.style.display = 'none';
    hideIcon.style.display = 'inline-block';
  } else {
    passwordInput.type = 'password';

    showIcon.style.display = 'inline-block';
    hideIcon.style.display = 'none';
  }
}


export function initMultiStepForm(formId) {
    const form = document.querySelector(formId);

    // Safety check: If the form isn't on this page, stop.
    if (!form) {
        console.warn('Form not found:', formId);
        return;
    }

    const steps = form.querySelectorAll('.form-step');
    const tabs = form.querySelectorAll('.section-tabs .tabs'); 
    const nextBtns = form.querySelectorAll('.btn-next');
    const prevBtns = form.querySelectorAll('.btn-prev');

    console.log('Multi-step form initialized:', {
        formId,
        steps: steps.length,
        tabs: tabs.length
    });

    let currentStep = 0;

    // Function to update UI (show/hide step and update tab style)
    const updateStep = () => {
        // 1. Show/Hide Steps
        steps.forEach((step, index) => {
            // Only the current step is shown
            if (index === currentStep) {
                step.style.display = 'block';
                step.style.opacity = '1';
                step.style.visibility = 'visible';
            } else {
                step.style.display = 'none';
                step.style.opacity = '0';
                step.style.visibility = 'hidden';
            }
        });

        // 2. Update Tab Styles (Highlight the active tab)
        tabs.forEach((tab, index) => {
            if (index === currentStep) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        console.log('Current step:', currentStep);
    };

    // Event: Next Button
    nextBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent any default behavior
            // Validation logic can go here
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateStep();
            }
        });
    });

    // Event: Previous Button
    prevBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent any default behavior
            if (currentStep > 0) {
                currentStep--;
                updateStep();
            }
        });
    });

    // Event Listener for Clickable Tabs
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent any default behavior
            // Set the current step to the clicked tab's index
            currentStep = index; 
            updateStep();
        });
        // Add pointer cursor to show tabs are clickable
        tab.style.cursor = 'pointer';
    });

    // Initialize the form to the first step
    updateStep();
}

export function applyEmployeeViewMode(isReadOnly) {
    // Target the form ID used for editing/viewing on the /view-employee page
    const form = document.getElementById("edit-employee-form"); 
    if (!form) return;

    // Select all inputs, selects, and textareas within the form
    const inputs = form.querySelectorAll("input, select, textarea");

    // Select all containers for form buttons (e.g., Next, Previous, Save)
    const buttonsContainer = form.querySelectorAll(".buttons-container");
    
    // The main 'Edit Profile' button should not be disabled/hidden
    const editButton = document.getElementById("editBtn");

    if (isReadOnly) {
        // Disable fields and add a visual cue class
        inputs.forEach(el => {
            el.disabled = true;
            el.classList.add("disabled-field");
        });
        
        // Hide the form navigation/action buttons (like Save/Next/Previous)
        buttonsContainer.forEach(section => section.style.display = "none");
        
        // Ensure the form itself is marked as read-only if needed (optional)
        form.classList.add("read-only-form");

    } else {
        // Enable fields and remove the visual cue class
        inputs.forEach(el => {
            el.disabled = false;
            el.classList.remove("disabled-field");
        });
        
        // Show the form navigation/action buttons
        buttonsContainer.forEach(section => section.style.display = "flex");
        
        // Remove the form read-only class (optional)
        form.classList.remove("read-only-form");
    }
    
    // Ensure the edit button itself is never disabled and is always visible
    if (editButton) {
        editButton.disabled = false;
        editButton.style.display = "block"; 
    }
}

export function setupVerticalTabs(multiStepInitFunction) {
    const verticalButtons = document.querySelectorAll('#vertical-tabs .vertical-tab-btn');
    const verticalContents = document.querySelectorAll('#main-content .vertical-content');
    const editButton = document.getElementById("editBtn"); // Get the edit button once

    const switchVerticalTab = (targetId) => {
        // 1. Deactivate all buttons and hide all content
        verticalButtons.forEach(btn => btn.classList.remove('active'));
        verticalContents.forEach(content => content.classList.add('hidden'));

        // 2. Activate the clicked button
        const activeBtn = document.querySelector(`[data-vertical-target="${targetId}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // 3. Show the target content
        const activeContent = document.getElementById(targetId);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        // 🎯 NEW LOGIC: Toggle the visibility of the Edit Button
        if (editButton) {
            if (targetId === 'profile-view') {
                // Show the edit button only on the Profile tab
                editButton.style.display = 'block'; 
            } else {
                // Hide the edit button on all other tabs
                editButton.style.display = 'none'; 
            }
        }
        
        // If switching to the Profile tab, initialize the inner multi-step form
        // NOTE: The ID should likely be '#edit-employee-form' as used in initPageScripts.
        // I've kept '#add-employee-form' as you provided, but you may need to adjust this.
        if (targetId === 'profile-view' && typeof multiStepInitFunction === 'function') {
            multiStepInitFunction('#edit-employee-form'); // Assumed fix for ID
        }
    };

    // Add event listeners to vertical buttons
    verticalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-vertical-target');
            if (target) {
                switchVerticalTab(target);
            }
        });
    });
    
    // Initialize the default vertical tab (Profile) on setup and ensure button is visible
    switchVerticalTab('profile-view');
}

export function initClock() {
    const el = document.getElementById('clock');
    if (!el) return;

    function updateClock() {
        const now = new Date();
        el.textContent = now.toLocaleTimeString();
    }

    updateClock();
    setInterval(updateClock, 1000);
}
