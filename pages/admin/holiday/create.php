<form id="holidayCreateForm" class="flex-column gap-20">
  
  <div id="feedback" style="margin-bottom: 10px;"></div>
  
  <div class="filter-group">
    <label>Holiday Name:</label>
    <input type="text" name="name" id="holiday_name" required placeholder="e.g. New Year's Day">
  </div>

  <div class="filter-group">
    <label>Date:</label>
    <input type="date" name="date" id="holiday_date" required>
  </div>

  <div class="filter-group">
    <label>Type:</label>
    <select name="type" id="holiday_type" required>
      <option value="">Select Type</option>
      <option value="Regular">Regular Holiday</option>
      <option value="Special Non-Working">Special Non-Working Holiday</option>
    </select>
  </div>

  <button type="submit" class="btn solidBtn">Add Holiday</button>
</form>