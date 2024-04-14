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

if(isset($_SESSION['login'])) {
    if ($q === "deliveries") {

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
        foreach($deliveryPointData as $delivery) {
            $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName(), "addressOne" => $delivery->getAddressOne(), "addressTwo" => $delivery->getAddressTwo(), "postcode" => $delivery->getPostcode(), "lat" => $delivery->getLat(), "lng" => $delivery->getLng(), "username" => $delivery->getUsername(), "status" => $delivery->getStatusText(), "photo" => $delivery->getDelPhoto());
        }
        echo json_encode($deliveryPoints);
    }

    if($q === "total") {
        if (strtolower($_SESSION['login']->getUserType()) === 'manager') {
            $deliveryPointData = $deliveryPointDataSet->fetchAllDeliveries();

        } else {
            $deliveryPointData = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId());
        }
        echo json_encode(count($deliveryPointData));
    }

    if($q === "record" && $_REQUEST['id']) {
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

    if($q === "updateRecord" && $_REQUEST['id']) {
        $data = $_REQUEST['data'];
        
    }

    if ($q === "search") {
        $currentPage = $_GET['page'] ?? 1; //Sets page to current page number
        $searchQuery = $_GET['search'];
        $searchPage = 1;
        $searchResultPerPage = 10;

        $deliveryPointData = $deliveryPointDataSet->fetchSearchDeliveries($searchQuery);
        $totalItems = count($deliveryPointData);
        $searchCount = ceil($totalItems / $searchResultPerPage);
        $offset = ($searchPage -1) * $searchResultPerPage;
        $deliveryPointData = array_slice($deliveryPointData, $offset, $searchResultPerPage);
        $deliveryPoints = [];
        foreach($deliveryPointData as $delivery) {
            $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName());
        }
        echo json_encode($deliveryPoints);
    }

    if($q === 'statusTypes') {
        $statusTypeDataSet = new DeliveryStatusTypeDataSet();
        $statusTypeData = $statusTypeDataSet->fetchStatusTypes();
        $statusTypes = [];
        foreach($statusTypeData as $status) {
            $statusTypes[] = array("statusCode" => $status->getStatusCode(),"statusText" => $status->getStatusText());
        }
        echo json_encode($statusTypes);
    }
    if($q === 'deliverers') {
        $usersDataSet = new UserDataSet();
        $usersData = $usersDataSet->fetchAllUsers();
        $users = [];
        foreach($usersData as $user) {
            $users[] = array("id" => $user->getUserID(),"username" => $user->getUsername());
        }
        echo json_encode($users);
    }
}


