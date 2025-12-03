<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this employee';
?>
<div class="prompt-container">
    <p class="prompt-message">Are you sure you want to delete <strong><?php echo htmlspecialchars($name); ?></strong>? This action cannot be undone.</p>
    
    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">No, Cancel</button>
        
        <button class="btn redBtn" 
                id="confirmPromptBtn" 
                data-id="<?php echo $id; ?>"
                data-action="delete_employee">
            Yes, Delete
        </button>
    </div>
</div>