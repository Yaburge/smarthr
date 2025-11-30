<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">IT Department</h1>
        <p class="gray-text">
          <span class="link" onclick="navigate('/department')">All Departments</span> 
          <i class="fa-solid fa-angle-right"></i> 
          <span class="link">IT Department</span>
        </p>
      </div>
      <button type="button" class="cta rounded" id="createBtn" onclick="navigate('/add-employee')">Add Employee</button>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Search">
      </div>

      <div class="filter-container">
        <button type="button" id="filter-btn" class="btn outlineBtn"><i class="fa-solid fa-filter"></i> Filter</button>
        <div id='filter-modal' class='filter-modal'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Filter</h1>

            <div class="filter-group">
              <label>Department:</label>
              <select>
                <option value="All">All</option>
                <option value="it">Information Technology</option>
                <option value="hm">Hospitality Management</option>
                <option value="gened">GenEd</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Status:</label>
              <select>
                <option value="All">All</option>
                <option value="permanent">Permanent</option>
                <option value="part-time">Part-time</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="filter-action">
              <button type="button" id='filter-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" class="btn solidBtn">Apply</button>
            </div>

          </div>
        </div>
      </div>
      
    </div>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Employee ID</th>
          <th>Employee Name</th>
          <th>Designation</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td data-cell="Employee ID">0001</td>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Designation">Instructor</td>
          <td data-cell="Status">
            <p class="primary-status">Regular</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn" onclick="navigate('/view-employee')"><i class="fa-regular fa-eye"></i></button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Removal"
                      data-url="ajax/prompt.php?id=105&msg=Remove this employee from this department? &action=employee">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee ID">0002</td>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Harold A. Egrubay</p>
            </div> 
          </td>
          <td data-cell="Designation">Evaluator</td>
          <td data-cell="Status">
            <p class="secondary-status">Part-time</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn" onclick="navigate('/view-employee')"><i class="fa-regular fa-eye"></i></button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Removal"
                      data-url="ajax/prompt.php?id=105&msg=Remove this employee from this department? &action=employee">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee ID">0003</td>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Arjay Deimos</p>
            </div> 
          </td>
          <td data-cell="Designation">Instructor</td>
          <td data-cell="Status">
            <p class="tertiary-status">Inactive</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn" onclick="navigate('/view-employee')"><i class="fa-regular fa-eye"></i></button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Removal"
                      data-url="ajax/prompt.php?id=105&msg=Remove this employee from this department? &action=employee">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </div>
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