<?php

namespace model;

require_once("config.php");

class Leave extends \db\DatabaseConnection

{

    public function addRequest($data)
    {
        header('Content-Type: application/json');
        $emp_id = $data['emp_id'];
        $type = $data['type'];
        $duration = $data['duration'];
        $request_to = $data['request_to'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $status = $data['status'];


        $emp_id = htmlspecialchars(trim($emp_id), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $type = htmlspecialchars(trim($type), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $duration = htmlspecialchars(trim($duration), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $request_to = htmlspecialchars(trim($request_to), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $start_date = htmlspecialchars(trim($start_date), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $end_date = htmlspecialchars(trim($end_date), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $status = htmlspecialchars(trim($status), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        // $request_time = htmlspecialchars(trim($request_time), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters


        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $emp_id = filter_var($emp_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $type = filter_var($type, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $duration = filter_var($duration, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $request_to = filter_var($request_to, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start_date = filter_var($start_date, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $end_date = filter_var($end_date, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $request_time = filter_var($request_time, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Ensure sanitized strings are not empty
        if (
            empty($emp_id) || empty($type) || empty($duration) || empty($request_to) || empty($start_date) || empty($end_date) || empty($status)
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

       
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
          
            $sql = "INSERT INTO leave_requests (emp_id, type, duration, request_to, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->conn, $sql);

            mysqli_stmt_bind_param($stmt, 'issssss', $emp_id, $type, $duration, $request_to, $start_date, $end_date, $status);
           
            if (mysqli_stmt_execute($stmt)) {
            
                $response = array('success' => true, 'message' => 'Leave request added successfully');
                echo json_encode($response);
            }

          
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function addResponse($data)
    {
        header('Content-Type: application/json');
        $request_id = $data['request_id'];
        $status = $data['status'];
        $updated_by = $data['updated_by'];
        $allocated_time = $data['allocated_time'];


        $request_id = htmlspecialchars(trim($request_id), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $status = htmlspecialchars(trim($status), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $updated_by = htmlspecialchars(trim($updated_by), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $allocated_time = htmlspecialchars(trim($allocated_time), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        // $request_time = htmlspecialchars(trim($request_time), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters


        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $request_id = filter_var($request_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $updated_by = filter_var($updated_by, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $allocated_time = filter_var($allocated_time, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Ensure sanitized strings are not empty
        if (
            empty($request_id) || empty($status) || empty($updated_by) || empty($allocated_time)
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $sql = "INSERT INTO leave_allocated (request_id, status, updated_by, allocated_time) VALUES (?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->conn, $sql);

    
            mysqli_stmt_bind_param($stmt, 'isss', $request_id, $status, $updated_by, $allocated_time);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Response added successfully');
                echo json_encode($response);
            }

            
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function getResponseByReqId($id)
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT * FROM leave_allocated WHERE request_id = ? ";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {

                $stmt->bind_param('i', $id);
                $stmt->execute();

                $result = mysqli_stmt_get_result($stmt);
                $responseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if ($responseDetails) {
                    $response = array(
                        'success' => true,
                        'message' => 'Leave response fetched successfully',
                        'data' => $responseDetails
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
                throw new \Exception('Failed to prepare the statement');
            }
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }
    public function getRequestByEmpId($id)
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT * FROM leave_requests WHERE emp_id = ? ";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {

                $stmt->bind_param('i', $id);
                $stmt->execute();

                $result = mysqli_stmt_get_result($stmt);
                $responseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if ($responseDetails) {
                    $response = array(
                        'success' => true,
                        'message' => 'Request details fetched successfully',
                        'data' => $responseDetails
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
                throw new \Exception('Failed to prepare the statement');
            }
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function getRequestWithResponse($request_id)
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT leave_requests.*, leave_allocated.*
              FROM leave_requests 
              LEFT JOIN leave_allocated ON leave_requests.id = leave_allocated.request_id 
              WHERE leave_requests.id = ?";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {

                $stmt->bind_param('i', $request_id);
                $stmt->execute();

                $result = mysqli_stmt_get_result($stmt);
                $leaveDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

                if ($leaveDetails) {
                    $response = array(
                        'success' => true,
                        'message' => 'Leave Request and Response Details Fetched successfully',
                        'data' => $leaveDetails
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
                throw new \Exception('Failed to prepare the statement');
            }
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());
            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function updateLeaveResponse($id, $data)
    {

        header('Content-Type: application/json');
        $query = "SELECT * FROM leave_allocated WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $query);
        $request_id = $data['request_id'];
        $status = $data['status'];
        $updated_by = $data['updated_by'];
        $allocated_time = $data['allocated_time'];
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            
            if ($row) {
                // Sanitize strings to avoid unnecessary characters or malicious input
                $request_id = htmlspecialchars(trim($request_id), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
                $status = htmlspecialchars(trim($status), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
                $updated_by = htmlspecialchars(trim($updated_by), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
                $allocated_time = htmlspecialchars(trim($allocated_time), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

                // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
                $request_id = filter_var($request_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $updated_by = filter_var($updated_by, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $allocated_time = filter_var($allocated_time, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                if (empty($request_id) || empty($status) || empty($updated_by) || empty($allocated_time)) {
                    $response = array('success' => false, 'message' => 'Input field may be invalid');
                    echo json_encode($response);
                    return;
                }


                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                try {

                    $sql = "UPDATE leave_allocated SET request_id = ?, status = ?, updated_by = ?, allocated_time = ? WHERE id = ?";

                    $stmt = mysqli_prepare($this->conn, $sql);


                    mysqli_stmt_bind_param($stmt, 'isssi', $request_id, $status, $updated_by, $allocated_time, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        $response = array('success' => true, 'message' => 'Leave Response updated successfully');
                        echo json_encode($response);
                    }

                    mysqli_stmt_close($stmt);
                } catch (\mysqli_sql_exception $e) {
                    error_log("Database error: " . $e->getMessage());

                    $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
                    echo json_encode($response);
                } catch (\Exception $e) {
                    error_log("General error: " . $e->getMessage());
                    $response = array('success' => false, 'message' => 'An unexpected error occurred');
                    echo json_encode($response);
                }
            } else {
                $response = array('success' => false, 'message' => 'Document id not found');
                echo json_encode($response);
            }
        }
    }

    public function updateRequestStatus($id, $status)
    {

        header('Content-Type: application/json');
        $query = "SELECT * FROM leave_requests WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            
            if ($row) {
                if (is_array($status)) {
                    // Handle the array case, for example, convert to a string or throw an error
                    $status = implode(', ', $status);  // Convert array to a comma-separated string
                } 
                $status = htmlspecialchars(trim($status), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
           
                $status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                if (empty($status)) {
                    $response = array('success' => false, 'message' => 'Input field may be invalid');
                    echo json_encode($response);
                    return;
                }


                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                try {

                    $sql = "UPDATE leave_requests SET status = ? WHERE id = ?";

                    $stmt = mysqli_prepare($this->conn, $sql);


                    mysqli_stmt_bind_param($stmt, 'si', $status, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        $response = array('success' => true, 'message' => 'Request Status updated successfully');
                        echo json_encode($response);
                    }

                    mysqli_stmt_close($stmt);
                } catch (\mysqli_sql_exception $e) {
                    error_log("Database error: " . $e->getMessage());

                    $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
                    echo json_encode($response);
                } catch (\Exception $e) {
                    error_log("General error: " . $e->getMessage());
                    $response = array('success' => false, 'message' => 'An unexpected error occurred');
                    echo json_encode($response);
                }
            } else {
                $response = array('success' => false, 'message' => 'Document id not found');
                echo json_encode($response);
            }
        }
    }

    public function addType($name)
    {
        header('Content-Type: application/json');
        

        $name = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Ensure sanitized strings are not empty
        if (
            empty($name)
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $sql = "INSERT INTO leave_types (name) VALUES (?)";
            
            $stmt = mysqli_prepare($this->conn, $sql);

    
            mysqli_stmt_bind_param($stmt, 's', $name);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Leave Type added successfully');
                echo json_encode($response);
            }

            
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function updateType($id, $name)
    {
        header('Content-Type: application/json');
        

        $name = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Ensure sanitized strings are not empty
        if (
            empty($name)
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            // $sql = "INSERT INTO leave_types (name) VALUES (?)";
            $sql = "UPDATE leave_types SET name = ? WHERE id = ?";
            
            $stmt = mysqli_prepare($this->conn, $sql);

    
            mysqli_stmt_bind_param($stmt, 'si', $name, $id);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Leave Type updated successfully');
                echo json_encode($response);
            }

            
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function addLeaveDetails($data)
    {
        header('Content-Type: application/json');
        $type = $data['type'];
        $duration = $data['duration'];

        $type = htmlspecialchars(trim($type), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $duration = htmlspecialchars(trim($duration), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        $type = filter_var($type, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $duration = filter_var($duration, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (
            empty($type) ||  empty($duration) 
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $sql = "INSERT INTO leave_details (type, duration) VALUES (?, ?)";
            
            $stmt = mysqli_prepare($this->conn, $sql);

    
            mysqli_stmt_bind_param($stmt, 'ss', $type, $duration);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Leave Details added successfully');
                echo json_encode($response);
            }

            
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    public function updateTypeDetails($id, $data)
    {
        header('Content-Type: application/json');
        

        $type = htmlspecialchars(trim($data['type']), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $duration = htmlspecialchars(trim($data['duration']), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        $type = filter_var($type, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $duration = filter_var($duration, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (
            empty($type) || empty($duration)
        ) {
            $response = array('success' => false, 'message' => 'Field may be null or invalid');
            echo json_encode($response);
            return;
        }

        
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $sql = "UPDATE leave_details SET type = ?, duration = ? WHERE id = ?";
            
            $stmt = mysqli_prepare($this->conn, $sql);

            mysqli_stmt_bind_param($stmt, 'ssi', $type, $duration, $id);

            if (mysqli_stmt_execute($stmt)) {
                $response = array('success' => true, 'message' => 'Details of Leave Type updated successfully');
                echo json_encode($response);
            }

            
            mysqli_stmt_close($stmt);
        } catch (\mysqli_sql_exception $e) {

            error_log("Database error: " . $e->getMessage());

            $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());


            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    // public function deleteType($id)
    // {
    //     header('Content-Type: application/json');

    //     try {
    //         $sql = "DELETE FROM leave_types WHERE id = ?";
            
    //         $stmt = mysqli_prepare($this->conn, $sql);

    //         mysqli_stmt_bind_param($stmt, 'i', $id);

    //         if (mysqli_stmt_execute($stmt)) {
    //             $response = array('success' => true, 'message' => 'Leave Type deleted successfully');
    //             echo json_encode($response);
    //         }

            
    //         mysqli_stmt_close($stmt);
    //     } catch (\mysqli_sql_exception $e) {

    //         error_log("Database error: " . $e->getMessage());

    //         $response = array('success' => false, 'message' => 'Failed to add request due to a database error');
    //         echo json_encode($response);
    //     } catch (\Exception $e) {
    //         error_log("General error: " . $e->getMessage());


    //         $response = array('success' => false, 'message' => 'An unexpected error occurred');
    //         echo json_encode($response);
    //     }
    // }

    public function deleteType($id)
{
    header('Content-Type: application/json');

    try {
        // Check if the record is referenced by foreign keys before deleting
        $checkForeignKeySQL = "SELECT COUNT(*) AS count FROM leave_details WHERE type = ?";
        $stmt = mysqli_prepare($this->conn, $checkForeignKeySQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            // If the record is referenced, do not allow deletion
            $response = array('success' => false, 'message' => 'Cannot delete: Leave Type is referenced in other records.');
            echo json_encode($response);
            return; // Exit early
        }
        mysqli_stmt_close($stmt); // Close the previous statement

        // Now proceed to delete the record
        $deleteSQL = "DELETE FROM leave_types WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $deleteSQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('success' => true, 'message' => 'Leave Type deleted successfully.');
            echo json_encode($response);
        } else {
            // Handle failure case for the DELETE statement
            $response = array('success' => false, 'message' => 'Failed to delete the Leave Type.');
            echo json_encode($response);
        }

        mysqli_stmt_close($stmt); // Always close the statement

    } catch (\mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        $response = array('success' => false, 'message' => 'Failed to delete due to a database error.');
        echo json_encode($response);

    } catch (\Exception $e) {
        error_log("General error: " . $e->getMessage());
        $response = array('success' => false, 'message' => 'An unexpected error occurred.');
        echo json_encode($response);
    }
}

public function deleteTypeDetails($id)
{
    header('Content-Type: application/json');

    try {
        // // Check if the record is referenced by foreign keys before deleting
        // $checkForeignKeySQL = "SELECT COUNT(*) AS count FROM leave_details WHERE type = ?";
        // $stmt = mysqli_prepare($this->conn, $checkForeignKeySQL);
        // mysqli_stmt_bind_param($stmt, 'i', $id);
        // mysqli_stmt_execute($stmt);
        // $result = mysqli_stmt_get_result($stmt);
        // $row = mysqli_fetch_assoc($result);

        // if ($row['count'] > 0) {
        //     // If the record is referenced, do not allow deletion
        //     $response = array('success' => false, 'message' => 'Cannot delete: Leave Type is referenced in other records.');
        //     echo json_encode($response);
        //     return; // Exit early
        // }
        // mysqli_stmt_close($stmt); // Close the previous statement

        // // Now proceed to delete the record
        $deleteSQL = "DELETE FROM leave_details WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $deleteSQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            $response = array('success' => true, 'message' => 'Leave Type deleted successfully.');
            echo json_encode($response);
        } else {
            // Handle failure case for the DELETE statement
            $response = array('success' => false, 'message' => 'Failed to delete the Leave Type.');
            echo json_encode($response);
        }

        mysqli_stmt_close($stmt); // Always close the statement

    } catch (\mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        $response = array('success' => false, 'message' => 'Failed to delete due to a database error.');
        echo json_encode($response);

    } catch (\Exception $e) {
        error_log("General error: " . $e->getMessage());
        $response = array('success' => false, 'message' => 'An unexpected error occurred.');
        echo json_encode($response);
    }
}


    
    
}
