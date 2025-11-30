<div class="section">
  <div class="flex-column gap-40">
    <!-- SECTION HEADER -->
    <div class="row-fixed align-center">

      <div class="row-fixed align-center">
        <img src="assets/media/images/iso.jpg" alt="employee-img" class="employee-img">
        <div class="flex-column gap-10">
          <h1 class="sub-header bold">Harold Egrubay</h1> <!-- FETCH EMPLOYEE NAME -->
          <div>
            <p class="gray-text row-fixed gap-30 align-center justify-left"><i class="fa-solid fa-briefcase"></i> Instructor</p>
            <p class="gray-text row-fixed gap-30 align-center justify-left"><i class="fa-solid fa-envelope"></i> sample@sample.com</p>
          </div>
        </div>
      </div>

      <button type="button" id="editBtn" class="btn solidBtn">Edit Profile</button>
    </div>
    <!-- SECTION HEADER -->

    <!-- SECTION BODY -->
    <div id="main-content" class="row-fixed">
      <div id="vertical-tabs">
        <button type="button" data-vertical-target="profile-view" class="vertical-tab-btn active">Profile</button>
        <button type="button" data-vertical-target="attendance-view" class="vertical-tab-btn">Attendance</button>
        <button type="button" data-vertical-target="leave-view" class="vertical-tab-btn">Leave</button>
      </div>

      <div id="profile-view" class="width-100 vertical-content">
        <?php include 'renderEmployeeForm.php';?>
      </div>

      <div id="attendance-view" class="width-100 vertical-content hidden">
        <?php include 'attendance.php';?>
      </div>

      <div id="leave-view" class="width-100 vertical-content hidden">
        <?php include 'leave.php';?>
      </div>
      
      
    </div>
    <!-- SECTION BODY -->
  </div>
</div>