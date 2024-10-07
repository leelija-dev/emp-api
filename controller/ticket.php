<?php

use model\Ticket;

$Ticket = new Ticket();

function handleTicketRequest($method, $segments)
{
    global $Ticket;

    $second_segment = isset($segments[1]) ? $segments[1] : null;
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;

    switch ($second_segment) {
        case 'tickets':
            if ($method == 'GET' && $third_segment == null && $forth_segment == null) {
                $response = $Ticket->getTickets();
                echo $response;
            } else if ($method == 'GET'  && $third_segment == 'emp' && is_numeric($forth_segment)) {
                $emp_id = $forth_segment;
                $response = $Ticket->getTicketByEmpId($emp_id);
                echo $response;
            }
            break;
        case 'ticket':
            if ($method == 'POST' && $third_segment == 'insert') {
                $data['query'] = $_REQUEST['query'];
                $data['priority'] = $_REQUEST['priority'];
                $data['subject'] = $_REQUEST['subject'];
                $data['emp_id'] = $_REQUEST['emp_id'];
                $data['generated_by'] = $_REQUEST['generated_by'];
                $data['status'] = $_REQUEST['status'];
                if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                    $data['file'] = $_FILES['file']['name'];
                    $file = $_FILES['file']['name'];
                    $doc_tmp_path = $_FILES['file']['tmp_name'];
                    $target_directory = "public/emp-docs/";
                    $doc_path = $target_directory . basename($file);

                    if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                        $response = $Ticket->addTickets($data);
                        echo $response;
                    } else {
                        echo "Error moving the uploaded file.";
                    }
                }
            } else if ($method == 'GET' && is_numeric($third_segment)) {
                $id = $third_segment;

                $response = $Ticket->getAllDetails($id);
                echo $response;
            }
            break;
        case 'tickets':
            if ($method == 'GET' && $third_segment == null && $forth_segment == null) {
                $response = $Ticket->getTickets();
                echo $response;
            } else if ($method == 'GET'  && $third_segment == 'emp' && is_numeric($forth_segment)) {
                $emp_id = $forth_segment;
                $response = $Ticket->getTicketByEmpId($emp_id);
                echo $response;
            }
            break;
            case 'ticket-response':
                if ($method == 'POST' && $third_segment == 'insert') {
                    $data['ticket_id'] = $_REQUEST['ticket_id'];
                        $data['response'] = $_REQUEST['response'];
                        $data['respond_by'] = $_REQUEST['respond_by'];
                    // print_r($data['ticket_id']);  die();
                        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                            $data['file'] = $_FILES['file']['name'];
                            // print_r($data['file']); die();
                            $file = $_FILES['file']['name'];
                            $doc_tmp_path = $_FILES['file']['tmp_name'];
                            $target_directory = "public/emp-docs/";
                            $doc_path = $target_directory . basename($file);
                    
                            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                                // echo "hi"; die();
                                $response = $Ticket->addResponse($data);
                                echo $response;
                            } else {
                                echo "Error moving the uploaded file.";
                            }
                        }
                }
                break;
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(array('success' => false, 'message' => 'Invalid Ticket Route.'));
            exit();
    }
}
