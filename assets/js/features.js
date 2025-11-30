import { closeModal } from './ui.js';

export const BASE_PATH = '/SmartHR';

export function toggleFilterModal() {
  const modal = document.getElementById('filter-modal');
  modal.classList.toggle('open');
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
    if (!form) return;

    const steps = form.querySelectorAll('.form-step');
    const tabs = form.querySelectorAll('.section-tabs .tabs'); 
    const nextBtns = form.querySelectorAll('.btn-next');
    const prevBtns = form.querySelectorAll('.btn-prev');
    // const cancelBtn = form.querySelector('.btn-cancel'); // Not used in this function

    let currentStep = 0;

    // Function to update UI (show/hide step and update tab style)
    const updateStep = () => {
        // 1. Show/Hide Steps
        steps.forEach((step, index) => {
            // Only the current step is shown
            step.style.display = (index === currentStep) ? 'block' : 'none';
        });

        // 2. Update Tab Styles (Highlight the active tab)
        tabs.forEach((tab, index) => {
            if (index === currentStep) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
    };

    // Event: Next Button
    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Validation logic can go here
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateStep();
            }
        });
    });

    // Event: Previous Button
    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateStep();
            }
        });
    });

    // ✨ NEW LOGIC: Event Listener for Clickable Tabs
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            // Set the current step to the clicked tab's index
            currentStep = index; 
            updateStep();
        });
    });
    // ✨ END OF NEW LOGIC

    // Initialize the form to the first step
    updateStep();
}

export function applyEmployeeViewMode(isReadOnly) {
    const form = document.getElementById("add-employee-form");
    if (!form) return;

    const buttonsContainer = form.querySelectorAll(".buttons-container");
    const inputs = form.querySelectorAll("input, select, textarea");

    if (isReadOnly) {
        inputs.forEach(el => {
            el.disabled = true;
            el.classList.add("disabled-field");
        });
        buttonsContainer.forEach(section => section.style.display = "none");
    } else {
        inputs.forEach(el => {
            el.disabled = false;
            el.classList.remove("disabled-field");
        });
        buttonsContainer.forEach(section => section.style.display = "flex"); 
    }
}

export function setupVerticalTabs(multiStepInitFunction) {
    const verticalButtons = document.querySelectorAll('#vertical-tabs .vertical-tab-btn');
    const verticalContents = document.querySelectorAll('#main-content .vertical-content');

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

        // If switching to the Profile tab, initialize the inner multi-step form
        if (targetId === 'profile-view' && typeof multiStepInitFunction === 'function') {
            multiStepInitFunction('#add-employee-form'); 
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
    
    // Initialize the default vertical tab (Profile) on setup
    switchVerticalTab('profile-view');
}