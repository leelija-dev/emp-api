<?php

// use model\Login;

// $Login = new Login();

// function handleLoginRequest($method, $segments)
// {
//     global $Login;
// $env = getenv('JWT_SECRET_KEY');
// // print_r($env);  die();
//     $second_segment = isset($segments[1]) ? $segments[1] : null;
//     $third_segment = isset($segments[2]) ? $segments[2] : null;
//     $forth_segment = isset($segments[3]) ? $segments[3] : null;
//     // print_r($second_segment);  die();
//     switch ($second_segment) {
//         case 'login':
//             if ($method == 'POST' && $third_segment == null && $forth_segment == null) {
//                 $email = $_POST['email'];
//                 $password = $_POST['password'];
//                 $result = $Login->getUser($email, $password);
//                 if ($result) {
//                 }
//             }
//     }
// }

use model\Login;
require 'vendor/autoload.php'; // Load the JWT library
use \Firebase\JWT\JWT;

$Login = new Login();

function handleLoginRequest($method, $segments)
{
    global $Login;

    // Your JWT secret key (should be kept in a secure place, not hardcoded in production)
    $key = getenv('JWT_SECRET_KEY');

    $second_segment = isset($segments[1]) ? $segments[1] : null;
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;

    switch ($second_segment) {
        case 'login':
            if ($method == 'POST' && $third_segment == null && $forth_segment == null) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                // Get user by email and password
                $result = $Login->getUser($email, $password);

                if ($result) {
                    // Assuming $result is an associative array with user data
                    $user_id = $result['id'];
                    $username = $result['username'];

                    // Prepare JWT payload
                    $issuedAt = time();
                    $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

                    $payload = array(
                        "iss" => "localhost", // Issuer (your domain or application)
                        "iat" => $issuedAt,   // Issued at
                        "exp" => $expirationTime, // Expiration time
                        "data" => array(
                            "id" => $user_id,
                            "username" => $username
                        )
                    );

                    // Generate JWT token
                    $jwt = JWT::encode($payload, $key, 'HS256');

                    // Return response with JWT token
                    echo json_encode([
                        "status" => "success",
                        "jwt" => $jwt,
                        "message" => "Login successful"
                    ]);
                } else {
                    // If login fails
                    echo json_encode([
                        "status" => "error",
                        "message" => "Invalid email or password"
                    ]);
                }
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
?>

