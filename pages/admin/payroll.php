<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Payroll</h1>
        <p class="gray-text">All Payroll Summary</p>
      </div>
      <!-- <button type="button" class="cta rounded" id="createBtn" onclick="navigate('/add-employee')">Add Employee</button> -->
    </div>
    
    <div class="grid grid-4">
      <div class="flex-center bg-white rounded padding-50 shadow">
        <p class="bold">48</p>
        <p class="small-text gray-text">Employees to process</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow">
        <p class="bold">₱1,486,400.00</p>
        <p class="small-text gray-text">Total Gross Pay</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow">
        <p class="bold">₱298,720.00</p>
        <p class="small-text gray-text">Total Deductions</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow">
        <p class="bold">₱1,187,680.00</p>
        <p class="small-text gray-text">Total Net Pay</p>
      </div>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Search">
      </div>

      <div class="filter-container">
        <button type="button" id="filter-btn" class="btn outlineBtn"><i class="fa-solid fa-filter"></i> Pay Period</button>
        <div id='filter-modal' class='filter-modal'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Select Pay Period</h1>

            <div class="filter-group">
              <select>
                <option value="Monthly">Monthly</option>
                <option value="Semi-Monthly">Semi-Monthly</option>
              </select>
            </div>

            <div class="filter-group">
              <select>
                <option value="">November 16 - 30, 2025</option>
                <option value="t">December 1 - 15, 2025</option>
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
          <th>ID</th>
          <th>Employee Name</th>
          <th>Base Salary</th>
          <th>Gross Pay</th>
          <th>Total Deductions</th>
          <th>Net Pay</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td data-cell="ID">0001</td>
          <td data-cell="Employee">
            <div class="table-img-name">
              <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
              <p>Christopher Dave T. Cupta</p>
            </div> 
          </td>
          <td data-cell="Base Salary">₱22,500.00</td>
          <td data-cell="Gross Pay">₱29,457.50</td>
          <td data-cell="Total Deductions">
            <p class="red-text">-₱6,202.35</p> 
          </td>
          <td data-cell="Net Pay">
            <p class="green-text">₱23,347.62</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn" onclick="navigate('/payroll-summary')"><i class="fa-regular fa-eye"></i></button>
            </div>
          </td>
        </tr>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="7">
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

  <br>

  <div class="row-fixed justify-right">
    <button class="btn outlineBtn"><i class="fa-regular fa-file-excel"></i> Export Excel Payroll</button>
    <button class="btn solidBtn"><i class="fa-solid fa-check"></i> Process All Payroll</button>
  </div>

</section>