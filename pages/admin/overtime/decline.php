<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this employee';
?>
<div class="prompt-container">
    <p class="prompt-message">
        Are you sure you want to <strong>REJECT</strong> the overtime request for <strong><?php echo htmlspecialchars($name); ?></strong>?
    </p>

    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">Cancel</button>

        <button class="btn redBtn" 
                id="confirmOvertimeActionBtn" 
                data-id="<?php echo $id; ?>" 
                data-status="Rejected"
                data-action="update_overtime">
            Yes, Reject
        </button>
    </div>
</div>