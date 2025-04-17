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

    public function getTickets($status = null, $search = null) {
        try {
            $sql = "SELECT id, subject, requester, priority, last_replier, status, last_activity 
                    FROM {$this->table} 
                    WHERE 1"; // Always true, allows conditional chaining

            $params = [];

            // Status filter (Open, In Progress, Closed)
            if (!empty($status) && strtolower($status) !== 'all') {
                $sql .= " AND LOWER(status) = :status";
                $params[':status'] = strtolower($status);
            }

            // Search by subject, id, requester name
            if (!empty($search)) {
                $sql .= " AND (
                    LOWER(subject) LIKE :search 
                    OR CAST(id AS CHAR) LIKE :search
                    OR LOWER(requester) LIKE :search
                )";
                $params[':search'] = '%' . strtolower($search) . '%';
            }

            $sql .= " ORDER BY last_activity DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function getTicketByIdAndEmail($ticketId, $email) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, subject, priority, topic, description, last_replier, status, last_activity, requester, attachment 
                FROM tickets 
                WHERE id = :id
            ");
    
            $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
    
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$ticket) return null;
            if ($ticket['requester'] !== $email) return 'unauthorized';
    
            return $ticket;
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    public function getAttachmentById($ticketId) {
        try {
            $query = "SELECT attachment, attachment_type FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    
}