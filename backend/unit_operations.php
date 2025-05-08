<?php
require_once 'db.php';

function getUnitsByProgram($programCode) {
    global $conn;
    
    try {
        $sql = "SELECT 
                    u.id,
                    u.unit_code,
                    u.unit_name,
                    u.description,
                    u.credits,
                    GROUP_CONCAT(DISTINCT CONCAT(p.unit_code, ' - ', p.unit_name) SEPARATOR ', ') as prerequisites,
                    CASE WHEN c.program_id = pr.id THEN 1 ELSE 0 END as is_assigned
                FROM units u
                LEFT JOIN unit_prerequisites up ON u.id = up.unit_id
                LEFT JOIN units p ON up.prerequisite_unit_id = p.id
                LEFT JOIN courses c ON u.course_id = c.id
                LEFT JOIN programs pr ON c.program_id = pr.id
                LEFT JOIN programs pr2 ON pr2.program_code = ?
                GROUP BY u.id, u.unit_code, u.unit_name, u.description, u.credits, is_assigned
                ORDER BY u.unit_code";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $programCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = [
                'id' => $row['id'],
                'code' => $row['unit_code'],
                'name' => $row['unit_name'],
                'description' => $row['description'],
                'credits' => $row['credits'],
                'prerequisites' => $row['prerequisites'] ? explode(', ', $row['prerequisites']) : [],
                'is_assigned' => (bool)$row['is_assigned']
            ];
        }
        
        return $units;
        
    } catch (Exception $e) {
        error_log("Error getting units: " . $e->getMessage());
        throw $e;
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
        $conn->begin_transaction();
        
        // Get program ID
        $sql = "SELECT id FROM programs WHERE program_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $programCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Program not found");
        }
        
        $program = $result->fetch_assoc();
        $programId = $program['id'];
        
        // Get course ID for the program
        $sql = "SELECT id FROM courses WHERE program_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $programId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Course not found for program");
        }
        
        $course = $result->fetch_assoc();
        $courseId = $course['id'];
        
        // Update course_id for selected units
        $sql = "UPDATE units SET course_id = ? WHERE id IN (" . implode(',', array_fill(0, count($unitIds), '?')) . ")";
        $stmt = $conn->prepare($sql);
        $params = array_merge([$courseId], $unitIds);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error assigning units: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'get_units':
                    $programCode = $_POST['program'] ?? '';
                    $units = getUnitsByProgram($programCode);
                    echo json_encode(['success' => true, 'units' => $units]);
                    break;
                    
                case 'add_unit':
                    $unitCode = $_POST['unit_code'] ?? '';
                    $unitName = $_POST['unit_name'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $credits = $_POST['credits'] ?? 0;
                    $courseId = $_POST['course'] ?? 0;
                    
                    if (empty($unitCode) || empty($unitName) || empty($description) || $credits <= 0 || $courseId <= 0) {
                        throw new Exception("Missing required parameters");
                    }
                    
                    addNewUnit($unitCode, $unitName, $description, $credits, $courseId);
                    echo json_encode(['success' => true, 'message' => 'Unit added successfully']);
                    break;
                    
                case 'assign_units':
                    $programCode = $_POST['program'] ?? '';
                    $unitIds = isset($_POST['units']) ? (is_array($_POST['units']) ? $_POST['units'] : [$_POST['units']]) : [];
                    
                    if (empty($programCode) || empty($unitIds)) {
                        throw new Exception("Missing required parameters");
                    }
                    
                    assignUnitsToProgram($programCode, $unitIds);
                    echo json_encode(['success' => true, 'message' => 'Units assigned successfully']);
                    break;
                    
                default:
                    throw new Exception("Invalid action");
            }
        } else {
            throw new Exception("No action specified");
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 