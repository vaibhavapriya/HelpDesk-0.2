<?php
namespace app\services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php'; // Load PHPMailer & Dotenv

class Mailer {
        // Function to dynamically select the "From" email based on subject or other factors
        public function setSenderEmail($subject)
        {
            // Example conditions based on the subject
            if (strpos($subject, 'Sales') !== false) {
                $this->companyEmail = 'sales@company.com';
            } elseif (strpos($subject, 'Support') !== false) {
                $this->companyEmail = 'support@company.com';
            } elseif (strpos($subject, 'HR') !== false) {
                $this->companyEmail = 'hr@company.com';
            } else {
                $this->companyEmail = 'info@company.com'; // Default email
            }
        }
    public static function send($toEmail, $subject, $message) {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Details
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Support Team');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br(htmlspecialchars($message));
            $mail->AltBody = strip_tags($message);

            return $mail->send();
        } catch (Exception $e) {
            error_log("Email exception: " . $e->getMessage());
            return false;
        }
    }
}
