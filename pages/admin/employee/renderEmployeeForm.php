<form id="add-employee-form" class="rounded shadow" action="#" method="POST">

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
            <input type="file" name="pic" accept="image/png, image/jpeg, image/jpg">
          </div>

          <div class="input-container">
            <label>First Name</label>
            <input type="text" name="firstname" placeholder="Enter first name">
          </div>

          <div class="input-container">
            <label>Initial</label>
            <input type="text" name="initial" placeholder="Enter Initial">
          </div>

          <div class="input-container">
            <label>Last Name</label>
            <input type="text" name="lastname" placeholder="Enter last name">
          </div>

          <div class="input-container">
            <label>Number</label>
            <input type="text" name="number" placeholder="Enter Phone Number">
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
            <select>
              <option disabled selected>Select status</option>
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="divorced">Divorced</option>
            </select>
          </div>

          <div class="input-container">
            <label>Gender</label>
            <select>
              <option disabled selected>Select status</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
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
            <select>
              <option disabled selected>Select Department</option>
              <option value="1">Department 1</option>
              <option value="2">Department 2</option>
            </select>
          </div>

          <div class="input-container">
            <label>Designation</label>
            <select>
              <option disabled selected>Select Designation</option>
              <option value="1">Designation 1</option>
              <option value="2">Designation 2</option>
            </select>
          </div>

          <div class="input-container">
            <label>Salary</label>
            <input type="text" name="address" placeholder="Enter Salary Amount">
          </div>

          <div class="input-container">
            <label>Salary Type</label>
            <select>
              <option disabled selected>Select Type</option>
              <option value="Monthly">Monthly</option>
              <option value="Daily">Daily</option>
              <option value="Hourly">Hourly</option>
            </select>
          </div>

          <div class="input-container">
            <label>Allowance</label>
            <input type="text" name="address" placeholder="Enter Allowance Amount">
          </div>

          <div class="input-container">
            <label>Status</label>
            <select>
              <option disabled selected>Select Status</option>
              <option value="Regular">Regular</option>
              <option value="Part-time">Part-time</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <div class="input-container">
            <label>Degree suffixes</label>
            <select>
              <option disabled selected>Select Degree</option>
              <option value="None">None</option>
              <option value="Bachelor">Bachelor's</option>
              <option value="Masters">Master's</option>
              <option value="Doctorate">Doctorate</option>
            </select>
          </div>

          <div class="input-container">
            <label>PhilHealth Number</label>
            <input type="text" name="address" placeholder="Enter SSS Number">
          </div>

          <div class="input-container">
            <label>Pag-ibig Number</label>
            <input type="text" name="address" placeholder="Enter SSS Number">
          </div>

          <div class="input-container">
            <label>SSS Number</label>
            <input type="text" name="address" placeholder="Enter SSS Number">
          </div>

          <div class="input-container">
            <label>Tin Number</label>
            <input type="text" name="address" placeholder="Enter SSS Number">
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
            <input type="file" name="ID[]" multiple>
          </div>

          <div class="input-container">
            <label>Resume/CV</label>
            <input type="file" name="resume">
          </div>

          <div class="input-container">
            <label>Cover/Application Letter</label>
            <input type="file" name="cover">
          </div>

          <div class="input-container">
            <label>Transcript of Records <span class="light-text">(TOR)</span></label>
            <input type="file" name="tor">
          </div>

          <div class="input-container">
            <label>Diploma/Degree Certificate</label>
            <input type="file" name="diploma">
          </div>

          <div class="input-container">
            <label>NBI/Police Clearance</label>
            <input type="file" name="nbi/police">
          </div>

          <div class="input-container">
            <label>Medical/Barangay Clearance</label>
            <input type="file" name="brgy-medical[]" multiple>
          </div>

          <div class="input-container">
            <label>Tax Identification Number <span class="light-text">(TIN)</span></label>
            <input type="file" name="tin">
          </div>

          <div class="input-container">
            <label>Pag-ibig</label>
            <input type="file" name="Pag-ibig">
          </div>

          <div class="input-container">
            <label>SSS</label>
            <input type="file" name="SSS">
          </div>
        </div>

        <br>

        <div class="buttons-container">
          <button type="button" class="btn-prev btn outlineBtn">Previous</button>
          <button type="submit" 
                  class="btn-cancel btn solidBtn"
                  data-trigger="modal"
                  data-title="Add Employee Confirmation"
                  data-url="ajax/prompt.php?msg=Proceed with adding the employee? &action=employee">
              Add
          </button>
        </div>
      </div>
      <!-- 3RD STEP -->

  </form>