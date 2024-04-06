<?php
require_once('./Models/DeliveryPointDataSet.php');

$q = $_REQUEST["q"];
$currentPage = $_GET['searchPage'] ?? 1; //Sets page to current page number
$searchPage = 1;
$searchResultPerPage = 10;

if ($q !== "") {
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


