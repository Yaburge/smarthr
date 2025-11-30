<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Attendance</h1>
        <p class="gray-text">All Employee Attendance</p>
      </div>
      <!-- <button type="button" class="cta rounded" id="createBtn" onclick="navigate('/add-employee')">Add Employee</button> -->
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Search">
      </div>
    </div>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Employee Name</th>
          <th>Department</th>
          <th>Designation</th>
          <th>Time In</th>
          <th>Time Out</th>
          <th>Working hrs</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Department">Information Technology</td>
          <td data-cell="Designation">Instructor</td>
          <td data-cell="Time In">09:27 AM</td>
          <td data-cell="Time Out"> - </td>
          <td data-cell="Working Hrs"> - </td>
          <td data-cell="Status">
            <p class="secondary-status">On Time</p>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Harold A. Egrubay</p>
            </div> 
          </td>
          <td data-cell="Department">GenEd</td>
          <td data-cell="Designation">Evaluator</td>
          <td data-cell="Time In">09:10 AM</td>
          <td data-cell="Time Out"> - </td>
          <td data-cell="Working Hrs"> - </td>
          <td data-cell="Status">
            <p class="secondary-status">On Time</p>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Arjay Deimos</p>
            </div> 
          </td>
          <td data-cell="Department">Hospitality Management</td>
          <td data-cell="Designation">Instructor</td>
          <td data-cell="Time In">10:50 PM</td>
          <td data-cell="Time Out"> - </td>
          <td data-cell="Working Hrs"> - </td>
          <td data-cell="Status">
            <p class="tertiary-status">Late</p>
          </td>
        </tr>

      </tbody>

      <tfoot>
        <tr>
          <td colspan="8">
            <div class="tfoot-container">
              <p>Showing 1 to 4 of 20 results</p>

              <div class="pagination">
                <button class="page-btn"><i class="fa-solid fa-angle-left"></i></button>
                
                <button class="page-number active">1</button>
                <button class="page-number">2</button>
                
                <button class="page-btn"><i class="fa-solid fa-angle-right"></i></button>
              </div>
            </div>
          </td>
        </tr>
      </tfoot>

    </table>
  </div>

</section>