<?php
namespace app\models;

use app\services\Logger;

class MailConfig {
    private $db;
    private $logger;

    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
    }

    // Get all email configurations
    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT id, email, name, active FROM smtp_config");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    // Insert a new email configuration
    public function insertMailConfig($email, $name, $passcode) {
        $query = "INSERT INTO smtp_config (email, name, password, host, port, username) VALUES (:email, :name, :passcode, :host, :port, :username)";
        $host = 'smtp.mailtrap.io';
        $port = 2525;
        // Prepare the statement
        $stmt = $this->db->prepare($query);
        
        // Bind the parameters
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $email);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':passcode', $passcode);
        $stmt->bindParam(':host', $host);
        $stmt->bindParam(':port', $port);// Default port for mailtrap.io or use dynamic if needed
        
        // Execute the query
        try {
            $stmt->execute();
            return true;
        } catch (\PDOException $e)  {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    // Delete an email configuration
    public function deleteMailConfig($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM smtp_config WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    public function setActive($id) {
        try {
            // Set all to inactive
            $this->db->exec("UPDATE smtp_config SET active = 0");
            
            // Activate the selected one
            $stmt = $this->db->prepare("UPDATE smtp_config SET active = 1 WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (\PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
}
