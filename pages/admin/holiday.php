<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/holiday.php';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

// Fetch all holidays
$allHolidays = getAllHolidays();
$totalHolidays = count($allHolidays);
$totalPages = max(1, ceil($totalHolidays / $perPage));
$page = max(1, min($page, $totalPages));

$offset = ($page - 1) * $perPage;
$paginatedHolidays = array_slice($allHolidays, $offset, $perPage);
?>

<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Holidays</h1>
        <p class="gray-text">All Holiday List</p>
      </div>
      <button type="button" class="cta rounded" id="createBtn"
              data-trigger="modal"
              data-title="Add New Holiday"
              data-url="pages/admin/holiday/create.php">Add New Holiday</button>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="holidaySearchInput" placeholder="Search">
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
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($paginatedHolidays)): ?>
        <tr>
          <td colspan="5" style="text-align: center;">No holidays found</td>
        </tr>
        <?php else: ?>
          <?php foreach ($paginatedHolidays as $holiday): ?>
          <tr>
            <td data-cell="Date">
              <?php echo date('F d, Y', strtotime($holiday['date'])); ?>
            </td>
            <td data-cell="Day">
              <?php echo date('l', strtotime($holiday['date'])); ?>
            </td>
            <td data-cell="Holiday Name">
              <?php echo htmlspecialchars($holiday['name']); ?>
            </td>
            <td data-cell="Type">
              <?php echo htmlspecialchars($holiday['type']); ?>
            </td>
            <td data-cell="Action">
              <div class="table-btn">
                <button class="tableBtn outlineBtn"
                        data-trigger="modal"
                        data-title="Edit Holiday"
                        data-url="pages/admin/holiday/edit.php?id=<?php echo $holiday['holiday_id']; ?>">
                  <i class="fa-regular fa-pen-to-square"></i>
                </button>
                <button class="tableBtn outlineBtn"
                        data-trigger="modal"
                        data-title="Delete Holiday"
                        data-url="pages/admin/holiday/delete.php?id=<?php echo $holiday['holiday_id']; ?>&name=<?php echo urlencode($holiday['name']); ?>">
                  <i class="fa-regular fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="5">
            <div class="tfoot-container">
              <p>Showing <?php echo count($paginatedHolidays); ?> of <?php echo $totalHolidays; ?> result(s)</p>

              <div class="pagination">
                <?php if ($totalPages > 1): ?>
                  <button class="page-btn" data-page="<?php echo $page - 1; ?>" <?php echo $page === 1 ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-angle-left"></i>
                  </button>
                  
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === 1 || $i === $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                      <button class="page-number <?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
                    <?php elseif ($i === $page - 2 || $i === $page + 2): ?>
                      <span>...</span>
                    <?php endif; ?>
                  <?php endfor; ?>
                  
                  <button class="page-btn" data-page="<?php echo $page + 1; ?>" <?php echo $page === $totalPages ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-angle-right"></i>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </td>
        </tr>
      </tfoot>

    </table>
  </div>

</section>