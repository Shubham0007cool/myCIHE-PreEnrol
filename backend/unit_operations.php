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
        case 'add_unit':
            $unitCode = $_POST['unit_code'] ?? '';
            $unitName = $_POST['unit_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $credits = $_POST['credits'] ?? 0;
            $courseId = $_POST['course'] ?? 0;
            
            if (empty($unitCode) || empty($unitName) || empty($description) || empty($credits) || empty($courseId)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }
            
            try {
                addNewUnit($unitCode, $unitName, $description, $credits, $courseId);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
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
            
        case 'add_selection':
            $unit_id = $_POST['unit_id'] ?? 0;
            $student_id = $_SESSION['id'] ?? 0;
            
            if (empty($unit_id) || empty($student_id)) {
                $_SESSION["error"] = "Invalid request";
                header("Location: ../select_units.php");
                exit;
            }
            
            // Check if already selected
            $check_sql = "SELECT id FROM unit_selections WHERE student_id = ? AND unit_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "ii", $student_id, $unit_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $_SESSION["error"] = "You have already selected this unit";
                header("Location: ../select_units.php");
                exit;
            }
            
            // Add selection
            $sql = "INSERT INTO unit_selections (student_id, unit_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $unit_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["success"] = "Unit selected successfully";
            } else {
                $_SESSION["error"] = "Error selecting unit";
            }
            
            header("Location: ../select_units.php");
            exit;
            break;

        case 'remove_selection':
            $unit_id = $_POST['unit_id'] ?? 0;
            $student_id = $_SESSION['id'] ?? 0;
            
            if (empty($unit_id) || empty($student_id)) {
                $_SESSION["error"] = "Invalid request";
                header("Location: ../select_units.php");
                exit;
            }
            
            $sql = "DELETE FROM unit_selections WHERE student_id = ? AND unit_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $unit_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["success"] = "Unit removed from selection";
            } else {
                $_SESSION["error"] = "Error removing unit";
            }
            
            header("Location: ../select_units.php");
            exit;
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 