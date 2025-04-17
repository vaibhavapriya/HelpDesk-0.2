<?php
namespace app\services;
require_once __DIR__ .'/../services/Auth.php';
use app\services\Auth;

class CheckTicket {

    public function handle() {

        $requester_id =  Auth::id();

        $stmt = $this->conn->prepare("SELECT requester_id FROM tickets WHERE id = ?");
        if (!$stmt) {
            throw new \Exception("Statement preparation failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $ticket_id);
        if (!$stmt->execute()) {
            throw new \Exception("Execution failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $ticket = $result->fetch_assoc();

        $stmt->close();

        if (!$ticket || $ticket['requester_id'] != $requester_id) {
            http_response_code(403);
            header("Location: /project/myTickets?error=Access Denied");
            die(json_encode(["error" => "Access Denied"]));
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
