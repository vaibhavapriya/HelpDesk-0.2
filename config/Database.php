<?php
class Database {
  private $servername = "localhost";
  private $dbname = "supportdesk";
  private $username = "ad";
  private $password = "yb8y8nlE]d1eSR8]";
  private $conn = null;

  public function connect() {
      try {
          $this->conn = new PDO(
              "mysql:host={$this->servername};dbname={$this->dbname}",
              $this->username,
              $this->password
          );
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
          echo "Connection failed: " . $e->getMessage();
      }
      return $this->conn;
  }
}

$database = new Database();
$db = $database->connect();


