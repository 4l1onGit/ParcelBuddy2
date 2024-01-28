<?php

require_once('authenticate.php');
require_once("./Models/DeliveryPointDataSet.php");
require_once("./Models/DeliveryPoint.php");
require_once("./Models/UserType.php");
require_once("./Models/UserTypeDataSet.php");
require_once("./Models/DeliveryStatusTypeDataSet.php");

$view = new stdClass();
$view->pageTitle = 'Dashboard';
$view->errors = [];
$view->success = [];

if (isset($_SESSION['login'])) { //Checks if the user is authenticated
    if (strtolower($_SESSION['login']->getUserType()) === 'manager') { //Verifies the logged-in user is a manager
        $deliveryStatusDataSet = new DeliveryStatusTypeDataSet();
        $view->statusDataSet = $deliveryStatusDataSet->fetchStatusTypes();
        $userDataSet = new UserDataSet();
        $view->userDataSet = $userDataSet->fetchAllUsers();
        $deliveryPointDataSet = new DeliveryPointDataSet();
        $view->deliveryDataSet = $deliveryPointDataSet->fetchAllDeliveries();
        $userTypeDataSet = new UserTypeDataSet();
        $view->userTypeDataSet = $userTypeDataSet->fetchUserTypes();

    } else
        header('Location: index.php'); //Redirects user to index as they are not a manager 
} else {
    require_once('./Views/sign-in.phtml'); //If authentication check fails a standard home page is displayed until a valid user logs in
}

