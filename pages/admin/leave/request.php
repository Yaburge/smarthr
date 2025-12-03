<form class="flex-column gap-20">
  <div class="filter-group">
    <label>Start Date:</label>
    <input type="date">
  </div>

  <div class="filter-group">
    <label>End Date:</label>
    <input type="date">
  </div>

  <div class="filter-group">
    <label>Leave Type:</label>
    <select>
      <option disabled selected>Select Leave Type</option>
      <option value="Vacation">Vacation</option>
      <option value="Birthday">Birthday</option>
      <option value="Sick">Sick</option>
      <option value="Maternity">Maternity</option>
    </select>
  </div>

  <div class="filter-group">
    <label>Reason:</label>
    <textarea Placeholder="Please provide a brief reason for your leave"></textarea>
  </div>

  <input type="submit" value="Apply" class="btn solidBtn">
</form>