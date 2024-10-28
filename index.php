<?php
// use model\Employee;
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// } else {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'model/Employee.php';
require_once 'model/Ticket.php';
require_once 'model/Login.php';
require_once 'model/Leave.php';
require_once 'model/Location.php';


$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$url_path = ltrim($url, '/');
$segments = explode('/', $url_path);

$first_segment = isset($segments[0]) ? $segments[0] : null;
$second_segment = isset($segments[1]) ? $segments[1] : null;
switch ($second_segment) {
    case 'employees':
        require_once 'controller/employee.php';
        handleEmployeeRequest($method, $segments);
        break;
    case 'emp':
        require_once 'controller/employee.php';
        handleEmployeeRequest($method, $segments);
        break;
    case 'employee-details':
        require_once 'controller/employee.php';
        handleEmployeeRequest($method, $segments);
        break;

    case 'tickets':
        require_once 'controller/ticket.php';
        handleTicketRequest($method, $segments);
        break;

    case 'ticket':
        require_once 'controller/ticket.php';
        handleTicketRequest($method, $segments);
        break;
    case 'ticket-response':
        require_once 'controller/ticket.php';
        handleTicketRequest($method, $segments);
        break;
    case 'login':
        require_once 'controller/login.php';
        handleLoginRequest($method, $segments);
        break;
    case 'leave':
        require_once 'controller/leave.php';
        handleLeaveRequest($method, $segments);
        break;
    case 'location':
        require_once 'controller/location.php';
        handleLocationRequest($method, $segments);
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(array('success' => false, 'message' => 'Endpoint not found.'));
        exit();
}
