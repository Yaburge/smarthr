<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Leave</h1>
        <p class="gray-text">All Employee Leave</p>
      </div>
      <!-- <button type="button" class="cta rounded" id="createBtn" onclick="navigate('/add-employee')">Add Employee</button> -->
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
              <label>Status:</label>
              <select>
                <option value="All">All</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Pending">Pending</option>
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
          <th>Employee Name</th>
          <th>Leave Type</th>
          <th>Start/End Date</th>
          <th>Attachment</th>
          <th>Status</th>
          <th>Action</th>
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
          <td data-cell="Leave Type">Sick</td>
          <td data-cell="Start/End Date">12 Jun 2025 to 13 Jun 2025</td>
          <td data-cell="Attachment">N/A</td>
          <td data-cell="Status">
            <p class="primary-status">Pending</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this leave request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's leave request? &action=#">
                <i class="fa-regular fa-circle-check"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Harold A. Egrubay</p>
            </div> 
          </td>
          <td data-cell="Leave Type">Vacation</td>
          <td data-cell="Start/End Date">09 Jun 2025 to 11 Jun 2025</td>
          <td data-cell="Attachment">N/A</td>
          <td data-cell="Status">
            <p class="tertiary-status">Rejected</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this leave request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's leave request? &action=#">
                <i class="fa-regular fa-circle-check"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Employee Name">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Arjay Deimos</p>
            </div> 
          </td>
          <td data-cell="Leave Type">Maternity</td>
          <td data-cell="Start/End Date">02 Jul 2025 to 11 Jul 2025</td>
          <td data-cell="Attachment">N/A</td>
          <td data-cell="Status">
            <p class="secondary-status">Approved</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this leave request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's leave request? &action=#">
                <i class="fa-regular fa-circle-check"></i>
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