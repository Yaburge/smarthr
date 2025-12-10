<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this employee';
?>
<div class="prompt-container">
    <p class="prompt-message">
        Are you sure you want to <strong>APPROVE</strong> the leave request for <strong><?php echo htmlspecialchars($name); ?></strong>?
    </p>

    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">Cancel</button>

        <button class="btn solidBtn" 
                id="confirmLeaveActionBtn" 
                data-id="<?php echo $id; ?>" 
                data-status="Approved"
                data-action="update_leave">
            Yes, Approve
        </button>
    </div>
</div>