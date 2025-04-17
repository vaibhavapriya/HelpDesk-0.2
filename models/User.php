<?php
require_once __DIR__ . '/../services/Logger.php';
use app\services\Logger;
class User {
    private $db;
    private $logger; 

    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
    }

    public function userExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT userid FROM user WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT password FROM user WHERE userid = ?");
            $query = "SELECT userid FROM user WHERE email = :email";
            $stmt = $this->db->prepare($query);
            if (!$stmt) return false;
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) return false;
    
            return $result['userid'];
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT userid, email, password, role FROM user WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function storeResetToken($userid, $token, $expires_at) {
        try {
            $query = "UPDATE user SET reset_token = :token, reset_token_expiry = :expiry WHERE userid = :userid";
            $stmt = $this->db->prepare($query);
            if (!$stmt) return false;
    
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':expiry', $expires_at, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function verifyResetToken($token, $email) {
        try {
            $query = "SELECT userid, reset_token_expiry FROM user WHERE reset_token = :token AND email = :email";
            $stmt = $this->db->prepare($query);
            if (!$stmt) return false;
    
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) return false;
    
            return [
                'userid' => $result['userid'],
                'expiry' => $result['reset_token_expiry']
            ];
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function registerUser($username, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, 'client')");
            return $stmt->execute([$username, $email, $hashedPassword]);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function updatePassword($userid, $hashedPassword) {
        try {
            $query = "UPDATE user SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE userid = :userid";
            $stmt = $this->db->prepare($query);
            if (!$stmt) return false;
    
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function getProfile($userid){
        try {
            $stmt = $this->db->prepare("SELECT name, email, phone FROM user WHERE userid = ?");
            $stmt->execute([$userid]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }

    public function getPasswordByUserId($userId) {
        try {
            $stmt = $this->db->prepare("SELECT password FROM user WHERE userid = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $result ? $result['password'] : null;
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return null;
        }
    }
    
    public function updateProfile($userId, $name, $email, $phone) {
        try {
            $stmt = $this->db->prepare("UPDATE user SET name = ?, email = ?, phone = ? WHERE userid = ?");
            return $stmt->execute([$name, $email, $phone, $userId]);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
    
    public function passwordChange($userId, $newPassword) {
        try {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE userid = ?");
            return $stmt->execute([$newHashedPassword, $userId]);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
}