if (isset($_POST['createPoint'])) { //Runs when the create point button is submitted
    $data = [];
    if(isset($_POST['inputName'])) { //Goes through input validation to prevent misuse
        try{
            $data['name'] = validateInput($_POST['inputName'], FILTER_SANITIZE_STRING, 'Error: Enter a valid name');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage(); //Errors are set to an array to be used later for the view

        }
    }
    if(isset($_POST['inputAddress'])) {
        try{
            $data['addressOne'] = validateInput($_POST['inputAddress'], FILTER_SANITIZE_STRING, 'Error: Enter a valid address');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputAddress2'])) {
        try{
            $data['addressTwo'] = validateInput($_POST['inputAddress2'], FILTER_SANITIZE_STRING, 'Error: Enter a valid address');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputPostcode'])) {
        try{
            $data['postcode'] = validateInput($_POST['inputPostcode'], FILTER_SANITIZE_STRING, 'Error: Enter a valid postcode');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputDeliverer'])) {
        try{
            $data['deliverer'] = validateInput($_POST['inputDeliverer'], FILTER_SANITIZE_STRING, 'Error: Enter a deliverer');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputLat'])) {
        try{
            $data['lat'] = validateInput($_POST['inputLat'], FILTER_VALIDATE_FLOAT, 'Error: Enter Lat');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputLng'])) {
        try{
            $data['lng'] = validateInput($_POST['inputLng'], FILTER_VALIDATE_FLOAT, 'Error: Enter Lng');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }

    if(count($view->errors) === 0) {
        $deliveryData = new DeliveryPointDataSet();

        if ($deliveryData->createDeliveryPoint($data))
            $view->errors[] = 'Successfully created! ' . $data['name'];
        else
            $view->errors[] = '';
    }

}

if (isset($_POST['createUser'])) { //Runs when createUser button is submitted
    if(isset($_POST['inputName']) && $_POST['inputName'] !== '') { //Statements to check all inputs are filled correctly
        try{
            $username = validateInput($_POST['inputName'], FILTER_SANITIZE_STRING, 'Error: Enter a valid Name');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    } else {
        $view->errors[] = 'Name cannot be empty';
    }
    if(isset($_POST['inputPassword']) && $_POST['inputPassword'] !== '') {
        try{
            $password = validateInput($_POST['inputPassword'], FILTER_SANITIZE_STRING, 'Error: Enter a valid Password');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    } else {
        $view->errors[] = 'Password cannot be empty';
    }
    if(isset($_POST['inputUserType']) && $_POST['inputUserType'] !== '') {
        try{
            $userType = validateInput($_POST['inputUserType'], FILTER_VALIDATE_INT, 'Error: Select a valid User Type');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }

    if(isset($username) && isset($password) && isset($userType)) {
        $userData = new UserDataSet();
        if ($userData->createUser($username, password_hash($password, PASSWORD_DEFAULT), $userType)) {
            $view->errors[] = 'Successfully created!: ' . $username;
        } else {
            $view->errors[] = 'Failed to create user! Please Try again later';
        }
    }
}

if (isset($_POST['deletePoint'])) { //Runs when deletePoint button is submitted
    if(isset($_POST['inputDeliveryID']) && $_POST['inputDeliveryID'] !== '') {
        try{
            $id = validateInput($_POST['inputDeliveryID'], FILTER_VALIDATE_INT, 'Error: Select an ID');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();

        }
    }
    if(isset($id)) { //Verifies an id has been selected
        $deliveryData = new DeliveryPointDataSet();
        if ($deliveryData->deleteDeliveryPoint($id))
            $view->errors[] = 'Successfully deleted delivery point: ' . $id;
        else
            $view->errors[] = 'Failed to delete delivery point!';
    }

}

/**
 * @throws Exception
 */
function validateInput($input, $filter, $errMsg) //Used throughout to validate Inputs to prevent malicious attacks/misuse
{
    $filtered = filter_var($input, $filter); //Uses php built in filters to validate data
    if ($filtered === false || $filtered === null || $filtered === '') {
        if ($errMsg !== null || '') { //If an error message is provided the message will be used as the exception
            throw new Exception($errMsg);
        } else {
            throw new Exception('Invalid Input'); //Basic error message if message not provided
        }
    }

    return $filtered;
}

if (isset($_POST['updatePoint'])) { //Runs when updatePoint button is submitted
    $data = [];

    if(isset($_POST['inputID']) && $_POST['inputID'] !== '') { //Checks if the value is set
        try{
            $data['id'] = validateInput($_POST['inputID'], FILTER_VALIDATE_INT, 'Error: Enter a valid ID');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();

        }
    }
    if(isset($_POST['inputName']) && $_POST['inputName'] !== '') {
        try{
            $data['name'] = validateInput($_POST['inputName'], FILTER_SANITIZE_STRING, 'Error: Enter a valid name');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();

        }
    }
    if(isset($_POST['inputAddress']) && $_POST['inputAddress'] !== '') {
        try{
            $data['addressOne'] = validateInput($_POST['inputAddress'], FILTER_SANITIZE_STRING, 'Error: Enter a valid address');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputAddress2']) && $_POST['inputAddress2'] !== '') {
        try{
            $data['addressTwo'] = validateInput($_POST['inputAddress2'], FILTER_SANITIZE_STRING, 'Error: Enter a valid address');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputPostcode']) && $_POST['inputPostcode'] !== '') {
        try{
            $data['postcode'] = validateInput($_POST['inputPostcode'], FILTER_SANITIZE_STRING, 'Error: Enter a valid postcode');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputDeliverer']) && $_POST['inputDeliverer'] !== '') {
        try{
            $data['deliverer'] = validateInput($_POST['inputDeliverer'], FILTER_SANITIZE_STRING, 'Error: Enter a deliverer');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputLat']) && $_POST['inputLat'] !== '') {
        try{
            $data['lat'] = validateInput($_POST['inputLat'], FILTER_VALIDATE_FLOAT, 'Error: Enter Lat');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if(isset($_POST['inputLng']) && $_POST['inputLng'] !== '') {
        try{
            $data['lng'] = validateInput($_POST['inputLng'], FILTER_VALIDATE_FLOAT, 'Error: Enter Lng');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if (isset($_POST['statusInput']) && $_POST['statusInput'] !== '') {
        try {
            $data['status'] = validateInput($_POST['statusInput'], FILTER_VALIDATE_INT, 'Error: Invalid status');
        } catch (Exception $ex) {
            $view->errors[] = $ex->getMessage();
        }
    }
    if (isset($data['id'])) { //As long as an id is provided the update will continue using the provided fields
        $deliveryData = new DeliveryPointDataSet();
        if ($deliveryData->updateDeliveryPoint($data)) {
            $view->errors[] = 'Successfully updated delivery point: ' . $data['id'];
        } else {

            $view->errors[] = 'Failed to update delivery point!';
        }
    } else {
        $view->errors[] = 'ID is required to update';
    }


}

if(isset($_SESSION['login'])) {
    require_once('./Views/dashboard.phtml');
}