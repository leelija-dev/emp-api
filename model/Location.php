<?php
namespace model;

require_once("config.php");

class Location extends \db\DatabaseConnection
{
    //get country name
    public function getCountryName(): void
    {
        header('Content-Type: application/json');
        $query = "SELECT name, iso3 FROM countries"; 
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $countries = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($countries) {
                $response = array('success' => true, 'message' => 'Country Names Fetched successfully', 'data' => $countries);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }

    // get country details using country id
    public function getCountryDetails($id): never
    {
        header('Content-Type: application/json');
        $query = "SELECT name, iso3, numeric_code, iso2, phonecode, capital, currency, 
        currency_name, currency_symbol, tld, native, region, subregion, timezones 
        FROM countries WHERE id = ?";
        //$query = "SELECT * FROM countries WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {

            $stmt->bind_param('i', $id);
            $stmt->execute();

            $result = mysqli_stmt_get_result($stmt);
            $countryDetails = mysqli_fetch_assoc($result);

            if ($countryDetails) {
                $response = array(
                    'success' => true,
                    'message' => 'Country Details Fetched successfully',
                    'data' => $countryDetails
                );
                echo json_encode($response);
                die();
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to fetch details'
                );
                echo json_encode($response);
                die();
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
            echo json_encode($response);
            die();
        }
    }

    //get states name by country id
    public function getStatesByCountry($country_Id): never
    {
        header('Content-Type: application/json');

        $query = "SELECT name FROM states WHERE country_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            $stmt->bind_param('i', $country_Id);
            $stmt -> execute();

            $result = mysqli_stmt_get_result($stmt);
            $stateDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
            //$stateDetails = mysqli_fetch_assoc($result);
            
            if ($stateDetails) {
                $response = array(
                    'success' => true,
                    'message' => 'States Fetched successfully',
                    'data' => $stateDetails
                );
                echo json_encode($response);
                die();
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to fetch details'
                );
                echo json_encode($response);
                die();
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
            echo json_encode($response);
            die();
        }
    }

    // get Cities name using state id
    public function getCitiesByState($state_id): never
    {
        header('Content-Type: application/json');

        $query = "SELECT name FROM cities WHERE state_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            $stmt->bind_param('i', $state_id);
            $stmt -> execute();

            $result = mysqli_stmt_get_result($stmt);
            $cityDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            if ($cityDetails) {
                $response = array(
                    'success' => true,
                    'message' => 'cities Fetched successfully',
                    'data' => $cityDetails
                );
                echo json_encode($response);
                die();
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to fetch details'
                );
                echo json_encode($response);
                die();
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
            echo json_encode($response);
            die();
        }
    }
}
