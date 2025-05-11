<?php
session_start();
require_once "backend/db.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: index.php");
    exit;
}

// Get unit selections with student counts
$sql = "SELECT u.*, c.name as course_name, 
        COUNT(us.id) as student_count,
        GROUP_CONCAT(DISTINCT s.first_name, ' ', s.last_name SEPARATOR ', ') as students
        FROM units u 
        JOIN courses c ON u.course_id = c.id
        LEFT JOIN unit_selections us ON u.id = us.unit_id
        LEFT JOIN students s ON us.student_id = s.id
        GROUP BY u.id
        ORDER BY c.name, u.unit_code";

$result = mysqli_query($conn, $sql);
$units = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Selections - CIHE</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="container">
        <h1>Unit Selections Overview</h1>
        
        <div class="export-section">
            <form action="backend/export_unit_selections.php" method="POST">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </form>
        </div>

        <div class="unit-selections-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Credits</th>
                        <th>Students Count</th>
                        <th>Selected By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($units as $unit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($unit["course_name"]); ?></td>
                            <td><?php echo htmlspecialchars($unit["unit_code"]); ?></td>
                            <td><?php echo htmlspecialchars($unit["unit_name"]); ?></td>
                            <td><?php echo htmlspecialchars($unit["credits"]); ?></td>
                            <td><?php echo htmlspecialchars($unit["student_count"]); ?></td>
                            <td><?php echo htmlspecialchars($unit["students"] ?? 'None'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 