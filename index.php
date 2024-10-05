<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'model/Employee.php';
require_once 'model/Ticket.php';

$Employee   = new Employee();
$Ticket     = new Ticket();


$url        = $_SERVER['REQUEST_URI'];
$url_path   = ltrim($url, '/');

$segments           = explode('/', $url_path);
$first_segment      = isset($segments[0]) ? $segments[0] : null;
$second_segment     = isset($segments[1]) ? $segments[1] : null;
$third_segment      = isset($segments[2]) ? $segments[2] : null;
$forth_segment      = isset($segments[3]) ? $segments[3] : null;


if (isset($first_segment)) {
    if ($second_segment == 'employees' && is_numeric($third_segment)) {
        $id = $third_segment;

        $response = $Employee->getEmployeeDetails($id);
        echo $response;
    }

    //ADD DOCUMENT IN THE TABLE


    // if ($action == 'postEmployeeDetails') {
    //     $id = $_REQUEST['emp_id'];
    //     $updated_by = $_REQUEST['updated_by'];
    //     if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
    //         $doc_name = $_FILES['doc_name']['name'];
    //         $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
    //         $target_directory = "EmployeeDoc/";
    //         $doc_path = $target_directory . basename($doc_name);

    //         if (move_uploaded_file($doc_tmp_path, $doc_path)) {
    //             $response = $Employee->addEmployeeDoc($id, $doc_name, $doc_path, $updated_by);
    //             echo $response;
    //         } else {
    //             echo "Error moving the uploaded file.";
    //         }
    //     }
    // }

    //This section is for all employee name
    if ($second_segment == 'employees' && $third_segment == null && $forth_segment == null) {
        $response = $Employee->getEmployeesName();
        echo $response;
    }

    //This section is for all employee details
    if ($second_segment == 'employee-details' && $third_segment == null && $forth_segment == null) {
        $response = $Employee->getEmployeesDetails();
        echo $response;
    }

    //UPDATE EMPLOYEE DETAILS USING DOCUMEMT ID
    if ($second_segment == 'emp' && $third_segment == 'update-doc' && is_numeric($forth_segment)) {
        $id = $forth_segment;
        $emp_id = $_REQUEST['emp_id'];
        $updated_by = $_REQUEST['updated_by'];
        if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
            $doc_name = $_FILES['doc_name']['name'];
            $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
            $target_directory = "EmployeeDoc/";
            $doc_path = $target_directory . basename($doc_name);

            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
                echo $response;
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            $data = $Employee->getDocDetails($id);
            $doc_name = $data['doc_name'];
            $doc_path = $data['doc_path'];
            $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
            echo $response;
        }
    }


    if ($second_segment == 'emp' && $third_segment == 'update' && is_numeric($forth_segment)) {
        $id = $forth_segment;
        $exist = $Employee->checkIfEmployeeExists($id);
        if ($exist == true) {
            $data['name'] = $_REQUEST['name'];
            $data['designation'] = $_REQUEST['designation'];
            $data['doj'] = $_REQUEST['doj'];
            $data['gender'] = $_REQUEST['gender'];
            $data['phone'] = $_REQUEST['phone'];
            $data['email'] = $_REQUEST['email'];
            $data['password'] = $_REQUEST['password'];
            $data['status'] = $_REQUEST['status'];
            $data['featured'] = $_REQUEST['featured'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $data['image'] = $_FILES['image']['name'];
                $image = $_FILES['image']['name'];
                $doc_tmp_path = $_FILES['image']['tmp_name'];
                $target_directory = "EmployeeDoc/";
                $doc_path = $target_directory . basename($image);

                if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                    $response = $Employee->updateEmployeeDetails($id, $data);
                    echo $response;
                } else {
                    echo "Error moving the uploaded file.";
                }
            } else {
                $emp = $Employee->getempDetails($id);
                $data['image'] = $emp['image'];
                $response = $Employee->updateEmployeeDetails($id, $data);
                echo $response;
            }
        } else {
            echo "The Employee Not Found";
        }
    }

    if ($second_segment == 'emp' && $third_segment == 'add') {
        $data['name'] = $_REQUEST['name'];
        $data['designation'] = $_REQUEST['designation'];
        $data['doj'] = $_REQUEST['doj'];
        $data['gender'] = $_REQUEST['gender'];
        $data['phone'] = $_REQUEST['phone'];
        $data['email'] = $_REQUEST['email'];
        $data['password'] = $_REQUEST['password'];
        $data['status'] = $_REQUEST['status'];
        $data['featured'] = $_REQUEST['featured'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $data['image'] = $_FILES['image']['name'];
            $image = $_FILES['image']['name'];
            $doc_tmp_path = $_FILES['image']['tmp_name'];
            $target_directory = "EmployeeDoc/";
            $doc_path = $target_directory . basename($image);

            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                $response = $Employee->addEmployee($data);
                echo $response;
            } else {
                echo "Error moving the uploaded file.";
            }
        }
    }

    if ($second_segment == 'ticket' && $third_segment == 'insert') {
        $data['query'] = $_REQUEST['query'];
        $data['priority'] = $_REQUEST['priority'];
        $data['subject'] = $_REQUEST['subject'];
        $data['emp_id'] = $_REQUEST['emp_id'];
        $data['generated_by'] = $_REQUEST['generated_by'];
        $data['status'] = $_REQUEST['status'];
        // $data['added_on'] = $_REQUEST['added_on'];
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $data['file'] = $_FILES['file']['name'];
            $file = $_FILES['file']['name'];
            $doc_tmp_path = $_FILES['file']['tmp_name'];
            $target_directory = "EmployeeDoc/";
            $doc_path = $target_directory . basename($file);

            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                $response = $Ticket->addTickets($data);
                echo $response;
            } else {
                echo "Error moving the uploaded file.";
            }
        }
    }

    if ($second_segment == 'ticket' && is_numeric($third_segment)) {
        $id = $third_segment;

        $response = $Ticket->getAllDetails($id);
        echo $response;
    }

    if ($second_segment == 'tickets'  && $third_segment == 'emp' && is_numeric($forth_segment)) {
        $emp_id = $forth_segment;

        $response = $Ticket->getTicketByEmpId($emp_id);
        echo $response;
    }

    if ($second_segment == 'tickets' && $third_segment == null && $forth_segment == null) {
        $response = $Ticket->getTickets();
        echo $response;
    }

    if ($second_segment == 'ticket-response' && $third_segment == 'insert') {
        $data['ticket_id'] = $_REQUEST['ticket_id'];
        $data['response'] = $_REQUEST['response'];
        $data['respond_by'] = $_REQUEST['respond_by'];

        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $data['file'] = $_FILES['file']['name'];
            // print_r($data['file']); die();
            $file = $_FILES['file']['name'];
            $doc_tmp_path = $_FILES['file']['tmp_name'];
            $target_directory = "EmployeeDoc/";
            $doc_path = $target_directory . basename($file);

            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                $response = $Ticket->addResponse($data);
                echo $response;
            } else {
                echo "Error moving the uploaded file.";
            }
        }
    }



    if ($second_segment == 'tickets'  && $third_segment == null && $forth_segment == null) {
        // $id = $third_segment;

        $response = $Ticket->getTickets();
        echo $response;
    }
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}
