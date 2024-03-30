<?php
require_once('./Models/DeliveryPointDataSet.php');

$q = $_REQUEST["q"];

if ($q !== "") {
    $deliveryPointDataSet = new DeliveryPointDataSet();
    echo json_encode($deliveryPointDataSet->fetchSearchDeliveries($q));

} else {
    echo 'No Suggestions';
}


