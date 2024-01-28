<?php
require_once('authenticate.php');
require_once('./Models/DeliveryPointDataSet.php');
require_once('./Models/DeliveryStatusTypeDataSet.php');
require_once('./Models/UserDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Home';

$view->errors = [];
$view->currentPage = $_GET['page'] ?? 1; //Sets page to current page number
$view->pageCount = 0;
$view->recordsPerPage = 10;




if (isset($_SESSION['login'])) {
    $deliveryPointDataSet = new DeliveryPointDataSet();
    $deliveryStatusDataSet = new DeliveryStatusTypeDataSet();
    $view->statusDataSet = $deliveryStatusDataSet->fetchStatusTypes();
    if(strtolower($_SESSION['login']->getUserType()) === 'manager') {
        $userDataSet = new UserDataSet();
        $view->userDataSet = $userDataSet->fetchAllUsers();
    }


    if (isset($_POST['filterButton'])) { //If filter button is submitted the following code will run
        $data = [];

        if(isset($_POST['pointIDInput']) && $_POST['pointIDInput'] !== '') { //If statements to check what filters have been set and if the filters are valid
            try{
                $data['id'] = validateInput($_POST['pointIDInput'], FILTER_VALIDATE_INT, 'Error: ID Must be an integer');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }
        if (isset($_POST['nameInput']) && $_POST['nameInput'] !== '' ) {
            try {
                $data['name'] = validateInput($_POST['nameInput'], FILTER_SANITIZE_STRING, 'Error: Invalid Name');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }
        if (isset($_POST['addressOneInput']) && $_POST['addressOneInput'] !== '') {
            try {
                $data['addressOne'] = validateInput($_POST['addressOneInput'], FILTER_SANITIZE_STRING, 'Error: Invalid Address One');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }
        if (isset($_POST['addressTwoInput']) && $_POST['addressTwoInput'] !== '') {
            try {
                $data['addressTwo'] = validateInput($_POST['addressTwoInput'], FILTER_SANITIZE_STRING, 'Error: Invalid Address Two');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }
        if (isset($_POST['postcodeInput']) && $_POST['postcodeInput'] !== '') {
            try {
                $data['postcode'] = validateInput($_POST['postcodeInput'], FILTER_SANITIZE_STRING, 'Error: Invalid postcode');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }

        if (isset($_POST['delivererInput']) && $_POST['delivererInput'] !== '' && strtolower($_SESSION['login']->getUserType()) === 'manager') {
            try {
                $data['deliverer'] = validateInput($_POST['delivererInput'], FILTER_SANITIZE_STRING, 'Error: Invalid deliverer');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }


        if (isset($_POST['statusInput']) && $_POST['statusInput'] !== '') {
            try {
                $data['status'] = validateInput($_POST['statusInput'], FILTER_SANITIZE_STRING, 'Error: Invalid status');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }

        if (isset($_POST['latInput']) && $_POST['latInput'] !== '') {
            try {
                $data['lat'] = validateInput($_POST['latInput'], FILTER_VALIDATE_FLOAT, 'Error: Invalid latitude');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
            }
        }

        if (isset($_POST['lngInput']) && $_POST['lngInput'] !== '') {
            try {
                $data['lng'] = validateInput($_POST['lngInput'], FILTER_VALIDATE_FLOAT, 'Error: Invalid longitude');
            } catch (Exception $ex) {
                $view->errors[] = $ex->getMessage();
                }
        }

        if (strtolower($_SESSION['login']->getUserType()) === 'manager') { //if the user is a manager then the user will have access to all data

            $view->filteredDeliveryDataSet = $deliveryPointDataSet->fetchFilteredDeliveries($data);
            $view->deliveryDataSet = $deliveryPointDataSet->fetchAllDeliveries();
        }  else { //If not the user will only have delivery points associated to the user

            $view->filteredDeliveryDataSet = $deliveryPointDataSet->fetchFilteredDeliveries($data, $_SESSION['login']->getId());
            $view->deliveryDataSet = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId());
        }

        $view->totalItems = count($view->filteredDeliveryDataSet); //Following code used for pagination, Counts the amount of items
        $view->pageCount = ceil($view->totalItems / $view->recordsPerPage); //Set the page count by dividing totalItems by the amount of records to be displayed (Rounded up)
        $view->offset = ($view->currentPage - 1) * $view->recordsPerPage; //The data is shown is determined by the current page and total records to be displayed
        $view->filteredDeliveryDataSet = array_slice($view->filteredDeliveryDataSet, $view->offset, $view->recordsPerPage); //filtered records made into 10 records
    }
    else {
        if (strtolower($_SESSION['login']->getUserType()) === 'manager') { //Checks if the authenticated user is a manager
            $view->deliveryDataSet = $deliveryPointDataSet->fetchAllDeliveries(); //If the user is a manager all delivery points will be displayed

        } else {
            $view->deliveryDataSet = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId()); //Only delivery points associated with the deliverer will be displayed
        }
        $view->totalItems = count($view->deliveryDataSet);
        $view->pageCount = ceil($view->totalItems / $view->recordsPerPage);
        $view->offset = ($view->currentPage - 1) * $view->recordsPerPage;
        $view->deliveryDataSet = array_slice($view->deliveryDataSet, $view->offset, $view->recordsPerPage);
    }

    require_once('./Views/index.phtml');
}
else {
    require_once('./Views/sign-in.phtml'); //Shows standard home page until user signs in
}

/**
 * @throws Exception
 */
function validateInput($input, $filter, $errMsg) { //Used throughout to validate Inputs to prevent malicious attacks/misuse
    $filtered = filter_var($input, $filter);
    if($filtered === false || $filtered === null){ //Uses php built in filters to validate data
        if($errMsg !== null || '') { //If an error message is provided the message will be used as the exception
            throw new Exception($errMsg);
        } else {
            throw new Exception('Invalid Input'); //Basic error message if message not provided
        }
    }
    return $filtered;
}

