<?php

use model\Employee;

$Employee = new Employee();

function handleEmployeeRequest($method, $segments)
{
    global $Employee;

    $second_segment = isset($segments[1]) ? $segments[1] : null;
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;

    switch ($second_segment) {
        case 'employees':
            if ($method == 'GET') {
                //Fetch list of all employees (name list only)


                if ($third_segment && is_numeric($third_segment)) {
                    // Fetch employee details by ID
                    $id = intval($third_segment);
                    $response = $Employee->getEmployeeDetails($id);
                    echo $response;
                } else {
                    // Fetch all employee names
                    $response = $Employee->getEmployeesName();
                    echo $response;
                }
            }
           
            break;

        case 'employee-details':
            if ($method == 'GET') {
                $response = $Employee->getEmployeesDetails();
                echo $response;
            }
            break;
        case 'emp':
            if ($method == 'POST' && $third_segment == 'update' && is_numeric($forth_segment)) {

                // parse_str(file_get_contents("php://input"), $put_vars);
                $id = intval($forth_segment);
                $exist = $Employee->checkIfEmployeeExists($id);
                if ($exist) {
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
                        $target_directory = "public/emp-docs/";
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
            } else if ($method == 'POST' && $third_segment == 'update-doc' && is_numeric($forth_segment)) {
                $id = $forth_segment;
                $emp_id = $_REQUEST['emp_id'];
                $updated_by = $_REQUEST['updated_by'];
                if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
                    $doc_name = $_FILES['doc_name']['name'];
                    $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
                    $target_directory = "public/emp-docs/";
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
            } else if ($third_segment && is_numeric($third_segment)) {
                // Fetch employee details by ID
                $id = intval($third_segment);
                $response = $Employee->getEmployeeDetails($id);
                echo $response;
            } elseif ($method == 'POST' && $third_segment == 'add') {
                // Add a new employee
                $data = array(
                    'name' => $_POST['name'],
                    'designation' => $_POST['designation'],
                    'doj' => $_POST['doj'],
                    'gender' => $_POST['gender'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'status' => $_POST['status'],
                    'featured' => $_POST['featured'],
                );
                if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    $image = $_FILES['image']['name'];
                    $doc_tmp_path = $_FILES['image']['tmp_name'];
                    $target_directory = "public/emp-docs/";
                    $doc_path = $target_directory . basename($image);
                    if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                        $data['image'] = $image;
                        $response = $Employee->addEmployee($data);
                        echo $response;
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'Error moving the uploaded file.'));
                    }
                }
            } elseif ($method == 'POST' && $third_segment == 'add-doc') {
                // Add a new employee do

                // $doc_name = $_REQUEST['doc_name'];
                $updated_by = $_REQUEST['updated_by'];
                $emp_id = $_REQUEST['emp_id'];


                if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
                    $doc_name = $_FILES['doc_name']['name'];
                    $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
                    $target_directory = "public/emp-docs/";
                    $doc_path = $target_directory . basename($doc_name);
                    if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                        $response = $Employee->addEmployeeDoc($emp_id, $doc_name, $doc_path, $updated_by);
                        echo $response;
                    } else {
                        echo "Error moving the uploaded file.";
                    }
                }
            }
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(array('success' => false, 'message' => 'Invalid Employee Route.'));
            exit();
    }
}
