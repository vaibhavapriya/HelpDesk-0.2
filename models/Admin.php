<?php
require_once __DIR__ . '/../services/Logger.php';
use app\services\Logger;
class Admin {
    private $db;
    private $logger;
    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
    }
    public function getErrorlog(){
        
        try {
            $query ="SELECT id, error_message, error_file, error_line, created_at FROM error_logs ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
    public function getUsers(){
        
        try {
            $query ="SELECT userid, email FROM user ORDER BY email ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
    public function getUserinfo($search = null) {
        try {
            if ($search) {
                $stmt = $this->db->prepare("SELECT userid, name, role, email, phone FROM user WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search");
                $stmt->execute(['search' => "%$search%"]);
            } else {
                $stmt = $this->db->query("SELECT userid, name, role, email, phone FROM user");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
    public function updateUser($userid, $name, $role, $email, $phone) {
        try {
            $query = "UPDATE user SET name = :name, role = :role, email = :email, phone = :phone WHERE userid = :userid";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                'name' => $name,
                'role' => $role,
                'email' => $email,
                'phone' => $phone,
                'userid' => $userid
            ]);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    public function deleteUser($userid) {
        try {
            $stmt = $this->db->prepare("DELETE FROM user WHERE userid = ?");
            return $stmt->execute([$userid]);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

}