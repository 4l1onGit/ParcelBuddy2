<?php
require_once('Database.php'); //Used for the Database connection
require_once('DeliveryStatusType.php'); //Used to create DeliveryStatusType objects when storing data

class DeliveryStatusTypeDataSet //Used for handling Delivery Status Type data
{
    protected $_dbHandle, $_dbInstance;

    public function __construct() //This constructor is responsible for handling a connection to the database
    { //Establish Connection to database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchStatusTypes() : array
    {
        $sqlQuery = 'SELECT * FROM delivery_status'; //SQL query stored in variable

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        $dataSet = [];

        while ($row = $statement->fetch()) { //Storing all records matching the SQL query
            $dataSet[] = new DeliveryStatusType($row); //Creating a DeliveryStatusType object when storing the records into the array
        }
        return $dataSet; //Returns the records (DeliveryStatusType objects)
    }


}