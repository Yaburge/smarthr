<?php
  $id = $_GET['id'] ?? 0;
  $message = $_GET['msg'] ?? 'Are you sure you want to proceed?';
  $action = $_GET['action'] ?? ''; // The file to process the logic (e.g., 'api/delete_item.php')
  $type = $_GET['type'] ?? 'danger'; // 'danger' (red) or 'success' (green)
?>

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