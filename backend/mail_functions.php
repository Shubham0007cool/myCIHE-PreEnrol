<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '8baad75d5aa414';
        $mail->Password = '5bbd106aafb08c';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@cihe.edu.au', 'CIHE System');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

function sendWelcomeEmail($email, $first_name, $student_id) {
    $subject = "Welcome to CIHE";
    $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Welcome to Crown Institute of Higher Education!</h2>
            <p>Dear $first_name,</p>
            <p>Your registration has been successful. Here are your account details:</p>
            <p><strong>Student ID:</strong> $student_id</p>
            <p>You can now log in to your account using your email and password.</p>
            <p>If you have any questions, please don't hesitate to contact us.</p>
            <p>Best regards,<br>CIHE Team</p>
        </div>";
    
    return sendEmail($email, $subject, $body);
}

function sendResetEmail($email, $token) {
    $subject = 'Password Reset Request';
    $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p>Hello,</p>
            <p>We received a request to reset your password. Click the button below to reset your password:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='http://localhost/cihe/reset_password.php?token=$token' 
                   style='background-color: #4CAF50; color: white; padding: 12px 24px; 
                   text-decoration: none; border-radius: 4px; display: inline-block;'>
                    Reset Password
                </a>
            </div>
            <p>This link will expire in 24 hours.</p>
            <p>If you didn't request this password reset, you can safely ignore this email.</p>
            <p>Best regards,<br>CIHE Team</p>
        </div>";
    
    return sendEmail($email, $subject, $body);
}
?> 