<?php
namespace app\services;

class Logger {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function log($message, $file, $line) {
        if (!$this->conn) {
            error_log("Database connection error.");
            return;
        }

        $stmt = $this->conn->prepare("INSERT INTO error_logs (error_message, error_file, error_line) VALUES (?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed: " . implode(", ", $this->conn->errorInfo()));
            return;
        }

        $stmt->bindValue(1, $message, \PDO::PARAM_STR);
        $stmt->bindValue(2, $file, \PDO::PARAM_STR);
        $stmt->bindValue(3, $line, \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            error_log("Execute failed: " . implode(", ", $stmt->errorInfo()));
        }
    }
}
