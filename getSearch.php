<?php
require_once('./Models/DeliveryPointDataSet.php');
require_once('./Models/DeliveryStatusTypeDataSet.php');
require_once('./Models/UserDataSet.php');
require_once('authenticate.php');

$q = $_REQUEST["q"];
$currentPage = $_GET['page'] ?? 1; //Sets page to current page number
$searchPage = 1;
$searchResultPerPage = 10;

if ($q !== "" && isset($_SESSION['login'])) {

    $deliveryPointDataSet = new DeliveryPointDataSet();
    $deliveryPointData = $deliveryPointDataSet->fetchSearchDeliveries($q);
    $totalItems = count($deliveryPointData);
    $searchCount = ceil($totalItems / $searchResultPerPage);
    $offset = ($searchPage -1) * $searchResultPerPage;
    $deliveryPointData = array_slice($deliveryPointData, $offset, $searchResultPerPage);
    $deliveryPoints = [];
    foreach($deliveryPointData as $delivery) {
        $deliveryPoints[] = array("id" => $delivery->getID(), "name" => $delivery->getName());
    }
    echo json_encode($deliveryPoints);
} else {
    echo 'No Suggestions';
}


