<?php
require_once('./Models/DeliveryPointDataSet.php');
require_once('./Models/DeliveryStatusTypeDataSet.php');
require_once('./Models/UserDataSet.php');
require_once('authenticate.php');



$q = $_REQUEST["q"];
$currentPage = $_REQUEST['page'] ?? 1; //Sets page to current page number
$pageCount = 0;
$recordsPerPage = 10;
$deliveryPointDataSet = new DeliveryPointDataSet();

if(isset($_SESSION['login'])) { //Makes sure the user is logged in before any of the backend functionalities are carried out
    if ($q === "deliveries" && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Checks if the query is deliveries
        if (strtolower($_SESSION['login']->getUserType()) === 'manager') {
            $deliveryPointData = $deliveryPointDataSet->fetchAllDeliveries();

        } else {
            $deliveryPointData = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId());
        }
        $deliveryPoints = [];
        $totalItems = count($deliveryPointData); //Following code used for pagination, Counts the amount of items
        $pageCount = ceil($totalItems / $recordsPerPage); //Set the page count by dividing totalItems by the amount of records to be displayed (Rounded up)
        $offset = ($currentPage - 1) * $recordsPerPage; //The data is shown is determined by the current page and total records to be displayed
        $deliveryPointData = array_slice($deliveryPointData, $offset, $recordsPerPage); //filtered records made into 10 records
        foreach($deliveryPointData as $delivery) { //The following code assigns easy to use array keys and values to later be accessed when converted to json, This also could've been done in the DeliveryPoint class using JsonSeralize.
            $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName(), "addressOne" => $delivery->getAddressOne(), "addressTwo" => $delivery->getAddressTwo(), "postcode" => $delivery->getPostcode(), "lat" => $delivery->getLat(), "lng" => $delivery->getLng(), "username" => $delivery->getUsername(), "status" => $delivery->getStatusText(), "photo" => $delivery->getDelPhoto());
        }
        echo json_encode($deliveryPoints);
    }

    if($q === "total") { //Used to find the total amount of records available to the logged in user, Managers have access to all records
        if (strtolower($_SESSION['login']->getUserType()) === 'manager') {
            $deliveryPointData = $deliveryPointDataSet->fetchAllDeliveries();

        } else {
            $deliveryPointData = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId());
        }
        echo json_encode(count($deliveryPointData));
    }

    if($q === "record" && $_REQUEST['id'] && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Uses the id to select the corresponding record
        $deliveryPoints = [];
        if (strtolower($_SESSION['login']->getUserType()) === 'manager') {
            $deliveryPointData = $deliveryPointDataSet->fetchDelivery($_REQUEST['id']);

        } else {
            $deliveryPointData = $deliveryPointDataSet->fetchDelivery($_REQUEST['id']);
        }

        foreach($deliveryPointData as $delivery) {
            $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName(), "addressOne" => $delivery->getAddressOne(), "addressTwo" => $delivery->getAddressTwo(), "postcode" => $delivery->getPostcode(), "lat" => $delivery->getLat(), "lng" => $delivery->getLng(), "username" => $delivery->getUsername(), "status" => $delivery->getStatusText(), "photo" => $delivery->getDelPhoto(), "statusCode" => $delivery->getStatusCode(), "delivererID" => $delivery->getUserID());
        }

        echo json_encode($deliveryPoints);
    }

    if($q === "updateRecord" && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Used to update the record with provided data
        $data = json_decode($_REQUEST['data'], 1);
        $deliveryPointData = $deliveryPointDataSet->updateDeliveryPoint($data);
        echo json_encode($deliveryPointData);
    }

    if($q === "createRecord" && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) {
        if (strtolower($_SESSION['login']->getUserType()) === 'manager')  {
            $data = json_decode($_REQUEST['data'], 1);
            $deliveryPointData = $deliveryPointDataSet->createDeliveryPoint($data);
            echo json_encode($deliveryPointData);
        }
    }

    if ($q === "search" && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Used for the live search functionality
        $currentPage = $_REQUEST['page'] ?? 1; //Sets page to current page number
        $search = $_REQUEST['search'];

        if(isset($_REQUEST['filters']))
        {
            $data=[];
            $filters = json_decode($_REQUEST['filters']);

            foreach($filters as $filter) {
                $data[$filter] = $search;
            }

            $deliveryPointData = $deliveryPointDataSet->fetchFilteredDeliveries($data);

        } else {
            $deliveryPointData = $deliveryPointDataSet->fetchSearchDeliveries($search);
        }

        $searchResultPerPage = 5;

        $totalItems = count($deliveryPointData);
        $searchCount = ceil($totalItems / $searchResultPerPage);
        $offset = ($currentPage -1) * $searchResultPerPage;
        $deliveryPointData = array_slice($deliveryPointData, $offset, $searchResultPerPage);

        $deliveryPoints = [];
        foreach($deliveryPointData as $delivery) {
            $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName(), "addressOne" => $delivery->getAddressOne());
        }


        echo json_encode($deliveryPoints);
    }

    if($q === 'statusTypes' && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Used to fetch statusTypes (Used for easy updating of delivery status)
        $statusTypeDataSet = new DeliveryStatusTypeDataSet();
        $statusTypeData = $statusTypeDataSet->fetchStatusTypes();
        $statusTypes = [];
        foreach($statusTypeData as $status) {
            $statusTypes[] = array("statusCode" => $status->getStatusCode(),"statusText" => $status->getStatusText());
        }
        echo json_encode($statusTypes);
    }
    if($q === 'deliverers' && ($_REQUEST['token'] === $_SESSION['ajaxToken'])) { //Used to fetch the deliverers making it easier to assign deliverers to different deliveries
        $usersDataSet = new UserDataSet();
        $usersData = $usersDataSet->fetchAllUsers();
        $users = [];
        foreach($usersData as $user) {
            $users[] = array("id" => $user->getUserID(),"username" => $user->getUsername());
        }
        echo json_encode($users);
    }

    if($q === 'delete' && $_REQUEST['id'] && (strtolower($_SESSION['login']->getUserType())) === 'manager') { //Used to delete records but must be a manager to do so
        $deliveryPointData = $deliveryPointDataSet->deleteDeliveryPoint($_REQUEST['id']);
        echo json_encode($deliveryPointData);
    }
}


