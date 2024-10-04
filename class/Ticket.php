<?php
require_once("config.php");
class Ticket extends DatabaseConnection
{
    public function addTickets($data)
    {
        $query = $data['query'];
        $priority = $data['priority'];
        $status = $data['status'];
        $generated_by = $data['generated_by'];
        $file = $data['file'];

        header('Content-Type: application/json');

        $sql = "INSERT INTO ticket (query, priority, status, generated_by) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $query, $priority, $status, $generated_by);

            if (mysqli_stmt_execute($stmt)) {

                $ticket_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_files (ticket_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $ticket_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'success' => true,
                            'message' => 'Ticket added successfully'
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Ticket added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'Ticket added but there is an error with file'
                    );
                }
            } else {
                $response = array('success' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'success' => false,
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
            mysqli_stmt_bind_param($stmt, 'ssss', $ticket_id, $response, $respond_by);

            if (mysqli_stmt_execute($stmt)) {

                $response_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_response_files (response_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $response_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'success' => true,
                            'message' => 'Ticket added successfully'
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Ticket added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'Ticket added but there is an error with file'
                    );
                }
            } else {
                $response = array('success' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
        }

        echo json_encode($response);
        die();
    }
}