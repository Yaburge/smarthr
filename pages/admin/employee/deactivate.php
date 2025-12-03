<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this employee';
?>
<div class="prompt-container">
    <p class="prompt-message">Are you sure you want to deactivate <strong><?php echo htmlspecialchars($name); ?></strong>? They will not be able to log in.</p>
    
    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">No, Cancel</button>
        
        <button class="btn solidBtn" 
                id="confirmPromptBtn" 
                data-id="<?php echo $id; ?>"
                data-action="deactivate_employee">
            Yes, Deactivate
        </button>
    </div>
</div>