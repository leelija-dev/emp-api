<?php
require_once 'class/Employee.php';

$Employee = new Employee();

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action == 'getEmployeeDetails') {
        $id = $_REQUEST['emp_id'];

        $response = $Employee->getEmployeeDetails($id);
        echo $response;
    }

    if ($action == 'postEmployeeDetails') {
        $id = $_REQUEST['emp_id'];
        $updated_by = $_REQUEST['updated_by'];
        if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
            $doc_name = $_FILES['doc_name']['name'];
            $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
            $target_directory = "EmployeeDoc/";
            $doc_path = $target_directory . basename($doc_name);

            if (move_uploaded_file($doc_tmp_path, $doc_path)) {
                $response = $Employee->addEmployeeDoc($id, $doc_name, $doc_path, $updated_by);
                echo $response;
            } else {
                echo "Error moving the uploaded file.";
            }
        }
    }
}
