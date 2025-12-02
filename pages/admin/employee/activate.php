<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this employee';
?>
<div class="prompt-container">
    <p class="prompt-message">Are you sure you want to activate <strong><?php echo htmlspecialchars($name); ?></strong>?</p>
    
    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">No, Cancel</button>
        
        <button class="btn solidBtn" 
                id="confirmPromptBtn" 
                data-id="<?php echo $id; ?>"
                data-action="activate_employee">
            Yes, Activate
        </button>
    </div>
</div>