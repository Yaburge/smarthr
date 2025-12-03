<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/departments.php';

// Get all departments with their total member count
$departments = getDepartmentsWithStats();
?>

<section class="section">
  <div class="flex-column gap-40">
    
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Department</h1>
        <p class="gray-text"><span>All Departments</span></p>
      </div>
      
      <button type="button" 
              class="cta rounded" 
              id="createBtn"
              data-trigger="modal"
              data-title="Add New Department"
              data-url="pages/admin/department/add-department.php">
        Add Department
      </button>
    </div>

    <div class="grid grid-2 department-grid">
      
      <?php if (empty($departments)): ?>
          <div class="width-100 text-center gray-text padding-50">
              No departments found. Please add one.
          </div>
      <?php else: ?>
      
          <?php foreach ($departments as $dept): ?>
              <?php 
                  // Fetch top 4 members for this specific department
                  $previewMembers = getDepartmentPreviewEmployees($dept['department_id']); 
              ?>

              <div class="department-card rounded shadow">
                
                <div class="row-fixed align-center department-card-header">
                  <div>
                    <h1 class="sub-header bold"><?php echo htmlspecialchars($dept['name']); ?></h1>
                    <p class="gray-text"><?php echo $dept['member_count']; ?> Members</p>
                  </div>

                  <button type="button" 
                          class="noOutlineBtn outlineBtn accent-400" 
                          onclick="navigate('/view-department?id=<?php echo $dept['department_id']; ?>')">
                      View All
                  </button>
                </div>
                <?php if (!empty($previewMembers)): ?>
                    
                    <?php foreach ($previewMembers as $emp): ?>
                        <?php 
                            $fullname = $emp['first_name'] . ' ' . $emp['last_name'];
                            $designation = $emp['designation'] ?? 'Employee';
                        ?>
                        
                        <div class="row-fixed align-center department-card-body">
                          <div class="row-fixed align-center justify-left">
                            <img src="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($emp['profile_picture']); ?>" 
                                 alt="Employee-img" 
                                 class="t-img"
                                 onerror="this.src='/SmartHR/assets/media/images/default_avatar.jpg'">
                            <div>
                              <h1><?php echo htmlspecialchars($fullname); ?></h1>
                              <p class="gray-text"><?php echo htmlspecialchars($designation); ?></p>
                            </div>
                          </div>
                          <button class="noOutlineBtn outlineBtn" 
                                  onclick="navigate('/view-employee?id=<?php echo $emp['employee_id']; ?>')">
                              <i class="fa-regular fa-eye fa-lg"></i>
                          </button>
                        </div>
                        <?php endforeach; ?>

                <?php else: ?>
                    
                    <div class="flex-column text-center gap-20 padding-50">
                      <h1 class="bold gray-text">No Employees</h1>
                      <p class="light-text">Please add some Employees</p>
                    </div>

                <?php endif; ?>
                
              </div>
              <?php endforeach; ?>
      
      <?php endif; ?>

    </div> 
    </div>
</section>