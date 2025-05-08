<?php
require_once 'db.php';

function getUnitsByProgram($programCode) {
    global $conn;
    
    try {
        $sql = "SELECT 
                    u.id,
                    u.unit_code as code,
                    u.unit_name as name,
                    u.description,
                    u.credits,
                    GROUP_CONCAT(DISTINCT CONCAT(p.unit_code, ' - ', p.unit_name) SEPARATOR ', ') as prerequisites,
                    CASE WHEN c.program_id = pr.id THEN 1 ELSE 0 END as is_assigned
                FROM units u
                LEFT JOIN unit_prerequisites up ON u.id = up.unit_id
                LEFT JOIN units p ON up.prerequisite_unit_id = p.id
                LEFT JOIN course_units c ON u.id = c.unit_id
                LEFT JOIN programs pr ON c.program_id = pr.id AND pr.program_code = ?
                GROUP BY u.id, u.unit_code, u.unit_name, u.description, u.credits, c.program_id, pr.id
                ORDER BY u.unit_code";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $programCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $row['prerequisites'] = $row['prerequisites'] ? explode(', ', $row['prerequisites']) : [];
            $units[] = $row;
        }
        
        return ['success' => true, 'units' => $units];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function addNewUnit($unitCode, $unitName, $description, $credits, $courseId) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Check if unit code already exists
        $sql = "SELECT id FROM units WHERE unit_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $unitCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Unit code already exists");
        }
        
        // Insert new unit
        $sql = "INSERT INTO units (unit_code, unit_name, description, credits, course_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssii', $unitCode, $unitName, $description, $credits, $courseId);
        $stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error adding unit: " . $e->getMessage());
        throw $e;
    }
}

function assignUnitsToProgram($programCode, $unitIds) {
    global $conn;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get program ID from code
        $stmt = $conn->prepare("SELECT id FROM programs WHERE program_code = ?");
        $stmt->bind_param("s", $programCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $program = $result->fetch_assoc();
        
        if (!$program) {
            throw new Exception("Program not found");
        }
        
        // Remove existing assignments
        $stmt = $conn->prepare("DELETE FROM course_units WHERE program_id = ?");
        $stmt->bind_param("i", $program['id']);
        $stmt->execute();
        
        // Add new assignments
        $stmt = $conn->prepare("INSERT INTO course_units (program_id, unit_id) VALUES (?, ?)");
        foreach ($unitIds as $unitId) {
            $stmt->bind_param("ii", $program['id'], $unitId);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        return ['success' => true];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_units':
            $programCode = $_POST['program'] ?? '';
            if (empty($programCode)) {
                echo json_encode(['success' => false, 'message' => 'Program code is required']);
                exit;
            }
            echo json_encode(getUnitsByProgram($programCode));
            break;
            
        case 'assign_units':
            $programCode = $_POST['program'] ?? '';
            $units = json_decode($_POST['units'] ?? '[]', true);
            
            if (empty($programCode)) {
                echo json_encode(['success' => false, 'message' => 'Program code is required']);
                exit;
            }
            
            if (empty($units)) {
                echo json_encode(['success' => false, 'message' => 'No units selected']);
                exit;
            }
            
            echo json_encode(assignUnitsToProgram($programCode, $units));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 