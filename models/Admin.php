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

}