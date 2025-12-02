<div class="prompt-container">
    <p class="prompt-message"><?php echo $message; ?></p>
    
    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">No, Cancel</button>
        
        <button class="btn solidBtn" 
                id="confirmPromptBtn" 
                data-id="<?php echo $id; ?>"
                data-action="<?php echo $action; ?>">
            Yes, Proceed
        </button>
    </div>
</div>