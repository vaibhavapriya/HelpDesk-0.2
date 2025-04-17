<?php
require_once __DIR__ . '/../services/Logger.php';
use app\services\Logger;
class Ticket {
    private $db;
    private $table = "tickets";
    private $logger; 
    private $logs = ''; 
    

    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
    }
    public function create($requester_id, $requester, $subject, $priority, $topic, $description, $logs, $attachment, $attachmentType)
    {
        try {
            $logs = json_encode(json_decode($logs, true), JSON_UNESCAPED_UNICODE);
            $query = "INSERT INTO {$this->table} 
                    (requester_id, requester, subject, priority, topic, description, logs, attachment, attachment_type) 
                    VALUES (:requester_id, :requester, :subject, :priority, :topic, :description, :logs, :attachment, :attachment_type)";

            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                ':requester_id'     => $requester_id,
                ':requester'        => $requester,
                ':subject'          => $subject,
                ':priority'         => $priority,
                ':topic'            => $topic,
                ':description'      => $description,
                ':logs'             => $logs,
                ':attachment'       => $attachment,
                ':attachment_type'  => $attachmentType
            ]);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }

    public function getTicketsByEmail($email) {
        try {
            $query = "SELECT id, subject, requester, last_replier, status, last_activity 
                      FROM {$this->table} 
                      WHERE requester = :email 
                      ORDER BY last_activity DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }
    public function getTickets() {
        try {
            $query = "SELECT id, subject, requester, last_replier, status, last_activity 
                      FROM {$this->table} 
                      ORDER BY last_activity DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }

}