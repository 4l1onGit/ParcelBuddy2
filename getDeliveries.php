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

if ($q === "deliveries" && isset($_SESSION['login'])) {

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

if($q === "total" && isset($_SESSION['login'])) {
    if (strtolower($_SESSION['login']->getUserType()) === 'manager') {
        $deliveryPointData = $deliveryPointDataSet->fetchAllDeliveries();

    } else {
        $deliveryPointData = $deliveryPointDataSet->fetchUserDeliveries($_SESSION['login']->getId());
    }
    echo json_encode(count($deliveryPointData));
}
