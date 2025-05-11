<?php
session_start();
require_once "db.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../index.php");
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

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="unit_selections.xls"');
header('Cache-Control: max-age=0');

// Output Excel content
echo "Course\tUnit Code\tUnit Name\tCredits\tStudents Count\tSelected By\n";

foreach ($units as $unit) {
    echo implode("\t", [
        $unit['course_name'],
        $unit['unit_code'],
        $unit['unit_name'],
        $unit['credits'],
        $unit['student_count'],
        $unit['students'] ?? 'None'
    ]) . "\n";
}
?> 