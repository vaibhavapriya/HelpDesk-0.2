<?php
namespace app\middlewares;

use \Firebase\JWT\JWT;
use \Exception;

class JWTMiddleware {
    
    private $jwtSecret = "your_secret_key"; // Define your secret key here.
    public $token;
    public $user_id;
    public $email;
    public $role;

    // Function to handle the JWT authentication.
    public function handle() {
        $headers = $this->getBearerToken();

        if (!$headers) {
            header("Location: /project/login?error=" . urlencode("Authorization token is missing!"));
            exit();
        }

        try {
            $decoded = JWT::decode($headers, $this->jwtSecret, ['HS256']);
            
            // Store user information in the session
            $token = $headers;
            $user_id = $decoded->sub;
            $email = $decoded->email;
            $role = $decoded->role;

        } catch (Exception $e) {
            header("Location: /project/login?error=" . urlencode("Invalid token. Please log in again."));
            exit();
        }
    }

    // Function to get the Bearer token from the Authorization header
    private function getBearerToken() {
        $headers = null;
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
