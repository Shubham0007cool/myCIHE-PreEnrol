<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=cihe_enrolments", "root", "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user["password"]) {
        $_SESSION["student_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        header("Location: dashboard.php"); // Or any post-login page
        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='student_login.php';</script>";
    }
}
?>
