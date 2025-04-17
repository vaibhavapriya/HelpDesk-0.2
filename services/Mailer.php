<?php
namespace app\services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/Exception.php';

class Mailer {
    public static function send($toEmail, $subject, $message) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vaibhavapriya39@gmail.com';
            $mail->Password = 'qpfp rdqd ltzz hudq'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('vaibhavapriya39@gmail.com', 'Support Team');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = strip_tags($message);
            $mail->AltBody = "<html><body><p>" . nl2br(htmlspecialchars($message)) . "</p></body></html>";

            if ($mail->send()) {
                return true;
            } else {
                $logger->log("Email send failed: " . $mail->ErrorInfo, __FILE__, __LINE__);
                return false;
            }
    
        } catch (Exception $e) {
            $logger->log("Email exception: " . $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
}
