<?php
namespace app\controller;

require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ .'/../services/Logger.php';
require_once __DIR__ .'/../services/Emailhandler.php';
require_once __DIR__ .'/../services/Auth.php';
use Ticket;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Logger;
use app\services\Mailer;
use app\services\Auth;
use app\services\Emailhandler;

class TicketController{
    private $db;
    private $ticketModel;
    private $logger;
    private $emailHandler;

    public function __construct($db) {
        $this->db = $db;
        $this->ticketModel = new Ticket($db);
        $this->logger = new Logger($db);
    }

    public function submitTicket(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        // Error handling
        // $decoded = JWT::decode($jwt, new Key($this->jwtSecret, "HS256"));
        // $requester_id = $decoded->sub;
        $requester_id= Auth::id();
        $requester= Auth::email();
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
            $this->logger->log("Upload fail: " . $_FILES['attachment']['error'],__FILE__,__LINE__);
            // error_log("Upload fail: " . $_FILES['attachment']['error']);
        }
        
        // $logs = json_encode([]);
        $emailHandler = new EmailHandler($this->db);
        // Save ticket to database
        if ($this->ticketModel->create($requester_id, $requester, $subject, $priority, $topic, $description, $attachment, $attachmentType)) {
            // Send confirmation mail
            $message = "<p>Thank you for your interest, <b>$requester</b>. We have received your ticket:</p><blockquote>$subject</blockquote><p>We will contact you soon.</p>";
            $subjectMail = "Ticket Received - We will contact you soon!";
            $emailHandler->sendEmail($requester, $subjectMail, $message);

            echo json_encode(['status' => 'success', 'message' => 'Ticket submitted successfully!']);
        } else {
            // error_log("Ticket creation failed", 0);
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
        }

        exit();
    }

    public function myTickets(){
        // if (!isset($_SESSION['email'])) {
        //     echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        //     exit();
        // }

        $email = Auth::email();
        $tickets = $this->ticketModel->getTicketsByEmail($email);

        if ($tickets === false) {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'success', 'data' => $tickets]);
        }
        exit();
    }

    public function clientTicket(){
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ticket ID.'], 400);
            return;
        }
        $ticketId = intval($_GET['id']);
        $email = Auth::email();

        if (!$email) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized.'], 401);
            return;
        }

        $ticket = $this->ticketModel->getTicketByIdAndEmail($ticketId, $email);

        if ($ticket === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch ticket.']);
        } elseif ($ticket === 'unauthorized') {
            echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        } elseif ($ticket === null) {
            echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $ticket]);
        }
    }

    public function serveAttachment() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            http_response_code(400);
            die("Bad Request: No image ID provided.");
        }

        $ticketId = intval($_GET['id']);
        $attachment = $this->ticketModel->getAttachmentById($ticketId);

        if (!$attachment || empty($attachment['attachment'])) {
            http_response_code(404);
            die("No image found.");
        }

        $attachmentData = base64_decode($attachment['attachment']);
        $attachmentType = $attachment['attachment_type'];

        header("Content-Type: $attachmentType");
        echo $attachmentData;
        exit;
    }
    public function deleteTicket() {
    
        $ticketId = $_GET['id'] ?? null;
        if (!$ticketId) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
            exit();
        }
        if(Auth::role()==='client'){
            $email = Auth::email();

            if (!$email) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized.'], 401);
                return;
            }
    
            $ticket = $this->ticketModel->getTicketByIdAndEmail($ticketId, $email);
    
            if ($ticket === false) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch ticket.']);
            } elseif ($ticket === 'unauthorized') {
                echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
            } elseif ($ticket === null) {
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
            } else {
                $deleted = $this->ticketModel->deleteTicket($ticketId);
                if ($deleted) {
                    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
                }
                exit();
            }
        }
        if(Auth::role()==='admin'){
            $deleted = $this->ticketModel->deleteTicket($ticketId);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
            }
            exit();
        }
        echo json_encode(['status' => 'error', 'message' => Auth::role()]);
    }
    public function ticket(){
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ticket ID.'], 400);
            return;
        }
        $ticketId = intval($_GET['id']);

        $ticket = $this->ticketModel->getTicketById($ticketId);

        if ($ticket === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch ticket.']);
        } elseif ($ticket === 'unauthorized') {
            echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        } elseif ($ticket === null) {
            echo json_encode(['status' => 'error', 'message' => 'Ticket not found.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $ticket]);
        }
    }
    public function editTicket(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id= $_POST['ticketId'] ?? $_POST['id'] ?? null;
        $subject = $_POST['subject'] ?? '';
        $reply = $_POST['reply'] ?? '';
        $priority = $_POST['priority'] ?? null;
        $status = $_POST['status'] ?? null;
        $topic = $_POST['topic'] ?? null;
        $description = $_POST['description'] ?? null;

        if (!$id ) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ticket ID is required.'
            ]);
            return;
        }

        $attachment = null;
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
            $this->logger->log("Upload fail: " . $_FILES['attachment']['error'],__FILE__,__LINE__);
            // error_log("Upload fail: " . $_FILES['attachment']['error']);
        }

        $result = $this->ticketModel->edit($id, $subject, $priority, $status, $topic, $description, $attachment, $attachmentType);
    
        if($result){
            echo json_encode(['status' => 'success', 'message' => 'Ticket submitted successfully!']);
        }else{
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
        }

        exit();
    }
    
}