<?php
namespace db;

use mysqli; 

require 'vendor/autoload.php'; 
use \Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();  // Load environment variables from .env file

class DatabaseConnection {

    private $host;
    private $user;
    private $pass;
    private $db;
    public $conn;

    public function __construct() {
        $this->db_connect();
    }

    private function db_connect() {
        $this->host = $_ENV['DATABASE_HOST'];
        $this->user = $_ENV['DATABASE_USER'];
        $this->pass = $_ENV['DATABASE_PASSWORD'];
        $this->db   = $_ENV['DATABASE_NAME'];

        $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->db);

        // Check for connection errors
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
?>