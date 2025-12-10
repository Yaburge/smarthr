<?php
// ============================================
// FILE PATH: includes/queries/holiday.php
// ============================================

require_once BASE_PATH . '/includes/config.php';

// Get all holidays
function getAllHolidays() {
    global $pdo;
    $sql = "SELECT * FROM holidays ORDER BY date ASC";
    return $pdo->query($sql)->fetchAll();
}

// Get holidays with filters
function getAllHolidaysFiltered($search = '') {
    global $pdo;
    
    $sql = "SELECT * FROM holidays WHERE 1=1";
    $params = [];
    
    // Search filter - holiday name or date
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR date LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    $sql .= " ORDER BY date ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create holiday
function createHoliday($name, $date, $type) {
    global $pdo;
    
    try {
        // Check if holiday already exists for this date
        $checkSql = "SELECT holiday_id FROM holidays WHERE date = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$date]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'A holiday already exists on this date'];
        }
        
        $sql = "INSERT INTO holidays (name, date, type) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $date, $type]);
        
        return ['success' => true, 'message' => 'Holiday created successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to create holiday: ' . $e->getMessage()];
    }
}

// Get holiday by ID
function getHolidayById($holiday_id) {
    global $pdo;
    
    $sql = "SELECT * FROM holidays WHERE holiday_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$holiday_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update holiday
function updateHoliday($holiday_id, $name, $date, $type) {
    global $pdo;
    
    try {
        // Check if another holiday exists on this date (excluding current holiday)
        $checkSql = "SELECT holiday_id FROM holidays WHERE date = ? AND holiday_id != ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$date, $holiday_id]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Another holiday already exists on this date'];
        }
        
        $sql = "UPDATE holidays SET name = ?, date = ?, type = ? WHERE holiday_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $date, $type, $holiday_id]);
        
        return ['success' => true, 'message' => 'Holiday updated successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update holiday: ' . $e->getMessage()];
    }
}

// Delete holiday
function deleteHoliday($holiday_id) {
    global $pdo;
    
    try {
        $sql = "DELETE FROM holidays WHERE holiday_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$holiday_id]);
        
        return ['success' => true, 'message' => 'Holiday deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete holiday: ' . $e->getMessage()];
    }
}

?>