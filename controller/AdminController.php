<?php
namespace app\controller;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ .'/../services/Logger.php';
require_once __DIR__ .'/../services/Mailer.php';
require_once __DIR__ .'/../services/Auth.php';

use User;
use Admin;
use Ticket;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Logger;
use app\services\Mailer;
use app\services\Auth;

class AdminController {

    private $db;
    private $ticketModel;
    private $adminModel;
    private $logger;

    public function __construct($db) {
        $this->db = $db;
        $this->ticketModel = new Ticket($db);
        $this->adminModel = new Admin($db);
        $this->logger = new Logger($db);
    }
    public function tickets(){
        // if (!isset($_SESSION['email'])) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        //     exit();
        // }

        $email = Auth::email();
        $tickets = $this->ticketModel->getTickets();

        if ($tickets === false) {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $tickets]);
        }
        exit();
    }
    public function errorlogs(){
        $errorlog = $this->adminModel->getErrorlog();
        if ($errorlog === false) {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $errorlog]);
        }
        exit();
    }
    public function requesters(){
        $errorlog = $this->adminModel->getUsers();
        if ($errorlog === false) {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $errorlog]);
        }
        exit();
    }   
    public function submitTicket(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        // Error handling
        // $decoded = JWT::decode($jwt, new Key($this->jwtSecret, "HS256"));
        // $requester_id = $decoded->sub;
        $requester_id= trim($_POST["requester_id"] ?? '');
        $requester= trim($_POST["requester"] ?? '');
        $errors = []; 
        $subject = trim($_POST["subject"] ?? '');
        $priority = trim($_POST["priority"] ?? '');
        $topic = trim($_POST["topic"] ?? '');
        $description = trim($_POST["description"] ?? '');

        if (empty($subject)) $errors['subject_error'] = "Enter a subject.";
        if (empty($priority)) $errors['priority_error'] = "Select a priority.";
        if (empty($topic)) $errors['topic_error'] = "Enter a topic.";
        if (empty($description)) $errors['description_error'] = "Enter a description.";

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }
                // Handle attachment
        $attachment     = null;
        $attachmentType = null;

        if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fileType = strtolower(pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

            if (in_array($fileType, $allowedTypes)) {
                $attachment     = base64_encode(file_get_contents($_FILES["attachment"]["tmp_name"]));
                $attachmentType = $_FILES["attachment"]["type"];
            } else {
                echo json_encode(['status' => 'error', 'message' => ['attachment' => 'Invalid file type.']]);
                exit();
            }
        } elseif ($_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
            // error_log("Upload fail: " . $_FILES['attachment']['error']);
        }
        
        $logs = json_encode([]);
    
        // Save ticket to database
        if ($this->ticketModel->create($requester_id, $requester, $subject, $priority, $topic, $description, $logs, $attachment, $attachmentType)) {
            // Send confirmation mail
            $message = "<p>Thank you for your interest, <b>$requester</b>. We have received your ticket:</p><blockquote>$subject</blockquote><p>We will contact you soon.</p>";
            $subjectMail = "Ticket Received - We will contact you soon!";

            Mailer::send($requester, $subjectMail, $message);

            echo json_encode(['status' => 'success', 'message' => 'Ticket submitted successfully!']);
        } else {
            // error_log("Ticket creation failed", 0);
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
        }

        exit();
    }
}