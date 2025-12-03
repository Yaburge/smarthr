<section class="section">
  <div class="flex-column gap-40">
    <!-- SECTION HEADER -->
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Add New Employee</h1>
        <p class="gray-text">
          <span class="link" onclick="navigate('/employee')">All Employee</span> 
          <i class="fa-solid fa-angle-right"></i> 
          <span class="link" onclick="navigate('/add-employee')">Add New Employee</span>
        </p>
      </div>
    </div>
    <!-- SECTION HEADER -->

    <!-- SECTION BODY -->
    <?php include 'renderEmployeeForm.php';?>
    <!-- SECTION BODY -->
  </div>
</section>