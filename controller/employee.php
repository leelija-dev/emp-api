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
            //             if ($method == 'PUT' && $third_segment == 'update' && is_numeric($forth_segment)) {

            //                 // parse_str(file_get_contents("php://input"), $put_vars);
            //                 $id = intval($forth_segment);
            //                 // print_r($id);  die();
            //                 $exist = $Employee->checkIfEmployeeExists($id);
            //                 if ($exist) {
            //                     // $data['name'] = $_REQUEST['name'];
            //                     // $data['designation'] = $_REQUEST['designation'];
            //                     // $data['doj'] = $_REQUEST['doj'];
            //                     // $data['gender'] = $_REQUEST['gender'];
            //                     // $data['phone'] = $_REQUEST['phone'];
            //                     // $data['email'] = $_REQUEST['email'];
            //                     // $data['password'] = $_REQUEST['password'];
            //                     // $data['status'] = $_REQUEST['status'];
            //                     // $data['featured'] = $_REQUEST['featured'];

            //                     // parse_str(file_get_contents("php://input"), $put_vars);
            // parse_str(file_get_contents('php://input'), $_PUT);
            //                     $data['name'] = $_PUT['name'] ?? null;
            //                     print_r(json_encode($data['name']));  die();
            //                     $data['designation'] = $put_vars['designation'] ?? null;
            //                     $data['doj'] = $put_vars['doj'] ?? null;
            //                     $data['gender'] = $put_vars['gender'] ?? null;
            //                     $data['phone'] = $put_vars['phone'] ?? null;
            //                     $data['email'] = $put_vars['email'] ?? null;
            //                     $data['password'] = $put_vars['password'] ?? null;
            //                     $data['status'] = $put_vars['status'] ?? null;
            //                     $data['featured'] = $put_vars['featured'] ?? null;

            //                     if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            //                         $data['image'] = $_FILES['image']['name'];
            //                         $image = $_FILES['image']['name'];
            //                         $doc_tmp_path = $_FILES['image']['tmp_name'];
            //                         $target_directory = "public/emp-docs/";
            //                         $doc_path = $target_directory . basename($image);

            //                         if (move_uploaded_file($doc_tmp_path, $doc_path)) {
            //                             $response = $Employee->updateEmployeeDetails($id, $data);
            //                             echo $response;
            //                         } else {
            //                             echo "Error moving the uploaded file.";
            //                         }
            //                     } else {
            //                         $emp = $Employee->getempDetails($id);
            //                         $data['image'] = $emp['image'];
            //                         $response = $Employee->updateEmployeeDetails($id, $data);
            //                         echo $response;
            //                     }
            //                 } else {
            //                     echo "The Employee Not Found";
            //                 }
            if ($method == 'PUT' && $third_segment == 'update' && is_numeric($forth_segment)) {
                $id = intval($forth_segment);
                $exist = $Employee->checkIfEmployeeExists($id);

                if ($exist) {
                    // Get the Content-Type header
                    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

                    // Check if the content type is multipart/form-data
                    if (strpos($contentType, 'multipart/form-data') !== false) {
                        // Get the boundary
                        preg_match('/boundary=(.*)$/', $contentType, $matches);
                        $boundary = $matches[1];

                        // Get raw data from the input stream
                        $rawData = file_get_contents("php://input");

                        // Split the data into parts using the boundary
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
                                        // Regular form field
                                        $data[$fieldName] = $value;
                                    }
                                }
                            }
                        }

                        // Merge file data into $data array if any files were uploaded
                        if (!empty($fileData)) {
                            $data = array_merge($data, $fileData);
                        }

                        // Check if all required data is present
                        if (!empty($data)) {
                            // Handle image update or fallback to old image
                            if (!empty($data['image'])) {
                                // Image was uploaded and moved successfully
                                $response = $Employee->updateEmployeeDetails($id, $data);
                                echo $response;
                            } else {
                                // No new image, keep the existing image
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
            }
            // else if ($method == 'POST' && $third_segment == 'update-doc' && is_numeric($forth_segment)) {
            //     $id = $forth_segment;
            //     $emp_id = $_REQUEST['emp_id'];
            //     $updated_by = $_REQUEST['updated_by'];
            //     if (isset($_FILES['doc_name']) && $_FILES['doc_name']['error'] == UPLOAD_ERR_OK) {
            //         $doc_name = $_FILES['doc_name']['name'];
            //         $doc_tmp_path = $_FILES['doc_name']['tmp_name'];
            //         $target_directory = "public/emp-docs/";
            //         $doc_path = $target_directory . basename($doc_name);

            //         if (move_uploaded_file($doc_tmp_path, $doc_path)) {
            //             $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
            //             echo $response;
            //         } else {
            //             echo "Error moving the uploaded file.";
            //         }
            //     } else {
            //         $data = $Employee->getDocDetails($id);
            //         $doc_name = $data['doc_name'];
            //         $doc_path = $data['doc_path'];
            //         $response = $Employee->updateEmployeeDoc($id, $emp_id, $doc_name, $doc_path, $updated_by);
            //         echo $response;
            //     }
            // }
            if ($method == 'PUT' && $third_segment == 'update-doc' && is_numeric($forth_segment)) {
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

                    // Split the data using the boundary
                    $parts = explode("--" . $boundary, $rawData);

                    $data = [];
                    $fileData = [];

                    // Parse each part of the form data
                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition: form-data;') !== false) {
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
                                    // Regular form field
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
