<?php

namespace model;

require_once("config.php");

class Login extends \db\DatabaseConnection
// class Login extends DatabaseConnection
{
    
    public function getUser($email, $password)
    {
        header('Content-Type: application/json');
            $query = "SELECT * FROM employees 
              WHERE employees.email = ? AND employees.password = ?";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $login = mysqli_fetch_assoc($result);
                 return $login;
        }
    }
}

