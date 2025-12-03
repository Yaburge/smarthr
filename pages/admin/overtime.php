<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Overtime</h1>
        <p class="gray-text">All Overtime Requests</p>
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
          <th>Request Date</th>
          <th>Overtime Date</th>
          <th>Employee</th>
          <th>Duration</th>
          <th>Overtime</th>
          <th>Reason</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td data-cell="Request Date">17 February 2025</td>
          <td data-cell="Overtime Date">19 February 2025</td>
          <td data-cell="Employee">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Duration"> 3 hours </td>
          <td data-cell="Overtime"> 07:00 - 10:00 PM </td>
          <td data-cell="Reason">
            <p>
              Unexpected project deadline
            </p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this overtime request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's overtime request? &action=#">
                <i class="fa-regular fa-circle-check"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Request Date">17 February 2025</td>
          <td data-cell="Overtime Date">19 February 2025</td>
          <td data-cell="Employee">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Duration"> 3 hours </td>
          <td data-cell="Overtime"> 07:00 - 10:00 PM </td>
          <td data-cell="Reason">
            <p>
              Unexpected project deadline
            </p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this overtime request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's overtime request? &action=#">
                <i class="fa-regular fa-circle-check"></i>
              </button>
            </div>
          </td>
        </tr>

        <tr>
          <td data-cell="Request Date">17 February 2025</td>
          <td data-cell="Overtime Date">19 February 2025</td>
          <td data-cell="Employee">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Duration"> 3 hours </td>
          <td data-cell="Overtime"> 07:00 - 10:00 PM </td>
          <td data-cell="Reason">
            <p>
              Unexpected project deadline
            </p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this overtime request? &action=#">
                <i class="fa-regular fa-circle-xmark"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's overtime request? &action=#">
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