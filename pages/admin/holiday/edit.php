<?php
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
require_once BASE_PATH . '/includes/queries/holiday.php';

$holiday_id = $_GET['id'] ?? 0;
$holiday = getHolidayById($holiday_id);

if (!$holiday) {
    echo "<p>Holiday not found</p>";
    exit;
}
?>

<form id="holidayEditForm" class="flex-column gap-20">
  <input type="hidden" name="holiday_id" value="<?php echo $holiday['holiday_id']; ?>">
  
  <div id="feedback" style="margin-bottom: 10px;"></div>
  
  <div class="filter-group">
    <label>Holiday Name:</label>
    <input type="text" name="name" id="holiday_name" required placeholder="e.g. New Year's Day" value="<?php echo htmlspecialchars($holiday['name']); ?>">
  </div>

  <div class="filter-group">
    <label>Date:</label>
    <input type="date" name="date" id="holiday_date" required value="<?php echo $holiday['date']; ?>">
  </div>

  <div class="filter-group">
    <label>Type:</label>
    <select name="type" id="holiday_type" required>
      <option value="">Select Type</option>
      <option value="Regular" <?php echo $holiday['type'] === 'Regular' ? 'selected' : ''; ?>>Regular Holiday</option>
      <option value="Special Non-Working" <?php echo $holiday['type'] === 'Special Non-Working' ? 'selected' : ''; ?>>Special Non-Working Holiday</option>
    </select>
  </div>

  <button type="submit" class="btn solidBtn">Update Holiday</button>
</form>