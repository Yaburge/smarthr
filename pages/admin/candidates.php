<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Candidates</h1>
        <p class="gray-text">
          <span class="link" >Show All Candidates</span> 
          <!-- <i class="fa-solid fa-angle-right"></i> 
          <span class="link">IT Department</span> -->
        </p>
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
          <th>Applied For</th>
          <th>Applied Date</th>
          <th>Email</th>
          <th>Phone</th>
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
          <td data-cell="Applied For">Instructor</td>
          <td data-cell="Applied Date">November 24, 2025</td>
          <td data-cell="Email">Sili@gmail.com</td>
          <td data-cell="Phone">(+63)999 999 9999</td>
          <td data-cell="Status">
            <p class="primary-status">Regular</p>
          </td>
          <td data-cell="Action">
            <div class="table-btn">
              <button class="tableBtn outlineBtn" onclick="navigate('/view-candidate')"><i class="fa-regular fa-eye"></i></button>
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