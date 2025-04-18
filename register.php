<?php
$host = 'localhost';
$db   = 'cihe_enrolments';
$user = 'root';
$pass = ''; // Default MAMP root password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Simulate student ID for now (we can replace this with login later)
$student_id = "CIHE22580";

// Collect submitted values
$selected_subjects = $_POST['subjects'] ?? [];
$teachers = $_POST['teachers'] ?? [];
$days = $_POST['days'] ?? [];
$times = $_POST['times'] ?? [];

if (count($selected_subjects) > 0) {
    $stmt = $pdo->prepare("INSERT INTO subject_registrations (student_id, subject_code, teacher, day, time_slot) VALUES (?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($selected_subjects); $i++) {
        $stmt->execute([
            $student_id,
            $selected_subjects[$i],
            $teachers[$i],
            $days[$i],
            $times[$i]
        ]);
    }

    echo "<script>alert('Registration successful!'); window.location.href='profile.php';</script>";
} else {
    echo "<script>alert('Please select at least one subject!'); window.location.href='registration.html';</script>";
}
?>
