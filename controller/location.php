<?php

use model\Location;

require 'vendor/autoload.php';

$Location = new Location();

function handleLocationRequest($method, $segments)
{
    global $Location;

    $second_segment = isset($segments[1]) ? $segments[1] : null;
    // // print_r($second_segment);  die();
    $third_segment = isset($segments[2]) ? $segments[2] : null;
    $forth_segment = isset($segments[3]) ? $segments[3] : null;
    $fifth_segment = isset($segments[4]) ? $segments[4] : null;

    // require_once dirname(__DIR__) .'/SegmentHandler.php';

    // Get segment values
    // $segmentValues = getSegmentValues($segments);
    // $second_segment = $segmentValues['second'];
    // $third_segment = $segmentValues['third'];
    // $forth_segment = $segmentValues['forth'];
    // $fifth_segment = $segmentValues['fifth'];

    switch ($second_segment) {
        case 'location':
            if ($method == 'GET' && $third_segment == 'countries' && $forth_segment == null) {
                $response = $Location->getCountryName();
                echo $response;
            }


            //this section is for country details
            else if ($method == 'GET' && $third_segment == 'country' && is_numeric($third_segment) && $forth_segment == null) {
                $id = $third_segment;

                $response = $Location->getCountryDetails($id);
                echo $response;
            }

            //This section for States By Country
            else if ($method == 'GET' && $third_segment == "states" && is_numeric($forth_segment)) {
                $id = $forth_segment;
                print_r($id);

                $response = $Location->getStatesByCountry($id);
                echo $response;
            }

            //this section is for Cities by state
            else if ($method == 'GET' && $third_segment == 'state' && $forth_segment == 'cities' && is_numeric($fifth_segment)) {
                $id = $fifth_segment;
                print_r($id);

                $response = $Location->getCitiesByState($id);
                echo $response;
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
