<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'leelija_db';
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

class DatabaseConnection{

    private $host;
    private $user;
    private $pass;
    private $db;

    public $conn;

    public function __construct() {

        $this->db_connect();

      }

    function db_connect(){

        $this->host = 'localhost';
        $this->user = 'root';
        $this->pass = '';
        $this->db   = 'leelija_db';

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        return $this->conn;

    }

}
?>