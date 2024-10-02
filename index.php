<?php
$con = mysqli_connect("localhost", "root", "", "leelija_db");



if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action == 'getEmployeeDetails') {
        $id = $_REQUEST['emp_id'];
        $sql = "SELECT * FROM employees WHERE emp_id ='$id'";
        $rs = $con->query($sql);
        $result = $rs->fetch_assoc();
        if ($result) {
            $response = array('success' => true, 'message' => 'Employee Details Fetched successfully', 'data' => $result);
            echo json_encode($response);
            die();
        } else {
            $response = array('success' => false, 'message' => 'Failed to fetch details');
            echo json_encode($response);
            die();
        }
    }
}
