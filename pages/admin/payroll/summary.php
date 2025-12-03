<section class="section">
  <div class="flex-column gap-60">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Payroll</h1>
        <p class="gray-text">
          <span class="link" onclick="navigate('/payroll')">All Payroll Summary</span> 
          <i class="fa-solid fa-angle-right"></i> 
          <span class="link" onclick="navigate('/payroll-summary')">Summary Breakdown</span>
        </p>
      </div>
      <div class="filter-container">
        <button type="button" id="filter-btn" class="btn solidBtn">Add Deductions</button>
        <div id='filter-modal' class='filter-modal'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Other Deductions</h1>

            <div class="filter-group">
              <label>Deduction Name:</label>
              <input type="text" placeholder="-">
            </div>

            <div class="filter-group">
              <label>Deduction Amount:</label>
              <input type="text" placeholder="-">
            </div>

            <div class="filter-action">
              <button type="button" id='filter-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" class="btn solidBtn">Apply</button>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- PAYROLL SUMMARY HEADER -->
    <div class="row-fixed align-center bg-white rounded padding-50 shadow">
      <div class="row-fixed align-center">
        <img src="assets/media/images/iso.jpg" alt="employee-picture" class="t-img">
        <div class="flex-column gap-10">
          <h1 class="sub-header bold">Christopher Dave T. Cupta</h1>
          <p class="gray-text">0001 | Semi-Monthly </p>
        </div>
      </div>
      <p class="secondary-status"><i class="fa-regular fa-circle-check"></i> Ready for Approval</p>
    </div>
    <!-- PAYROLL SUMMARY HEADER -->

    <!-- PAYROLL SUMMARY BODY -->
    <div class="payroll-summary-container">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Basic Rates & Attendance</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>Monthly Rate</p>
          <p>₱45,000.00</p>
        </div>

        <div class="row-fixed">
          <p>Daily Rate</p>
          <p>₱1,479.45</p>
        </div>

        <div class="row-fixed">
          <p>Hourly Rate</p>
          <p>₱184.93</p>
        </div>

        <div class="row-fixed">
          <p>Days Worked (this period)</p>
          <p>11 days</p>
        </div>

        <div class="row-fixed">
          <p class="red-text">Tardiness (30 mins)</p>
          <p class="red-text">-₱92.47</p>
        </div>
      </div>
    </div>
    <!-- PAYROLL SUMMARY CARD -->

    <!-- PAYROLL SUMMARY CARD -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Earnings</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>Basic Pay (Semi-monthly)</p>
          <p>₱22,500.00</p>
        </div>

        <div class="row-fixed">
          <p class="red-text">Tardiness Deduction</p>
          <p class="red-text">-₱92.47</p>
        </div>

        <div class="row-fixed">
          <p>Net Basic Pay</p>
          <p class="bold">₱22,407.50</p>
        </div>

        <div class="row-fixed">
          <p>Overtime Pay</p>
          <p class="green-text">+₱1,250.00</p>
        </div>

        <div class="row-fixed">
          <p>Holiday Pay</p>
          <p class="green-text">+₱800.00</p>
        </div>

        <div class="row-fixed">
          <p>Allowances (Non-taxable)</p>
          <p>+₱5,000.00</p>
        </div>

        <hr>

        <div class="row-fixed">
          <p class="bold accent-text sub-header">Gross Pay</p>
          <p class="bold accent-text sub-header">₱29,457.50</p>
        </div>
      </div>
    </div>
    <!-- PAYROLL SUMMARY CARD -->

    <!-- PAYROLL SUMMARY CARD -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Statutory Deductions</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>SSS Contribution</p>
          <p> -₱1,012.50</p>
        </div>

        <div class="row-fixed">
          <p>PhilHealth</p>
          <p> -₱506.25</p>
        </div>

        <div class="row-fixed">
          <p>Pag-IBIG</p>
          <p> -₱100.00</p>
        </div>

        <div class="row-fixed">
          <p>Withholding Tax</p>
          <p> -₱2,391.13</p>
        </div>

        <hr>

        <div class="row-fixed">
          <p class="bold red-text sub-header">Total Statutory</p>
          <p class="bold red-text sub-header">-₱4,009.88</p>
        </div>
      </div>
    </div>
    <!-- PAYROLL SUMMARY CARD -->

    <!-- PAYROLL SUMMARY CARD -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Other Deductions</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>SSS Loan</p>
          <p> -₱500.00</p>
        </div>

        <div class="row-fixed">
          <p>Company Loan</p>
          <p> -₱1,500.00</p>
        </div>

        <div class="row-fixed">
          <p>Union Dues</p>
          <p> -₱100.00</p>
        </div>

        <hr>

        <div class="row-fixed">
          <p class="bold red-text sub-header">Total Other</p>
          <p class="bold red-text sub-header">-₱2,100.00</p>
        </div>
      </div>
    </div>
    <!-- PAYROLL SUMMARY CARD -->

    <div class="summary-body rounded shadow flex-column">
      <div class="row-fixed">
        <p class="bold green-text sub-header">Net Pay</p>
        <p class="bold green-text sub-header">₱23,347.62</p>
      </div>

      <hr>

      <div class="row-fixed justify-right">
        <button class="btn outlineBtn"><i class="fa-regular fa-file-pdf"></i> Export PDF Payslip</button>
        <button class="btn solidBtn"><i class="fa-solid fa-check"></i> Process Payroll</button>
      </div>
    </div>

    <!-- PAYROLL SUMMARY BODY -->

  </div>
</section>