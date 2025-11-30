<?php 
  // Disable error reporting for production, enable for debugging
  error_reporting(E_ALL); 
  
  $modal = $_POST['modalSend'] ?? '';
  $modalTitle = $_POST['modalTitle'] ?? 'Alert';
  $modalClass = $_POST['modalClass'] ?? '';

  // Sanitize output if necessary, or ensure input is safe
  ?>
  <div id='modal-container'>
    <div id='modal-center-content' class='<?php echo $modalClass; ?>'>
      <div id='modal-header'>
        <h3><?php echo $modalTitle; ?></h3>
        <button id='closeModal'>&times;</button>
      </div>
      <div id='modal-content'>
        <?php echo $modal; ?>
      </div>
    </div>
  </div>