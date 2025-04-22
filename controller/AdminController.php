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
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
    
        $tickets = $this->ticketModel->getTickets($status, $search);
    
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
        
    
        // Save ticket to database
        if ($this->ticketModel->create($requester_id, $requester, $subject, $priority, $topic, $description, $attachment, $attachmentType)) {
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
    public function users(){
        $search = isset($_GET['search']) ? $_GET['search'] : null;
    
        $users = $this->adminModel->getUserinfo($search);
    
        if ($users === false) {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $users]);
        }
        exit();
    }
    public function editUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            exit();
        }
    
        // Decode JSON from frontend
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
            exit();
        }
    
        $userid = $input['userid'] ?? null;
        $name   = trim($input['name'] ?? '');
        $role   = trim($input['role'] ?? '');
        $email  = trim($input['email'] ?? '');
        $phone  = trim($input['phone'] ?? '');
    
        if (!$userid || !$name || !$role || !$email || !$phone) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit();
        }
    
        // Call the model function
        $updated = $this->adminModel->updateUser($userid, $name, $role, $email, $phone);
        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
        }
        exit();
    }
    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            exit();
        }
    
        $userid = $_GET['id'] ?? null;
        if (!$userid) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
            exit();
        }
    
        $deleted = $this->adminModel->deleteUser($userid);
        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
        }
        exit();
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
    public function replyTicket(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id= $_POST['ticketId'] ?? $_POST['id'] ?? null;
        $subject = $_POST['subject'] ?? '';
        $reply = $_POST['reply'] ?? '';
        $priority = $_POST['priority'] ?? null;
        $status = $_POST['status'] ?? null;
        $topic = $_POST['topic'] ?? null;
        $description = $_POST['description'] ?? null;

        if (!$id || empty(trim($reply))) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ticket ID and reply are required.'
            ]);
            return;
        }

        $attachment = null;
        $attachmentType = null;
        $last_replier= Auth::email();

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

        $result = $this->ticketModel->updateReply($id, $last_replier, $subject, $priority, $status, $topic, $description, $reply, $attachment, $attachmentType);
    
        if($result){
            echo json_encode(['status' => 'success', 'message' => 'Ticket submitted successfully!']);
        }else{
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
        }
        // // Save ticket to database
        // if ($this->ticketModel->create($requester_id, $requester, $subject, $priority, $topic, $description, $attachment, $attachmentType)) {
        //     // Send confirmation mail
        //     $message = "<p>Thank you for your interest, <b>$requester</b>. We have received your ticket:</p><blockquote>$subject</blockquote><p>We will contact you soon.</p>";
        //     $subjectMail = "Ticket Received - We will contact you soon!";

        //     Mailer::send($requester, $subjectMail, $message);

        //     echo json_encode(['status' => 'success', 'message' => 'Ticket submitted successfully!']);
        // } else {
        //     // error_log("Ticket creation failed", 0);
        //     echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
        // }

        exit();
    }
}