<?php
$con = mysqli_connect("localhost", "root", "", "leelija_db");

function getEmployeeDetails($id){
    global $con;
    $sql = "SELECT * FROM employees WHERE emp_id ='$id'";
    $rs = $con->query($sql);
    $result = $rs->fetch_assoc();
    print_r($result);
}

?>

