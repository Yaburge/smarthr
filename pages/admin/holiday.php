<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Holidays</h1>
        <p class="gray-text">All Holiday List</p>
      </div>
      <button type="button" class="cta rounded" id="createBtn">Add New Holiday</button>
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
          <th>Date</th>
          <th>Day</th>
          <th>Holiday Name</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td data-cell="Date">January 01, 2025</td>
          <td data-cell="Day">Tuesday</td>
          <td data-cell="Holiday Name"> New Year  </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Rejection"
                      data-url="ajax/prompt.php?id=105&msg=Are you sure you want to reject this overtime request? &action=#">
                <i class="fa-regular fa-pen-to-square"></i>
              </button>
              <button class="tableBtn outlineBtn"
                      data-trigger="modal"
                      data-title="Confirm Approval"
                      data-url="ajax/prompt.php?id=105&msg=Approve this employee's overtime request? &action=#">
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