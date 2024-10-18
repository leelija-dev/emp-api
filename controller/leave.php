<?php

use model\Leave;

require 'vendor/autoload.php';


$Leave = new Leave();


function handleLeaveRequest($method, $segments)
{
    global $Leave;

    $second_segment = isset($segments[1]) ? $segments[1] : null;
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;

    switch ($second_segment) {
        case 'leave':
            if ($method == 'POST' && $third_segment == 'add' && $forth_segment == null) {
                $data = array(
                    'emp_id' => $_POST['emp_id'],
                    'type' => $_POST['type'],
                    'duration' => $_POST['duration'],
                    'request_to' => $_POST['request_to'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'status' => $_POST['status']
                );
                $response = $Leave->addRequest($data);
                echo $response;
            } else if ($method == 'POST' && $third_segment == 'addResponse' && $forth_segment == null) {
                $data = array(
                    'request_id' => $_POST['request_id'],
                    'status' => $_POST['status'],
                    'updated_by' => $_POST['updated_by'],
                    'allocated_time' => $_POST['allocated_time']
                );
                $response = $Leave->addResponse($data);
                echo $response;
            } else if ($method == 'GET' && $third_segment == 'response' && is_numeric($forth_segment)) {
                $id  = $forth_segment;
                $response = $Leave->getResponseByReqId($id);
                echo $response;
            } else if ($method == 'GET' && $third_segment == 'request' && is_numeric($forth_segment)) {
                $id  = $forth_segment;
                $response = $Leave->getRequestByEmpId($id);
                echo $response;
            }

            else if ($method == 'GET' && $third_segment == 'details' && is_numeric($forth_segment)) {
                $id  = $forth_segment;
                $response = $Leave->getRequestWithResponse($id);
                echo $response;
            }
            break;


        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid request"
            ]);
            break;
    }
}
