<?php
// FILE: pages/admin/employee/add-employee.php
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
require_once BASE_PATH . '/includes/queries/departments.php';

$departments = getAllDepartments();
$designations = getAllDesignations();
?>

<form id="add-employee-form" class="rounded shadow" enctype="multipart/form-data">

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
      </div>

      <div class="input-container">
        <label>First Name</label>
        <input type="text" name="first_name" placeholder="Enter first name">
      </div>

      <div class="input-container">
        <label>Initial</label>
        <input type="text" name="middle_initial" placeholder="Enter Initial">
      </div>

      <div class="input-container">
        <label>Last Name</label>
        <input type="text" name="last_name" placeholder="Enter last name">
      </div>

      <div class="input-container">
        <label>Number</label>
        <input type="text" name="phone_number" placeholder="Enter Phone Number">
      </div>

      <div class="input-container">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email address">
      </div>

      <div class="input-container">
        <label>Date of birth</label>
        <input type="date" name="birthdate">
      </div>

      <div class="input-container">
        <label>Marital Status</label>
        <select name="marital_status">
          <option disabled selected>Select status</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Divorced">Divorced</option>
          <option value="Widowed">Widowed</option>
        </select>
      </div>

      <div class="input-container">
        <label>Gender</label>
        <select name="gender">
          <option disabled selected>Select status</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div class="input-container">
        <label>Address</label>
        <input type="text" name="address" placeholder="Enter Address">
      </div>
    </div>
    
    <br>

    <div class="buttons-container">
      <button type="button" 
              class="btn-cancel btn outlineBtn"
              data-trigger="modal"
              data-title="Cancel Employee Creation"
              data-url="ajax/prompt.php?msg=Are you sure? All data will be lost. &type=danger&action=employee">
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
          <option disabled selected>Select Department</option>
          <?php foreach($departments as $dept): ?>
            <option value="<?= $dept['department_id'] ?>"><?= $dept['name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="input-container">
        <label>Designation</label>
        <select name="designation_id" id="designation_select">
          <option disabled selected>Select Department First</option>
          <?php foreach($designations as $des): ?>
            <option value="<?= $des['designation_id'] ?>" data-department="<?= $des['department_id'] ?>" style="display:none;">
              <?= $des['name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="input-container">
        <label>Salary</label>
        <input type="text" name="salary_amount" placeholder="Enter Salary Amount">
      </div>

      <div class="input-container">
        <label>Salary Type</label>
        <select name="salary_type">
          <option disabled selected>Select Type</option>
          <option value="Monthly">Monthly</option>
          <option value="Daily">Daily</option>
          <option value="Hourly">Hourly</option>
        </select>
      </div>

      <div class="input-container">
        <label>Allowance</label>
        <input type="text" name="allowance_amount" placeholder="Enter Allowance Amount">
      </div>

      <div class="input-container">
        <label>Status</label>
        <select name="employment_status">
          <option disabled selected>Select Status</option>
          <option value="Regular">Regular</option>
          <option value="Part-time">Part-time</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>

      <div class="input-container">
        <label>Degree suffixes</label>
        <select name="degree_suffix">
          <option disabled selected>Select Degree</option>
          <option value="None">None</option>
          <option value="Bachelor">Bachelor's</option>
          <option value="Masters">Master's</option>
          <option value="Doctorate">Doctorate</option>
        </select>
      </div>

      <div class="input-container">
        <label>PhilHealth Number</label>
        <input type="text" name="philhealth_no" placeholder="Enter SSS Number">
      </div>

      <div class="input-container">
        <label>Pag-ibig Number</label>
        <input type="text" name="pagibig_no" placeholder="Enter SSS Number">
      </div>

      <div class="input-container">
        <label>SSS Number</label>
        <input type="text" name="sss_no" placeholder="Enter SSS Number">
      </div>

      <div class="input-container">
        <label>Tin Number</label>
        <input type="text" name="tin_no" placeholder="Enter SSS Number">
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
      </div>

      <div class="input-container">
        <label>Resume/CV</label>
        <input type="file" name="doc_resume">
      </div>

      <div class="input-container">
        <label>Cover/Application Letter</label>
        <input type="file" name="doc_cover">
      </div>

      <div class="input-container">
        <label>Transcript of Records <span class="light-text">(TOR)</span></label>
        <input type="file" name="doc_tor">
      </div>

      <div class="input-container">
        <label>Diploma/Degree Certificate</label>
        <input type="file" name="doc_diploma">
      </div>

      <div class="input-container">
        <label>NBI/Police Clearance</label>
        <input type="file" name="doc_nbi">
      </div>

      <div class="input-container">
        <label>Medical/Barangay Clearance</label>
        <input type="file" name="doc_medical[]" multiple>
      </div>

      <div class="input-container">
        <label>Tax Identification Number <span class="light-text">(TIN)</span></label>
        <input type="file" name="doc_tin">
      </div>

      <div class="input-container">
        <label>Pag-ibig</label>
        <input type="file" name="doc_pagibig">
      </div>

      <div class="input-container">
        <label>SSS</label>
        <input type="file" name="doc_sss">
      </div>
    </div>

    <br>

    <div id="feedback" class="feedback-message"></div>

    <div class="buttons-container">
      <button type="button" class="btn-prev btn outlineBtn">Previous</button>
      <button type="submit" class="btn-cancel btn solidBtn">Add</button>
    </div>
  </div>
  <!-- 3RD STEP -->

</form>