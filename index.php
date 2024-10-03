<?php
require_once 'class/Employee.php';
$url = $_SERVER['REQUEST_URI'];
$url_path = ltrim($url, '/');

$segments = explode('/', $url_path);
$first_segment = isset($segments[0]) ? $segments[0] : null;
$second_segment = isset($segments[1]) ? $segments[1] : null;
$third_segment = isset($segments[2]) ? $segments[2] : null;
$forth_segment = isset($segments[3]) ? $segments[3] : null;
$Employee = new Employee();

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
    if ($second_segment == 'emp' && $third_segment == 'update' && is_numeric($forth_segment)) {
        $id = $forth_segment;
            $emp_id = $_POST['emp_id'];
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
            }
            else{
                $data = $Employee->getDocDetails($id);
                $doc_name = $data['doc_name'];
                $doc_path = $data['doc_path'];
                $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
                    echo $response;
            }
        }

    
}
