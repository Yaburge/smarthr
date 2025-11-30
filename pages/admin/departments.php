<section class="section">
  <div class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Department</h1>
        <p class="gray-text"><span>All Department</span></p>
      </div>
      <button type="button" 
              class="cta rounded" 
              id="createBtn"
              data-trigger="modal"
              data-title="Add New Department"
              data-url="pages/admin/department/add-department.php">
        Add Department
      </button>
    </div>

    <!-- THE GRID OF THE CARDS -->
    <div class="grid grid-2 department-grid">
      <!-- WHOLE CARD -->
      <div class="department-card rounded shadow">
        <!-- CARD HEADER -->
        <div class="row-fixed align-center department-card-header">
          <div>
            <h1 class="sub-header bold">Information Technology</h1>
            <p class="gray-text">20 Members</p>
          </div>

          <button type="button" class="noOutlineBtn outlineBtn accent-400" onclick="navigate('/view-department')">View All</button>
        </div>
        <!-- CARD HEADER -->

        <!-- NOTE: LIMIT TO 4 EMPLOYEE LAT MA SHOW -->
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn" onclick="navigate('/view-employee')"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        <div class="flex-column text-center gap-20 padding-50" style="display: none">
          <h1 class="bold gray-text">No Employees</h1>
          <p class="light-text">Please add some Employees</p>
        </div>
        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        
        <!-- WHOLE CARD -->
      </div>


      <div class="department-card rounded shadow"> <!-- WHOLE CARD -->
        <div class="row-fixed align-center department-card-header"> <!-- CARD HEADER -->
          <div>
            <h1 class="sub-header bold">GenEd</h1>
            <p class="gray-text">0 Member</p>
          </div>

          <button type="button" class="noOutlineBtn outlineBtn accent-400">View All</button>
        </div>  <!-- CARD HEADER -->

        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        <div class="flex-column text-center gap-20 padding-50">
          <h1 class="bold gray-text">No Employees</h1>
          <p class="light-text">Please add some Employees</p>
        </div>
        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->

      </div> <!-- WHOLE CARD -->


      <!-- WHOLE CARD -->
      <div class="department-card rounded shadow">
        <!-- CARD HEADER -->
        <div class="row-fixed align-center department-card-header">
          <div>
            <h1 class="sub-header bold">Hospitality Management</h1>
            <p class="gray-text">20 Members</p>
          </div>

          <button type="button" class="noOutlineBtn outlineBtn accent-400">View All</button>
        </div>
        <!-- CARD HEADER -->

        <!-- NOTE: LIMIT TO 4 EMPLOYEE LAT MA SHOW -->
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        <div class="flex-column text-center gap-20 padding-50" style="display: none">
          <h1 class="bold gray-text">No Employees</h1>
          <p class="light-text">Please add some Employees</p>
        </div>
        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        
        <!-- WHOLE CARD -->
      </div>


      <!-- WHOLE CARD -->
      <div class="department-card rounded shadow">
        <!-- CARD HEADER -->
        <div class="row-fixed align-center department-card-header">
          <div>
            <h1 class="sub-header bold">Filipino</h1>
            <p class="gray-text">20 Members</p>
          </div>

          <button type="button" class="noOutlineBtn outlineBtn accent-400">View All</button>
        </div>
        <!-- CARD HEADER -->

        <!-- NOTE: LIMIT TO 4 EMPLOYEE LAT MA SHOW -->
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->
        <div class="row-fixed align-center department-card-body">
          <div class="row-fixed align-center justify-left">
            <img src="assets/media/images/iso.jpg" alt="Employee-img" class="t-img">
            <div>
              <h1>Harold A. Egrubay</h1>
              <p class="gray-text">Instructor</p>
            </div>
          </div>
          <button class="noOutlineBtn outlineBtn"><i class="fa-regular fa-eye fa-lg"></i></button>
        </div>
        <!-- EMPLOYEE INFORMATION (IG LOOP LA ININ) -->

        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        <div class="flex-column text-center gap-20 padding-50" style="display: none">
          <h1 class="bold gray-text">No Employees</h1>
          <p class="light-text">Please add some Employees</p>
        </div>
        <!-- NOTE: SHOW THIS WHEN NO DATA IS DETECTED -->
        
        <!-- WHOLE CARD -->
      </div>

    </div> <!-- THE GRID OF THE CARDS -->
    
  </div>
</section>