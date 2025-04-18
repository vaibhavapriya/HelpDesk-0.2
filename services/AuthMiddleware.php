<?php
namespace app\services;

require_once __DIR__ .'/../services/Auth.php';
require_once __DIR__ . '/../services/Logger.php';
use app\services\Logger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Auth;

class AuthMiddleware {
    private $jwtSecret = "your_secret_key"; 
    private $logger; 
    public $token;
    public $user_id;
    public $email;
    public $role;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
    }
    // Function to handle the JWT authentication.
    public function handle() {
        $headers = $this->getBearerToken();

        if (!$headers) {
            header("Location: /HelpDesk-0.2/login?error=" . urlencode("Authorization token is missing!"));
            exit();
        }

        try {
            $decoded = JWT::decode($headers, new Key($this->jwtSecret, 'HS256'));
            Auth::set([
                'id' => $decoded->sub,
                'email' => $decoded->email,
                'role' => $decoded->role
            ]);

        } catch (ExpiredException $e) {
            // Token is expired — redirect to login
            session_destroy(); // optional: clear old session
            echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
            exit();
        } catch (Exception $e) {
            header("Location: /HelpDesk-0.2/login?error=" . urlencode("Session Expired"));
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            exit();
        }
    }

    // Function to get the Bearer token from the Authorization header
    private function getBearerToken() {
        $headers = null;

        // Try getting it from apache_request_headers if available
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            } elseif (isset($requestHeaders['authorization'])) {
                $headers = trim($requestHeaders['authorization']);
            }
        }   

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        }

        // If the Authorization header is present, extract the Bearer token
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null; // Return null if the token is missing
    }
    
}
