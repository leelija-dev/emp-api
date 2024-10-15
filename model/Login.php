<?php

namespace model;

require_once("config.php");

class Login extends \db\DatabaseConnection
// class Login extends DatabaseConnection
{
    //GET EMPLOYEE DETAILS USING EMPLOYEE ID
    // public function getEmployeeDetails($id)
    // {
    //     header('Content-Type: application/json');

    //     try {
    //         $query = "SELECT employees.*, emp_docs.* 
    //           FROM employees 
    //           LEFT JOIN emp_docs ON employees.emp_id = emp_docs.emp_id 
    //           WHERE employees.emp_id = ?";

    //         $stmt = mysqli_prepare($this->conn, $query);

    //         if ($stmt) {

    //             $stmt->bind_param('i', $id);
    //             $stmt->execute();

    //             $result = mysqli_stmt_get_result($stmt);
    //             $employeeDetails = mysqli_fetch_assoc($result);

    //             if ($employeeDetails) {
    //                 $response = array(
    //                     'success' => true,
    //                     'message' => 'Employee and Document Details Fetched successfully',
    //                     'data' => $employeeDetails
    //                 );
    //                 echo json_encode($response);
    //                 die();
    //             } else {
    //                 $response = array(
    //                     'success' => false,
    //                     'message' => 'Failed to fetch details'
    //                 );
    //                 echo json_encode($response);
    //                 die();
    //             }
    //         } else {
    //             throw new \Exception('Failed to prepare the statement');
    //         }
    //     } catch (\mysqli_sql_exception $e) {
    //         // Log error for debugging
    //         error_log("Database error: " . $e->getMessage());

    //         // Return failure response to the user
    //         $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
    //         echo json_encode($response);
    //     } catch (\Exception $e) {
    //         // Handle any other general exceptions
    //         error_log("General error: " . $e->getMessage());

    //         // Return failure response to the user
    //         $response = array('success' => false, 'message' => 'An unexpected error occurred');
    //         echo json_encode($response);
    //     }
    //     // else {
    //     //     $response = array(
    //     //         'success' => false,
    //     //         'message' => 'There is an error'
    //     //     );
    //     //     echo json_encode($response);
    //     //     die();
    //     // }
    // }

    public function getUser($email, $password)
    {
        header('Content-Type: application/json');
     //   try {
            $query = "SELECT * FROM employees 
              WHERE employees.email = ? AND employees.password = ?";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $login = mysqli_fetch_assoc($result);
                 return $login;
    //             if ($login) {
    //                 $response = array(
    //                     'success' => true,
    //                     'message' => 'Login successfull',
    //                     'data' => $login
    //                 );
    //                 echo json_encode($response);
    //                 die();
    //             } else {
    //                 $response = array(
    //                     'success' => false,
    //                     'message' => 'Invalid Username or Password'
    //                 );
    //                 echo json_encode($response);
    //                 die();
    //             }
    //         } else {
    //             throw new \Exception('Failed to prepare the statement');
    //         }
    //     } catch (\mysqli_sql_exception $e) {
    //         // Log error for debugging
    //         error_log("Database error: " . $e->getMessage());

    //         // Return failure response to the user
    //         $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
    //         echo json_encode($response);
    //     } catch (\Exception $e) {
    //         // Handle any other general exceptions
    //         error_log("General error: " . $e->getMessage());

    //         // Return failure response to the user
    //         $response = array('success' => false, 'message' => 'An unexpected error occurred');
    //         echo json_encode($response);
        }
    }
}

