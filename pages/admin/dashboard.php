<section class="section">

  
<div class="flex-row">
  <div class="flex-column gap-40 width-100">
    <!-- DASHBOARD HEADER CARDS -->
    <div class="grid grid-3">
      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <p class="secondary-status dashboard-icons">
            <i class="fa-solid fa-user-check"></i>
          </p>
          <p class="sub-header">Present</p>
        </div>
        <h1 class="header bold">142</h1>
        <p class="gray-text">out of 150 employee</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <p class="primary-status dashboard-icons">
            <i class="fa-solid fa-user-clock"></i>
          </p>
          <p class="sub-header">Late</p>
        </div>
        <h1 class="header bold">5</h1>
        <p class="gray-text">arrived after 7:05 AM</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <span class="tertiary-status dashboard-icons">
            <i class="fa-solid fa-user-slash"></i>
          </span>
          <p class="sub-header">Absent</p>
        </div>
        <h1 class="header bold">3</h1>
        <p class="gray-text">On Unplanned Leave</p>
      </div>
    </div>
    <!-- DASHBOARD HEADER CARDS -->

    <!-- EMPLOYEE OVERVIEW -->
    <div class="bg-white padding-50 rounded shadow flex-column gap-50">
      <h1 class="sub-header bold">Employee Overview</h1>
      <div class="grid grid-4">
        <div class="flex-center gap-20">
          <p class="sub-header bold">150</p>
          <p class="gray-text text-center">Total Employees</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">4</p>
          <p class="gray-text text-center">Total Departments</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">3</p>
          <p class="gray-text text-center">New Hires</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">19</p>
          <p class="gray-text text-center">Open Positions</p>
        </div>
      </div>
    </div>
    <!-- EMPLOYEE OVERVIEW -->
  </div>

  <!-- PAYROLL OVERVIEW -->
  <div class="flex-column justify-between gap-40 bg-white padding-40 rounded shadow">

    <div class="flex-center gap-20">
      <p class="tertiary-status dashboard-icons">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </p>

      <h1 class="sub-header text-center bold">Upcoming Payroll</h1>
      <p class="gray-text text-center">Cut-off for the current pay period is approaching.</p>
    </div>
    

    <div class="flex-center cutoff-container rounded padding-30">
      <p>Cut-off in</p>
      <p class="sub-header bold">2 Days</p>
    </div>

    <div>
      <p class="gray-text">Total Cost</p>
      <p class="bold">₱1,187,680.00</p>
    </div>

    <button type="button" onclick="navigate('/payroll')" class="btn solidBtn">View Details <i class="fa-solid fa-arrow-right"></i></button>

  </div>
  <!-- PAYROLL OVERVIEW -->
</div>
  

  

</section>