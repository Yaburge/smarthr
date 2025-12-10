<?php
$id = $_GET['id'] ?? 0;
$name = $_GET['name'] ?? 'this holiday';
?>
<div class="prompt-container">
    <p class="prompt-message">
        Are you sure you want to <strong>DELETE</strong> the holiday <strong>"<?php echo htmlspecialchars($name); ?>"</strong>?
    </p>

    <div class="prompt-actions">
        <button class="btn outlineBtn" id="cancelPromptBtn">Cancel</button>

        <button class="btn redBtn" 
                id="confirmHolidayDeleteBtn" 
                data-id="<?php echo $id; ?>" 
                data-action="delete_holiday">
            Yes, Delete
        </button>
    </div>
</div>