<?php
require_once("config.php");
class Employee extends DatabaseConnection
{
    //GET EMPLOYEE DETAILS USING EMPLOYEE ID

    public function getEmployeeDetails($id)
{
    $query = "SELECT employees.*, emp_docs.* 
              FROM employees 
              LEFT JOIN emp_docs ON employees.emp_id = emp_docs.emp_id 
              WHERE employees.emp_id = ?";

    $stmt = mysqli_prepare($this->conn, $query);

    if ($stmt) {
        
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = mysqli_stmt_get_result($stmt);
        $employeeDetails = mysqli_fetch_assoc($result);

        if ($employeeDetails) {
            $response = array(
                'success' => true, 
                'message' => 'Employee and Document Details Fetched successfully', 
                'data' => $employeeDetails
            );
            echo json_encode($response);
            die();
        } else {
            $response = array(
                'success' => false, 
                'message' => 'Failed to fetch details'
            );
            echo json_encode($response);
            die();
        }
    } else {
        $response = array(
            'success' => false, 
            'message' => 'There is an error'
        );
        echo json_encode($response);
        die();
    }
}

    //Get document details of a employee using doc id
    public function getDocDetails($docId)
    {
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

    //Get each employee details using emp id
    public function getempDetails($empId)
    {
        $query = "SELECT * FROM employees WHERE emp_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $empId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $employee = mysqli_fetch_assoc($result);
            return $employee;
        }
    }

    //GET ALL EMPLOYEES NAME

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

    //GET ALL EMPLOYEE DETAILS
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

    //UPDATE EMPLOYEE DOCUMENTS BY DOC ID

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

//UPDATE EMPLOYEE DETAILS USING EMPLOYEE ID
    public function updateEmployeeDetails($emp_id, $data)
    {
        $name = $data['name'];
        $designation = $data['designation'];
        $doj = $data['doj'];
        // print_r($doj);   die();
        $gender = $data['gender'];
        $image = $data['image'];
        $phone = $data['phone'];
        $email = $data['email'];
        $password = $data['password'];
        $status = $data['status']; 
        $featured = $data['featured'];
        $sql = "UPDATE employees SET name = ?, designation = ?, doj = ?, gender = ?, image = ?, phone = ?, email = ?, password = ?, status = ?, featured = ? WHERE emp_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssiii', $name, $designation, $doj, $gender, $image, $phone, $email, $password, $status, $featured, $emp_id);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Employee updated successfully');
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
