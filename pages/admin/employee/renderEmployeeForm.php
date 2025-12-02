<?php
// FILE: pages/admin/employee/renderEmployeeForm.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
}
require_once BASE_PATH . '/includes/queries/departments.php';

// Determine if we're in "edit mode" or "add mode"
$isEditMode = isset($employee) && !empty($employee);
$formId = $isEditMode ? 'edit-employee-form' : 'add-employee-form';
$formAction = $isEditMode ? 'update' : 'create';

// Pre-fill data if editing
$formData = $isEditMode ? $employee : [
    'first_name' => '',
    'middle_initial' => '',
    'last_name' => '',
    'phone_number' => '',
    'email' => '',
    'birthdate' => '',
    'marital_status' => '',
    'gender' => '',
    'address' => '',
    'department_id' => '',
    'designation_id' => '',
    'salary_amount' => '',
    'salary_type' => '',
    'allowance_amount' => '',
    'employment_status' => '',
    'degree_suffix' => '',
    'philhealth_no' => '',
    'pagibig_no' => '',
    'sss_no' => '',
    'tin_no' => '',
    'profile_picture' => 'default_avatar.jpg'
];

$departments = getAllDepartments();
$designations = getAllDesignations();
?>

<form id="<?php echo $formId; ?>" class="rounded shadow" enctype="multipart/form-data" data-action="<?php echo $formAction; ?>">
  
  <?php if ($isEditMode): ?>
    <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
  <?php endif; ?>

  <div class="section-tabs">
    <p class="tabs row-fixed gap-20 align-center"><i class="fa-solid fa-user"></i> Personal Information</p>
    <p class="tabs row-fixed gap-20 align-center"><i class="fa-solid fa-briefcase"></i> Professional Information</p>
    <p class="tabs row-fixed gap-20 align-center"><i class="fa-solid fa-file-lines"></i> Documents</p>
  </div>

  <!-- 1ST STEP -->
  <div class="form-step">
    <div class="grid grid-2">
      <div class="input-container">
        <label>Picture</label>
        <input type="file" name="profile_pic" accept="image/png, image/jpeg, image/jpg">
        <?php if ($isEditMode): ?>
          <small class="light-text">Current: <?php echo htmlspecialchars($formData['profile_picture']); ?></small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>First Name</label>
        <input type="text" name="first_name" placeholder="Enter first name" value="<?php echo htmlspecialchars($formData['first_name']); ?>">
      </div>

      <div class="input-container">
        <label>Initial</label>
        <input type="text" name="middle_initial" placeholder="Enter Initial" value="<?php echo htmlspecialchars($formData['middle_initial']); ?>">
      </div>

      <div class="input-container">
        <label>Last Name</label>
        <input type="text" name="last_name" placeholder="Enter last name" value="<?php echo htmlspecialchars($formData['last_name']); ?>">
      </div>

      <div class="input-container">
        <label>Number</label>
        <input type="text" name="phone_number" placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($formData['phone_number']); ?>">
      </div>

      <div class="input-container">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email address" value="<?php echo htmlspecialchars($formData['email']); ?>">
      </div>

      <div class="input-container">
        <label>Date of birth</label>
        <input type="date" name="birthdate" value="<?php echo htmlspecialchars($formData['birthdate']); ?>">
      </div>

      <div class="input-container">
        <label>Marital Status</label>
        <select name="marital_status">
          <option disabled <?php echo empty($formData['marital_status']) ? 'selected' : ''; ?>>Select status</option>
          <option value="Single" <?php echo $formData['marital_status'] === 'Single' ? 'selected' : ''; ?>>Single</option>
          <option value="Married" <?php echo $formData['marital_status'] === 'Married' ? 'selected' : ''; ?>>Married</option>
          <option value="Divorced" <?php echo $formData['marital_status'] === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
          <option value="Widowed" <?php echo $formData['marital_status'] === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
        </select>
      </div>

      <div class="input-container">
        <label>Gender</label>
        <select name="gender">
          <option disabled <?php echo empty($formData['gender']) ? 'selected' : ''; ?>>Select status</option>
          <option value="Male" <?php echo $formData['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
          <option value="Female" <?php echo $formData['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
        </select>
      </div>

      <div class="input-container">
        <label>Address</label>
        <input type="text" name="address" placeholder="Enter Address" value="<?php echo htmlspecialchars($formData['address']); ?>">
      </div>
    </div>
    
    <br>

    <div class="buttons-container">
      <button type="button" 
              class="btn-cancel btn outlineBtn"
              data-trigger="modal"
              data-title="Cancel Employee <?php echo $isEditMode ? 'Update' : 'Creation'; ?>"
              data-url="ajax/prompt.php?msg=Are you sure? All data will be lost.&type=danger&action=employee">
          Cancel
      </button>
      <button type="button" class="btn-next btn solidBtn">Next</button>
    </div>
  </div>
  <!-- 1ST STEP -->

  <!-- 2ND STEP -->
  <div class="form-step">
    <div class="grid grid-2">
      <div class="input-container">
        <label>Department</label>
        <select name="department_id" id="department_select">
          <option disabled <?php echo empty($formData['department_id']) ? 'selected' : ''; ?>>Select Department</option>
          <?php foreach($departments as $dept): ?>
            <option value="<?= $dept['department_id'] ?>" 
                    <?php echo $formData['department_id'] == $dept['department_id'] ? 'selected' : ''; ?>>
              <?= htmlspecialchars($dept['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="input-container">
        <label>Designation</label>
        <select name="designation_id" id="designation_select">
          <option disabled <?php echo empty($formData['designation_id']) ? 'selected' : ''; ?>>
            <?php echo empty($formData['department_id']) ? 'Select Department First' : 'Select Designation'; ?>
          </option>
          <?php foreach($designations as $des): ?>
            <option value="<?= $des['designation_id'] ?>" 
                    data-department="<?= $des['department_id'] ?>" 
                    style="display:<?php echo ($isEditMode && $des['department_id'] == $formData['department_id']) ? 'block' : 'none'; ?>;"
                    <?php echo $formData['designation_id'] == $des['designation_id'] ? 'selected' : ''; ?>>
              <?= htmlspecialchars($des['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="input-container">
        <label>Salary</label>
        <input type="text" name="salary_amount" placeholder="Enter Salary Amount" value="<?php echo htmlspecialchars($formData['salary_amount']); ?>">
      </div>

      <div class="input-container">
        <label>Salary Type</label>
        <select name="salary_type">
          <option disabled <?php echo empty($formData['salary_type']) ? 'selected' : ''; ?>>Select Type</option>
          <option value="Monthly" <?php echo $formData['salary_type'] === 'Monthly' ? 'selected' : ''; ?>>Monthly</option>
          <option value="Daily" <?php echo $formData['salary_type'] === 'Daily' ? 'selected' : ''; ?>>Daily</option>
          <option value="Hourly" <?php echo $formData['salary_type'] === 'Hourly' ? 'selected' : ''; ?>>Hourly</option>
        </select>
      </div>

      <div class="input-container">
        <label>Allowance</label>
        <input type="text" name="allowance_amount" placeholder="Enter Allowance Amount" value="<?php echo htmlspecialchars($formData['allowance_amount']); ?>">
      </div>

      <div class="input-container">
        <label>Status</label>
        <select name="employment_status">
          <option disabled <?php echo empty($formData['employment_status']) ? 'selected' : ''; ?>>Select Status</option>
          <option value="Regular" <?php echo $formData['employment_status'] === 'Regular' ? 'selected' : ''; ?>>Regular</option>
          <option value="Part-time" <?php echo $formData['employment_status'] === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
          <option value="Inactive" <?php echo $formData['employment_status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
        </select>
      </div>

      <div class="input-container">
        <label>Degree suffixes</label>
        <select name="degree_suffix">
          <option disabled <?php echo empty($formData['degree_suffix']) ? 'selected' : ''; ?>>Select Degree</option>
          <option value="None" <?php echo $formData['degree_suffix'] === 'None' ? 'selected' : ''; ?>>None</option>
          <option value="Bachelor" <?php echo $formData['degree_suffix'] === 'Bachelor' ? 'selected' : ''; ?>>Bachelor's</option>
          <option value="Masters" <?php echo $formData['degree_suffix'] === 'Masters' ? 'selected' : ''; ?>>Master's</option>
          <option value="Doctorate" <?php echo $formData['degree_suffix'] === 'Doctorate' ? 'selected' : ''; ?>>Doctorate</option>
        </select>
      </div>

      <div class="input-container">
        <label>PhilHealth Number</label>
        <input type="text" name="philhealth_no" placeholder="Enter PhilHealth Number" value="<?php echo htmlspecialchars($formData['philhealth_no']); ?>">
      </div>

      <div class="input-container">
        <label>Pag-ibig Number</label>
        <input type="text" name="pagibig_no" placeholder="Enter Pag-ibig Number" value="<?php echo htmlspecialchars($formData['pagibig_no']); ?>">
      </div>

      <div class="input-container">
        <label>SSS Number</label>
        <input type="text" name="sss_no" placeholder="Enter SSS Number" value="<?php echo htmlspecialchars($formData['sss_no']); ?>">
      </div>

      <div class="input-container">
        <label>Tin Number</label>
        <input type="text" name="tin_no" placeholder="Enter TIN Number" value="<?php echo htmlspecialchars($formData['tin_no']); ?>">
      </div>
    </div>

    <br>

    <div class="buttons-container">
      <button type="button" class="btn-prev btn outlineBtn">Previous</button>
      <button type="button" class="btn-next btn solidBtn">Next</button>
    </div>
  </div>
  <!-- 2ND STEP -->

  <!-- 3RD STEP -->
  <div class="form-step">
    <div class="grid grid-2">
      <div class="input-container">
        <label>ID's <span class="light-text">(Photocopy)</span></label>
        <input type="file" name="doc_ids[]" multiple>
        <?php if ($isEditMode): ?>
          <small class="light-text">
            <?php if (isset($formData['docs_by_type']['ID'])): ?>
              <br>Current files: 
              <?php foreach($formData['docs_by_type']['ID'] as $doc): ?>
                <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($doc['file_path']); ?></a>
              <?php endforeach; ?>
            <?php endif; ?>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Resume/CV</label>
        <input type="file" name="doc_resume">
        <?php if ($isEditMode && isset($formData['docs_by_type']['Resume'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['Resume'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['Resume'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Cover/Application Letter</label>
        <input type="file" name="doc_cover">
        <?php if ($isEditMode && isset($formData['docs_by_type']['Cover Letter'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['Cover Letter'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['Cover Letter'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Transcript of Records <span class="light-text">(TOR)</span></label>
        <input type="file" name="doc_tor">
        <?php if ($isEditMode && isset($formData['docs_by_type']['TOR'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['TOR'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['TOR'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Diploma/Degree Certificate</label>
        <input type="file" name="doc_diploma">
        <?php if ($isEditMode && isset($formData['docs_by_type']['Diploma'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['Diploma'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['Diploma'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>NBI/Police Clearance</label>
        <input type="file" name="doc_nbi">
        <?php if ($isEditMode && isset($formData['docs_by_type']['NBI'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['NBI'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['NBI'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Medical/Barangay Clearance</label>
        <input type="file" name="doc_medical[]" multiple>
        <?php if ($isEditMode): ?>
          <small class="light-text">
            <?php if (isset($formData['docs_by_type']['Medical'])): ?>
              <br>Current files: 
              <?php foreach($formData['docs_by_type']['Medical'] as $doc): ?>
                <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($doc['file_path']); ?></a>
              <?php endforeach; ?>
            <?php endif; ?>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Tax Identification Number <span class="light-text">(TIN)</span></label>
        <input type="file" name="doc_tin">
        <?php if ($isEditMode && isset($formData['docs_by_type']['TIN'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['TIN'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['TIN'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>Pag-ibig</label>
        <input type="file" name="doc_pagibig">
        <?php if ($isEditMode && isset($formData['docs_by_type']['Pagibig'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['Pagibig'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['Pagibig'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>

      <div class="input-container">
        <label>SSS</label>
        <input type="file" name="doc_sss">
        <?php if ($isEditMode && isset($formData['docs_by_type']['SSS'][0])): ?>
          <small class="light-text">
            Current: <a href="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($formData['docs_by_type']['SSS'][0]['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($formData['docs_by_type']['SSS'][0]['file_path']); ?></a>
          </small>
        <?php endif; ?>
      </div>
    </div>

    <br>

    <div id="feedback" class="feedback-message"></div>

    <div class="buttons-container">
      <button type="button" class="btn-prev btn outlineBtn">Previous</button>
      <button type="submit" class="btn-cancel btn solidBtn">
        <?php echo $isEditMode ? 'Update' : 'Add'; ?>
      </button>
    </div>
  </div>
  <!-- 3RD STEP -->

</form>