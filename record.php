<?php
require_once('authenticate.php');
require_once("./Models/DeliveryPointDataSet.php");
require_once("./Models/DeliveryPoint.php");
require_once("./Models/UserType.php");
require_once("./Models/UserTypeDataSet.php");
require_once("./Models/DeliveryStatusTypeDataSet.php");


$view = new stdClass();
$view->pageTitle = 'Record';
$view->recordID = $_GET['id'];


require_once('./Views/record.phtml');

