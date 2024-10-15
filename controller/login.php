<?php
use model\Login;
require 'vendor/autoload.php'; 
use \Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();  // Load environment variables from .env file

$Login = new Login();

function handleLoginRequest($method, $segments)
{
    global $Login;

    // Your JWT secret key (loaded from the .env file)
    $key = $_ENV['JWT_SECRET'];
    $second_segment = isset($segments[1]) ? $segments[1] : null;
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;

    switch ($second_segment) {
        case 'login':
            if ($method == 'POST' && $third_segment == null && $forth_segment == null) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                $result = $Login->getUser($email, $password);

                if ($result) {
                    $user_id = $result['emp_id'];
                    $username = $result['email'];
                    
                    // Prepare JWT payload
                    $issuedAt = time();
                    $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

                    $payload = array(
                        "iss" => "localhost", // Issuer 
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
                        "token" => $jwt,
                        "message" => "Login successful",
                        'data' => $result
                    
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

