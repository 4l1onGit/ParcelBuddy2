<?php
require_once('Database.php'); //Used for the Database connection
require_once('UserType.php'); //Used to create UserType objects when storing data

class UserTypeDataSet //Used for handling UserType data
{
    protected $_dbHandle, $_dbInstance;

    public function __construct() //This constructor is responsible for handling a connection to the database
    { //Establish Connection to database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchUserTypes() : array
    {
        $sqlQuery = 'SELECT * FROM delivery_usertype'; //SQL query stored in variable

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        $dataSet = [];

        while ($row = $statement->fetch()) { //Storing all records matching the SQL query
            $dataSet[] = new UserType($row); //Creating a UserType object when storing the records into the array
        }
        return $dataSet; //Returns the records (UserType objects)
    }


}