<?php
namespace app\services;

require_once __DIR__ .'/../services/Auth.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Auth;

class AuthRole {
    private $jwtSecret = 'your_secret_key'; // replace with your real secret

    public function handle() {
        $headers = $this->getBearerToken();
        if (!$headers) {
            http_response_code(401);
            echo "Unauthorized: No token provided";
            exit();
        }

        try {
            $decoded = JWT::decode($headers, new Key($this->jwtSecret, 'HS256'));
            $role = $decoded->role ?? null;

            if ($role !== 'admin') {
                header("Location: /HelpDesk2/home?error=" . urlencode("You have no access!"));
                exit();
            }

            // You can also store role in session if needed:
            $_SESSION['role'] = $role;

        } catch (Exception $e) {
            http_response_code(401);
            echo "Unauthorized: Invalid token";
            exit();
        }
    }
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