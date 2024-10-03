<?php
require_once("config.php");
class Employee extends DatabaseConnection
{

    public function getEmployeeDetails($id)
    {
        $query = "SELECT * FROM employees WHERE emp_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $employee = mysqli_fetch_assoc($result);
            
            if ($employee) {
                $response = array('success' => true, 'message' => 'Employee Details Fetched successfully', 'data' => $employee);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }


    public function getDocDetails($docId){
        $query = "SELECT * FROM emp_docs WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $docId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $employee = mysqli_fetch_assoc($result);
            return $employee;
        }
    }

    public function getEmployeesName()
    {
        $query = "SELECT name FROM employees";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
           
            $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($employees) {
                $response = array('success' => true, 'message' => 'Employee Names Fetched successfully', 'data' => $employees);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }

    public function getEmployeesDetails()
    {
        $query = "SELECT * FROM employees";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
           
            $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($employees) {
                $response = array('success' => true, 'message' => 'Employees Details Fetched successfully', 'data' => $employees);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }


    // public function addEmployeeDoc($id, $doc_name, $doc_path, $updated_by)
    // {

    //     $sql = "INSERT INTO emp_docs (emp_id, doc_name, doc_path, updated_by) VALUES (?, ?, ?, ?)";
    //     $stmt = mysqli_prepare($this->conn, $sql);
    //     if ($stmt){
    //         mysqli_stmt_bind_param($stmt, 'isss', $id, $doc_name, $doc_path, $updated_by);

    //         if (mysqli_stmt_execute($stmt)) {

    //             $response = array('success' => true, 'message' => 'Employee Documents Submitted successfully');
    //             echo json_encode($response);
    //             die();
    //         } else {
    //             $response = array('success' => false, 'message' => 'Failed to Submit documents');
    //             echo json_encode($response);
    //             die();
    //         }
    //     }
    // }

    public function updateEmployeeDoc($doc_id, $emp_id, $doc_name, $doc_path, $updated_by)
    {
        $sql = "UPDATE emp_docs SET emp_id = ?, doc_name = ?, doc_path = ?, updated_by = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'isssi', $emp_id, $doc_name, $doc_path, $updated_by, $doc_id);
    
            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Employee Document updated successfully');
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to update document');
                echo json_encode($response);
                die();
            }
        } else {
            $response = array('success' => false, 'message' => 'Failed to prepare the query');
            echo json_encode($response);
            die();
        }
    }
}
