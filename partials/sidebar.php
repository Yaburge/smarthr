<nav id="admin-sidebar">
  <div class="flex-column gap-50">
    <h2 class="admin-title sub-header bold">Smart<span class="accent-400">HR</span></h2>

    <div class="admin-top">
      <ul class="flex-column gap-10">
        <li data-route="/home" onclick="navigate('/dashboard'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-bars-progress fa-lg"></i></span>
          <span class="text">Dashboard</span>
        </li>

        <li data-route="/employee" onclick="navigate('/employee'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-users fa-lg"></i></span>
          <span class="text">Employees</span>
        </li>

        <li data-route="/department" onclick="navigate('/department'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-building-user fa-lg"></i></span>
          <span class="text">Departments</span>
        </li>

        <li data-route="/attendance" onclick="navigate('/attendance'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-check fa-xl"></i></span>
          <span class="text">Attendance</span>
        </li>

        <li data-route="/leaves" onclick="navigate('/leaves'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-minus fa-xl"></i></span>
          <span class="text">Leaves</span>
        </li>

        <li data-route="/payroll" onclick="navigate('/payroll'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-coins fa-xl"></i></span>
          <span class="text">Payroll</span>
        </li>

        <li data-route="/overtime" onclick="navigate('/overtime'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-clock fa-xl"></i></span>
          <span class="text">Overtime</span>
        </li>

        <li data-route="/holiday" onclick="navigate('/holiday'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-xmark fa-xl"></i></span>
          <span class="text">Holidays</span>
        </li>
      </ul>
    </div>
  </div>
</nav>