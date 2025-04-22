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
    public function create($requester_id, $requester, $subject, $priority, $topic, $description, $attachment, $attachmentType)
    {
        try {
            $query = "INSERT INTO {$this->table} 
                    (requester_id, requester, subject, priority, topic, description, attachment, attachment_type) 
                    VALUES (:requester_id, :requester, :subject, :priority, :topic, :description, :attachment, :attachment_type)";

            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                ':requester_id'     => $requester_id,
                ':requester'        => $requester,
                ':subject'          => $subject,
                ':priority'         => $priority,
                ':topic'            => $topic,
                ':description'      => $description,
                ':attachment'       => $attachment,
                ':attachment_type'  => $attachmentType
            ]);
        } catch (PDOException $e) {
            $this->logger->log( $e->getMessage(),__FILE__,__LINE__);
            return false;
        }
    }

    public function getTicketsByEmail($email, $status = null) {
        try {
            $sql = "SELECT id, subject, requester, last_replier, status, last_activity 
                    FROM {$this->table} 
                    WHERE requester = :email";
            
            $params = [':email' => $email];
    
            // Add status filter if not 'all' or empty
            if (!empty($status) && strtolower($status) !== 'all') {
                $sql .= " AND LOWER(status) = :status";
                $params[':status'] = strtolower($status);
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
                SELECT id, subject, priority, topic, description, last_replier, status, last_activity, requester,reply, attachment_type 
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
    public function getTicketById($ticketId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, subject, priority, topic, description, last_replier, status, last_activity, requester, attachment_type, reply 
                FROM tickets 
                WHERE id = :id
            ");
        
            $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
        
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$ticket) return null;
        
            return $ticket;
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    public function updateReply($id, $last_replier, $subject, $priority, $status, $topic, $description, $reply, $attachment = null, $attachmentType = null)
    {
        try {
            $fields = [
                'subject' => $subject,
                'priority' => $priority,
                'status' => $status,
                'topic' => $topic,
                'description' => $description,
                'reply' => $reply,
                'last_activity' => date('Y-m-d H:i:s'),
                'last_replier' => $last_replier, // update based on your auth
            ];

            if ($attachment && $attachmentType) {
                $fields['attachment'] = $attachment;
                $fields['attachment_type'] = $attachmentType;
            }

            $setParts = [];
            foreach ($fields as $column => $value) {
                $setParts[] = "$column = :$column";
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $fields['id'] = $id;

            return $stmt->execute($fields);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    public function deleteTicket($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tickets WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    public function edit($id, $subject, $priority, $status, $topic, $description, $attachment = null, $attachmentType = null)
    {
        try {
            $fields = [
                'subject' => $subject,
                'priority' => $priority,
                'status' => $status,
                'topic' => $topic,
                'description' => $description,
                'last_activity' => date('Y-m-d H:i:s'),
            ];

            if ($attachment && $attachmentType) {
                $fields['attachment'] = $attachment;
                $fields['attachment_type'] = $attachmentType;
            }

            $setParts = [];
            foreach ($fields as $column => $value) {
                $setParts[] = "$column = :$column";
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $fields['id'] = $id;

            return $stmt->execute($fields);
        } catch (PDOException $e) {
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
}