<?php

use model\Leave;

require 'vendor/autoload.php';


$Leave = new Leave();


function handleLeaveRequest($method, $segments)
{
    global $Leave;

    // $second_segment = isset($segments[1]) ? $segments[1] : null;
    // $third_segment = isset($segments[2]) ? $segments[2] : null;
    // $forth_segment = isset($segments[3]) ? $segments[3] : null;
    // $fifth_segment = isset($segments[4]) ? $segments[4] : null;

    require_once dirname(__DIR__) .'/SegmentHandler.php';

    // Get segment values
    $segmentValues = getSegmentValues($segments);
    $second_segment = $segmentValues['second'];
    $third_segment = $segmentValues['third'];
    $forth_segment = $segmentValues['forth'];
    $fifth_segment = $segmentValues['fifth'];

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
            } else if ($method == 'GET' && $third_segment == 'details' && is_numeric($forth_segment)) {
                $id  = $forth_segment;
                $response = $Leave->getRequestWithResponse($id);
                echo $response;
            }
            // else if ($method == 'POST' && $third_segment == 'updateResponse' && is_numeric($forth_segment)) {
            //     $id = $forth_segment;
            //     $data = array(
            //         'request_id' => $_POST['request_id'],
            //         'status' => $_POST['status'],
            //         'updated_by' => $_POST['updated_by'],
            //         'allocated_time' => $_POST['allocated_time']
            //     );
            //     $response = $Leave->updateLeaveResponse($id , $data);
            //     echo $response;
            // }

            else if ($method == 'PUT' && $third_segment == 'updateResponse' && is_numeric($forth_segment)) {
                $id = $forth_segment;

                // Get Content-Type and extract the boundary
                $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

                // Check if the content type is multipart/form-data
                if (strpos($contentType, 'multipart/form-data') !== false) {
                    // Extract the boundary
                    preg_match('/boundary=(.*)$/', $contentType, $matches);
                    $boundary = $matches[1];

                    // Get raw data from php://input
                    $rawData = file_get_contents("php://input");

                    $parts = explode("--" . $boundary, $rawData);

                    $data = [];
                    $fileData = [];

                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition: form-data;') !== false) {
                            if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                $fieldName = $nameMatches[1];

                                $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));

                                $data[$fieldName] = $value;
                            }
                        }
                    }
                }

                // Merge file data into $data array if any files were uploaded
                if (!empty($fileData)) {
                    $data = array_merge($data, $fileData);
                }

                // Extract required fields
                $request_id = $data['request_id'] ?? null;
                $updated_by = $data['updated_by'] ?? null;
                $status = $data['status'] ?? null;
                $allocated_time = $data['allocated_time'] ?? null;

                // Ensure that required fields are present
                if ($request_id && $updated_by && $status && $allocated_time) {

                    $response = $Leave->updateLeaveResponse($id, $data);
                    echo $response;
                } else {
                    echo "emp_id or updated_by is missing.";
                }
            }
            // else if ($method == 'POST' && $third_segment == 'updateRequestStatus' && is_numeric($forth_segment)) {
            //     $id = $forth_segment;

            //     $status = $_POST['status'];

            //     $response = $Leave->updateRequestStatus($id, $status);
            //     echo $response;
            // } 
            else if ($method == 'PUT' && $third_segment == 'updateRequestStatus' && is_numeric($forth_segment)) {
                $id = $forth_segment;

                $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

                if (strpos($contentType, 'multipart/form-data') !== false) {

                    preg_match('/boundary=(.*)$/', $contentType, $matches);
                    $boundary = $matches[1];

                    $rawData = file_get_contents("php://input");

                    $parts = explode("--" . $boundary, $rawData);

                    $data = [];
                    $fileData = [];

                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition: form-data;') !== false) {
                            if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                $fieldName = $nameMatches[1];

                                $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));

                                $data[$fieldName] = $value;
                            }
                        }
                    }
                }
                if (!empty($fileData)) {
                    $data = array_merge($data, $fileData);
                }
                $status = $data['status'] ?? null;
                if ($status) {

                    $response = $Leave->updateRequestStatus($id, $data);
                    echo $response;
                } else {
                    echo "emp_id or updated_by is missing.";
                }
            } else if ($method == 'POST' && $third_segment == 'type' && $forth_segment == 'add') {

                $name =  $_POST['name'];

                $response = $Leave->addType($name);
                echo $response;
            }
            //  else if ($method == 'POST' && $third_segment == 'type' && $forth_segment == 'update' && is_numeric($fifth_segment)) {
            //     $id = $fifth_segment;
            //     $name =  $_POST['name'];

            //     $response = $Leave->updateType($id, $name);
            //     echo $response;
            // } 
            else if ($method == 'PUT' && $third_segment == 'type' && is_numeric($forth_segment) && is_numeric($fifth_segment)) {
                $id = $fifth_segment;

                $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

                if (strpos($contentType, 'multipart/form-data') !== false) {

                    preg_match('/boundary=(.*)$/', $contentType, $matches);
                    $boundary = $matches[1];

                    $rawData = file_get_contents("php://input");

                    $parts = explode("--" . $boundary, $rawData);

                    $data = [];
                    $fileData = [];

                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition: form-data;') !== false) {
                            if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                $fieldName = $nameMatches[1];

                                $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));

                                $data[$fieldName] = $value;
                            }
                        }
                    }
                }
                if (!empty($fileData)) {
                    $data = array_merge($data, $fileData);
                }
                // $type = $data['type'] ?? null;
                $name = $data['name'] ?? null;

                if ($name) {

                    $response = $Leave->updateType($id, $data);
                    echo $response;
                } else {
                    echo "type or duration is missing.";
                }
            } 
            else if ($method == 'POST' && $third_segment == 'type-details' && $forth_segment == 'add') {

                $data['type'] =  $_POST['type'];
                $data['duration'] =  $_POST['duration'];

                $response = $Leave->addLeaveDetails($data);
                echo $response;
            } else if ($method == 'PUT' && $third_segment == 'update-type-details' && is_numeric($forth_segment)) {
                $id = $forth_segment;

                $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

                if (strpos($contentType, 'multipart/form-data') !== false) {

                    preg_match('/boundary=(.*)$/', $contentType, $matches);
                    $boundary = $matches[1];

                    $rawData = file_get_contents("php://input");

                    $parts = explode("--" . $boundary, $rawData);

                    $data = [];
                    $fileData = [];

                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition: form-data;') !== false) {
                            if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                $fieldName = $nameMatches[1];

                                $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));

                                $data[$fieldName] = $value;
                            }
                        }
                    }
                }
                if (!empty($fileData)) {
                    $data = array_merge($data, $fileData);
                }
                $type = $data['type'] ?? null;
                $duration = $data['duration'] ?? null;

                if ($type && $duration) {

                    $response = $Leave->updateTypeDetails($id, $data);
                    echo $response;
                } else {
                    echo "type or duration is missing.";
                }
            } 
            else if ($method == 'DELETE' && $third_segment == 'type' && $forth_segment == 'delete' && is_numeric($fifth_segment)) {

                $id = $fifth_segment;
                $response = $Leave->deleteType($id);
                echo $response;
            }
            else if ($method == 'DELETE' && $third_segment == 'type-details' && $forth_segment == 'delete' && is_numeric($fifth_segment)) {

                $id = $fifth_segment;
                $response = $Leave->deleteTypeDetails($id);
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
