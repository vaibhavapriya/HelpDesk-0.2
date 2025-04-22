<?php
namespace app\services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO; 
require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__.'/../vendor/phpmailer/phpmailer/src/Exception.php';
class EmailHandler
{
    private $mailer;
    private $db;
    private $logger;

    public function __construct($db)
    {
        $this->mailer = new PHPMailer(true);
        $this->db = $db; // PDO object passed during initialization
        $this->logger = new Logger($db);
    }


    // Determine sender email based on subject
    public function setSenderEmail()
    {
        $stmt = $this->db->prepare("SELECT email FROM smtp_config WHERE active = 1 LIMIT 1");
        $stmt->execute();
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['email'] : null;
    }

    // Fetch SMTP config from the database for the given email
    private function getSmtpConfigFromDB($email='vaibhavapriya39@gmail.com')
    {
        $stmt = $this->db->prepare("SELECT * FROM smtp_config WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function sendEmail($to, $subject, $body,  $fromEmail = null)
    {
        $fromEmail = $fromEmail ?: $this->setSenderEmail();
        $smtp = $this->getSmtpConfigFromDB($fromEmail);
        if (!$smtp) {
            return "No SMTP config found for $fromEmail";
        }

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $smtp['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $smtp['email'];
            $this->mailer->Password =$smtp['password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $smtp['port'];

            $this->mailer->setFrom($smtp['email'], $smtp['name']);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);

            if ($this->mailer->send()) {
                return "Email sent from ".$smtp['name'];
            } else {
                $this->logger->log("Email send failed: " . $mail->ErrorInfo, __FILE__, __LINE__);
                return "Email could not be sent.";
            }
        } catch (Exception $e) {
            $this->logger->log("Email exception: " . $e->getMessage(), __FILE__, __LINE__);
            return "Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }
}
