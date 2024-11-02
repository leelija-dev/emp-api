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

                if ($third_segment && is_numeric($third_segment)) {
                    // Fetch employee details by ID
                    $id = intval($third_segment);
                    $response = $Employee->getEmployeeDetails($id);
                    echo $response;
                } else if ($third_segment == 'details') {
                    $response = $Employee->getTeamMembers();
                    echo $response;
                } else if ($third_segment == 'featured') {
                    $response = $Employee->getFeatured();
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
            if ($method == 'PUT' && $third_segment == 'update' && is_numeric($forth_segment)) {
                $id = intval($forth_segment);
                $exist = $Employee->checkIfEmployeeExists($id);

                if ($exist) {
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
                                // Parse each part for form fields and files
                                if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                    $fieldName = $nameMatches[1];
                                    // Get value or file content
                                    $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));

                                    // Handle file upload manually
                                    if (strpos($part, 'filename=') !== false) {
                                        // This is a file upload part
                                        $fileStartPos = strpos($part, "\r\n\r\n") + 4;
                                        $fileContent = substr($part, $fileStartPos, strpos($part, "--") - $fileStartPos);
                                        $fileName = trim(preg_match('/filename="([^"]+)"/', $part, $fileNameMatches) ? $fileNameMatches[1] : '');

                                        if (!empty($fileName)) {
                                            // Save the file manually
                                            $target_directory = "public/emp-docs/";
                                            $target_file = $target_directory . basename($fileName);
                                            file_put_contents($target_file, $fileContent);
                                            $fileData[$fieldName] = $fileName; // Store file name in the data
                                        }
                                    } else {
                                        $data[$fieldName] = $value;
                                    }
                                }
                            }
                        }

                        if (!empty($fileData)) {
                            $data = array_merge($data, $fileData);
                        }

                        if (!empty($data)) {
                            if (!empty($data['image'])) {
                                $response = $Employee->updateEmployeeDetails($id, $data);
                                echo $response;
                            } else {
                                $emp = $Employee->getempDetails($id);
                                $data['image'] = $emp['image'];

                                $response = $Employee->updateEmployeeDetails($id, $data);
                                echo $response;
                            }
                        } else {
                            echo "No data to update.";
                        }
                    } else {
                        echo "Content-Type must be multipart/form-data";
                    }
                } else {
                    echo "Employee not found.";
                }
            } else if ($method == 'GET' && $third_segment == 'address' && is_numeric($forth_segment)) {
                $emp_id = $forth_segment;
                // print_r($emp_id); die();
                $response = $Employee->getEmployeeAddress($emp_id);
                echo $response;
            }
            
            else if ($method == 'PUT' && $third_segment == 'update-doc' && is_numeric($forth_segment)) {
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

                                if (strpos($part, 'filename=') !== false) {
                                    $fileStartPos = strpos($part, "\r\n\r\n") + 4;
                                    $fileContent = substr($part, $fileStartPos, strpos($part, "--") - $fileStartPos);
                                    $fileName = trim(preg_match('/filename="([^"]+)"/', $part, $fileNameMatches) ? $fileNameMatches[1] : '');

                                    if (!empty($fileName)) {
                                        $target_directory = "public/emp-docs/";
                                        $target_file = $target_directory . basename($fileName);
                                        file_put_contents($target_file, $fileContent);
                                        $fileData[$fieldName] = $fileName;
                                    }
                                } else {

                                    $data[$fieldName] = $value;
                                }
                            }
                        }
                    }

                    if (!empty($fileData)) {
                        $data = array_merge($data, $fileData);
                    }

                    // Extract required fields
                    $emp_id = $data['emp_id'] ?? null;
                    $updated_by = $data['updated_by'] ?? null;
                    $doc_name = $data['doc_name'] ?? null;

                    // Ensure that required fields are present
                    if ($emp_id && $updated_by) {
                        if ($doc_name) {
                            // Handle the file update with the new file
                            $doc_path = "public/emp-docs/" . basename($doc_name);
                            $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
                            echo $response;
                        } else {
                            // No new file uploaded, fetch existing document details
                            $docDetails = $Employee->getDocDetails($id);
                            $doc_name = $docDetails['doc_name'];
                            $doc_path = $docDetails['doc_path'];
                            $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
                            echo $response;
                        }
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'Emp_id and doc_name is missing.'));
                    }
                } else {
                    echo "Invalid Content-Type. Expected multipart/form-data.";
                }
            } else if ($third_segment && is_numeric($third_segment)) {
                // Fetch employee details by ID
                $id = intval($third_segment);
                $response = $Employee->getEmployeeDetails($id);
                echo $response;
            } elseif ($method == 'PUT' && $third_segment == 'update-address' && is_numeric($forth_segment)) {
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

                    $address_line1 = $data['address_line1'] ?? null;
                    $address_line2 = $data['address_line2'] ?? null;
                    $city = $data['city'] ?? null;
                    $state = $data['state'] ?? null;
                    $pin = $data['pin'] ?? null;
                    $country = $data['country'] ?? null;


                    // Ensure that required fields are present
                    if ($address_line1 && $pin) {

                        $response = $Employee->updateEmpAddress($id, $data);
                        echo $response;
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'address and pin is missing.'));
                    }
                } else {
                    echo "Invalid Content-Type. Expected multipart/form-data.";
                }
            } elseif ($method == 'POST' && $third_segment == 'add') {
                $data = array(
                    'name' => $_POST['name'],
                    'designation' => $_POST['designation'],
                    'doj' => $_POST['doj'],
                    'gender' => $_POST['gender'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                );
                if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    $image = $_FILES['image']['name'];
                    $target_directory = "public/emp-docs/";
                    $new_file_name = time() . "_" . basename($image);
                    $target_path = $target_directory . $new_file_name;

                    // Move the uploaded file to the desired directory
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                        $data['image'] = $new_file_name;
                        $response = $Employee->addEmployee($data);
                    } else {
                        // Handle file moving failure
                        echo json_encode(array('success' => false, 'message' => 'Error moving the uploaded file.'));
                    }
                } else {
                    // If no image is uploaded, process the rest of the data
                    $response = $Employee->addEmployee($data);
                    echo json_encode(array('success' => false, 'message' => 'Image not uploaded.'));
                }
            }

            
            elseif ($method == 'POST' && $third_segment == 'add-doc') {
               
                $data['updated_by'] = $_REQUEST['updated_by'];
                $data['doc_name'] = $_FILES['doc_name']['name'];
                $data['emp_id'] = $_REQUEST['emp_id'];

                if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
                    $doc_name = $_FILES['doc_name']['name'];
                    $target_directory = "public/emp-docs/";
                    $doc_name = time() . "_" . basename($doc_name);
                    $doc_path = $target_directory . $doc_name;
                    $data['doc_path'] = $doc_path;
                    // Move the uploaded file to the desired directory
                    if (move_uploaded_file($_FILES['doc_name']['tmp_name'], $doc_path)) {
                        $data['doc_name'] = $doc_name;
                        $response = $Employee->addEmployeeDoc($data);
                    } else {
                        // Handle file moving failure
                        echo json_encode(array('success' => false, 'message' => 'Error moving the uploaded file.'));
                    }
                } else {
                    // If no image is uploaded, process the rest of the data
                    $response = $Employee->addEmployee($data);
                    echo json_encode(array('success' => false, 'message' => 'Image not uploaded.'));
                }
            } elseif ($method == 'POST' && $third_segment == 'address' && $forth_segment == 'add') {
                $data = array(
                    'emp_id'  => $_POST['emp_id'],
                    'address_line1' => $_POST['address_line1'],
                    'address_line2' => $_POST['address_line2'],
                    'city' => $_POST['city'],
                    'state' => $_POST['state'],
                    'pin' => $_POST['pin'],
                    'country' => $_POST['country'],
                );


                $response = $Employee->addEmployeeAddress($data);
                echo $response;
            }
            elseif ($method == 'PUT' && $third_segment == 'change-password' && is_numeric($forth_segment)) {
                $id = intval($forth_segment);
                $exist = $Employee->checkIfEmployeeExists($id);
                if ($exist) {
                    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
            
                    if (strpos($contentType, 'multipart/form-data') !== false) {
                        preg_match('/boundary=(.*)$/', $contentType, $matches);
                        $boundary = $matches[1];
                        $rawData = file_get_contents("php://input");
                        $parts = explode("--" . $boundary, $rawData);
            
                        $data = [];
                        foreach ($parts as $part) {
                            if (strpos($part, 'Content-Disposition: form-data;') !== false) {
                                if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                    $fieldName = $nameMatches[1];
                                    $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));
                                  
                                    $data[$fieldName] = $value;
                                }
                            }
                        }
            
                        // Check if only password field is provided
                        if (isset($data['password']) && count($data) === 1) {
                            // Update password only
                            $response = $Employee->changePassword($id, $data);
                            echo $response;
                        } else {
                            // Handle the image and other fields as before
                            if (!empty($data['image'])) {
                                $response = $Employee->updateEmployeeDetails($id, $data);
                                echo $response;
                            } else {
                                $emp = $Employee->getempDetails($id);
                                $data['image'] = $emp['image'];
                                $response = $Employee->updateEmployeeDetails($id, $data);
                                echo $response;
                            }
                        }
                    } else {
                        echo "Content-Type must be multipart/form-data";
                    }
                } else {
                    echo "Employee not found.";
                }
            }
            

            break;
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(array('success' => false, 'message' => 'Invalid Employee Route.'));
            exit();
    }
}
