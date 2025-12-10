<?php
// FILE: pages/admin/auth/logout.php
// Purpose: Dedicated Logout Confirmation Modal UI
?>

<div class="prompt-container">
    <p class="prompt-message">
        Are you sure you want to log out? <br>
        <span class="text-sm gray-text" style="font-size: 0.9rem; font-weight: normal;">You will be returned to the login screen.</span>
    </p>
    
    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">No, Cancel</button>
        
        <button class="btn redBtn" 
                id="confirmPromptBtn" 
                data-action="logout">
            Yes, Log Out
        </button>
    </div>
</div>