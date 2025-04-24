<?php
namespace app\controller;

require_once __DIR__ . '/../models/MailConfig.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ .'/../services/Logger.php';
require_once __DIR__ .'/../services/Mailer.php';
require_once __DIR__ .'/../services/Auth.php';
require_once __DIR__ .'/../services/Emailhandler.php';

use app\models\MailConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Logger;
use app\services\Mailer;
use app\services\Auth;
use app\services\Emailhandler;

class MailController {
    private $db;
    private $mailConfigModel;
    private $logger;

    public function __construct($db) {
        $this->db = $db;
        $this->mailConfigModel = new MailConfig($db);
        $this->logger = new Logger($db);
    }
        // Fetch all mail configurations
    public function fetchMailConfigs() {
        try {
            $mailConfigs = $this->mailConfigModel->getAll();
            if($mailConfigs){
                echo json_encode(['status' => 'success', 'data' => $mailConfigs]);
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Failed to fetch mails']]);
            }
        } catch (\PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);

        }
    }

    // Add a new mail config
    public function addMailConfig() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $email=trim($_POST["email"] ?? '');
        $name=trim($_POST["name"] ?? '');
        $passcode=trim($_POST["passcode"] ?? '');
        try {
            $add=$this->mailConfigModel->insertMailConfig($email, $name, $passcode);
            if($add){
                echo json_encode(['status' => 'success', 'message' => 'Mail added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
            }
            
        } catch (\PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            echo json_encode(['status' => 'error', 'message' => ['error' =>'Failed to fetch mails' ]]);
        }
    }

    // Delete a mail configuration
    public function deleteMailConfig() {
        // Ensure the request method is DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            // Handle invalid request method
            return false;
        }

        // Get the `id` from the URL query parameters
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = $_GET['id']; // This is the ID you need
        } else {
            // Handle invalid or missing id
            return false;
        }

        try {
            // Assuming $this->mailConfigModel->deleteMailConfig($id) deletes the mail configuration
            $result = $this->mailConfigModel->deleteMailConfig($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Mail deleted successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
            }
        } catch (\PDOException $e) {
            // Log the error and return failure
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            echo json_encode(['status' => 'error', 'message' => ['error' =>'Failed to delete mail' ]]);
        }
    }
    public function activateEmail() {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
    
        if (!$id || !is_numeric($id)) {
            echo json_encode(['status' => 'error', 'message' =>  ['error' =>'Invalid email ID']]);
            return;
        }
    
        try {
            $result=$this->mailConfigModel->setActive($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Changed Active Mail']);
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Database error.']]);
            }
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            echo json_encode(['status' => 'error', 'message' => 'Failed to set active']);
        }
    }
    
}