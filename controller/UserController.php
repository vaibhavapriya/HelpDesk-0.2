<?php
namespace app\controller;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ .'/../services/Logger.php';
require_once __DIR__ .'/../services/Mailer.php';
require_once __DIR__ .'/../services/Auth.php';

use User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\services\Logger;
use app\services\Mailer;
use app\services\Auth;

class UserController {
    private $db;
    private $userModel;
    private $logger;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->logger = new Logger($db);
    }
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $errors = [];

            if (empty($email)) {
                $errors['email_error'] = "Enter your email.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email_error'] = "Invalid email format.";
            }

            if (empty($password)) {
                $errors['password_error'] = "Enter your password.";
            }

            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'message' => $errors]);
                exit;
            }
            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $payload = [
                    "iss" => "localhost",
                    "iat" => time(),
                    "exp" => time() + (60 * 60),
                    "sub" => $user['userid'],
                    "email" => $user['email'],
                    "role" => $user['role']
                ];
                $jwt_secret = "your_secret_key";
                $jwt = JWT::encode($payload, $jwt_secret, "HS256");

                $_SESSION['jwt_token'] = $jwt;
                $_SESSION['role']= $user['role'];

                echo json_encode(['status' => 'success', 'message' => $jwt,'role' => $user['role']]);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Invalid email or password.']]);
                exit;
            }
        }
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = htmlspecialchars(trim($_POST["user"]));
            $email = htmlspecialchars(trim($_POST["email"]));
            $password = trim($_POST["password"]);
            $password1 = trim($_POST["password1"]);
        
            // Error handling
            $errors = [];
        
            if (empty($username)) {
                $errors['user_error'] = "Enter your name.";
            }
            if (empty($email)) {
                $errors['email_error'] = "Enter your email.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email_error'] = "Invalid email format.";
            }
            if (empty($password)) {
                $errors['password_error'] = "Enter your password.";
            }
            if ($password !== $password1) {
                $errors['error']="Passwords do not match";
            }
        
            // If there are errors, redirect back with errors in GET parameters
            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'message' => $errors]);
                exit();
            }
            if ($this->userModel->userExists($email)) {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'User already exists.']]);
                exit();
            }

            $success = $this->userModel->registerUser($username, $email, $password);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'User registered successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => ['error' => 'Failed to register user.']]);
            }

            exit();    
        }
    }
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        // Error handling
        $errors = [];

        $email = trim($_POST['email']);

        if (empty($email)) {
            $errors['email_error'] = "Enter your email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_error'] = "Invalid email format.";
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }

        $userid = $this->userModel->findByEmail($email);

        if (!$userid) {
            $errors['error']="Email not registered.";
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }

        // Generate token & expiry
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+3 hours"));

        if (!$this->userModel->storeResetToken($userid, $token, $expires_at)) {
            $logger->log("Failed to store reset token", __FILE__, __LINE__);
            $errors['error']="Failed to generate reset link.";
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }

        // Compose email
        $reset_link = "http://localhost/HelpDesk-0.2/resetPassword?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password:\n\n$reset_link\n\nThis link is valid for 3 hours.";

        if (Mailer::send($email, $subject, $message)) {
            echo json_encode(['status' => 'success', 'message' => "Password reset link sent! Check your email."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => ['error' => "Failed to send email. Try again later."]]);
        }

        exit();
    }
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $email = trim($_POST['email']);
        $newPassword = trim($_POST['password']);
        $confirmPassword = trim($_POST['password1']);
        $token = trim($_POST['token']);
    
        $errors = [];
    
        // Token validation
        if (empty($token)) {
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Invalid or missing reset token.']]);
            exit();
        }
    
        // Email validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_error'] = "Enter a valid email.";
        }
    
        // Password validation
        if (empty($newPassword)) {
            $errors['password_error'] = "Enter your password.";
        }
    
        if ($newPassword !== $confirmPassword) {
            $errors['match_error'] = "Passwords do not match.";
        }
    
        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }
    
        // Verify token
        $userData = $this->userModel->verifyResetToken($token, $email);
    
        if (!$userData) {
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Invalid or expired reset token.']]);
            exit();
        }
    
        // Check token expiry
        if (strtotime($userData['expiry']) < time()) {
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Token has expired. Please request a new one.']]);
            exit();
        }
    
        // Hash and update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $this->userModel->updatePassword($userData['userid'], $hashedPassword);
    
        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => ['error' => 'Failed to update password. Try again.']]);
        }
    
        exit();
    }
    public function profile() {
        $userid = Auth::id();
        $profile = $this->userModel->getProfile($userid);
        if ($profile) {
            echo json_encode(['status' => 'success', 'data' => $profile]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
        exit();
    }
    public function profileChange(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userId = Auth::id();
            $name = trim($_POST["name"]);
            $email = trim($_POST["email"]);
            $phone = trim($_POST["phone"]);

            if (empty($name) || empty($email) || empty($phone)) {
                header("Location: /project/profile?error=" . urlencode("All fields are required!"));
                echo json_encode(['status' => 'error', 'message' => "All fields are required!"]);
                exit();
            }

            $success = $this->userModel->updateProfile($userId, $name, $email, $phone);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => "Profile updated successfully!"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error updating profile."]);
            }
        }
    }
    public function passwordChange(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userId = Auth::id();
            $oldPassword = trim($_POST["old_password"]);
            $newPassword = trim($_POST["new_password"]);
            $confirmPassword = trim($_POST["confirm_password"]);

            if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode(['status' => 'error', 'message' => "All fields are required!"]);
                exit();
            }

            if ($newPassword !== $confirmPassword) {
                echo json_encode(['status' => 'error', 'message' => "Passwords do not match!"]);
                exit();
            }

            $hashedPassword = $this->userModel->getPasswordByUserId($userId);

            if (!password_verify($oldPassword, $hashedPassword)) {
                echo json_encode(['status' => 'error', 'message' => "Old password is incorrect!"]);
                exit();
            }

            $success = $this->userModel->passwordChange($userId, $newPassword);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => "Password changed successfully!"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error updating password."]);
            }
        }
    }
    
}
