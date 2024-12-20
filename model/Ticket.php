<?php

namespace model;

require_once("config.php");
class Ticket extends \db\DatabaseConnection
{
    public function addTickets($data)
    {
        $query = $data['query'];
        $priority = $data['priority'];
        $emp_id = $data['emp_id'];
        $status = $data['status'];
        $subject = $data['subject'];
        $generated_by = $data['generated_by'];
        $file = $data['file'];

        header('Content-Type: application/json');

        $sql = "INSERT INTO tickets (emp_id, query, subject, priority, status, generated_by) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $emp_id, $query, $subject, $priority, $status, $generated_by);

            if (mysqli_stmt_execute($stmt)) {

                $ticket_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_files (ticket_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $ticket_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'status' => true,
                            'message' => 'Ticket added successfully'
                        );
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Ticket added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Ticket added but there is an error with file'
                    );
                }
            } else {
                $response = array('status' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'There is an error'
            );
        }

        echo json_encode($response);
        die();
    }

    public function addResponse($data)
    {
        $ticket_id = $data['ticket_id'];
        $response = $data['response'];
        $respond_by = $data['respond_by'];
        $file = $data['file'];
       

        header('Content-Type: application/json');

        $sql = "INSERT INTO ticket_response (ticket_id, response, respond_by) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sss', $ticket_id, $response, $respond_by);

            if (mysqli_stmt_execute($stmt)) {

                $response_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_response_files (response_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $response_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'status' => true,
                            'message' => 'Response added successfully'
                        );
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Response added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Response added but there is an error with file'
                    );
                }
            } else {
                $response = array('status' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'There is an error'
            );
        }

        echo json_encode($response);
        die();
    }

    public function getAllDetails($ticket_id)
    {
        header('Content-Type: application/json');

        try {

            $query = "SELECT tickets.*, ticket_files.file AS ticket_file, ticket_files.added_on AS file_added,  ticket_response.*, ticket_response_files.* 
              FROM tickets
              LEFT JOIN ticket_files ON tickets.id = ticket_files.ticket_id
              LEFT JOIN ticket_response ON tickets.id = ticket_response.ticket_id
              LEFT JOIN ticket_response_files ON ticket_response.id = ticket_response_files.response_id 
              WHERE tickets.id = ?";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {

                $stmt->bind_param('i', $ticket_id);
                $stmt->execute();

                $result = mysqli_stmt_get_result($stmt);
                // $ticketDetails = mysqli_fetch_assoc($result);
                $ticketDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

                if ($ticketDetails) {
                    $response = array(
                        'status' => true,
                        'message' => 'Employee and Document Details Fetched successfully',
                        'data' => $ticketDetails
                    );
                    echo json_encode($response);
                    die();
                } else {
                    $response = array(
                        'status' => false,
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

            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        } catch (\Exception $e) {
            
            error_log("General error: " . $e->getMessage());

            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        }
    }


    //LIST OF TICKETS
    public function getTickets()
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT tickets.*, ticket_files.* 
              FROM tickets
              LEFT JOIN ticket_files ON tickets.id = ticket_files.ticket_id";
            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $tickets = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if ($tickets) {
                    $response = array('status' => true, 'message' => 'Tickets Fetched successfully', 'data' => $tickets);
                    echo json_encode($response);
                    die();
                } else {
                    $response = array('status' => false, 'message' => 'Failed to fetch Tickets Details');
                    echo json_encode($response);
                    die();
                }
            }
        } catch (\mysqli_sql_exception $e) {
            error_log("Database error: " . $e->getMessage());

            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());
            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        }
    }

    public function getTicketByEmpId($id)
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT tickets.*, ticket_files.* 
          FROM tickets
          LEFT JOIN ticket_files ON tickets.id = ticket_files.ticket_id 
          WHERE emp_id = ?";

            $stmt = mysqli_prepare($this->conn, $query);

            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);

                $ticketData = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if ($ticketData) {
                    $response = array('status' => true, 'message' => 'Ticket Data Fetched successfully', 'data' => $ticketData);
                    echo json_encode($response);
                    die();
                } else {
                    $response = array('status' => false, 'message' => 'Failed to fetch Tickets Details');
                    echo json_encode($response);
                    die();
                }
            }
        } catch (\mysqli_sql_exception $e) {
            error_log("Database error: " . $e->getMessage());

            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("General error: " . $e->getMessage());

            $response = array('status' => false, 'message' => $e->getMessage());
            echo json_encode($response);
        }
    }
}
