<?php
require_once('Database.php');
require_once('UserData.php');
class UserDataSet
{
    protected $_dbHandle, $_dbInstance;

    public function __construct()
    { //Establish Connection to database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function createUser($username, $password, $userType)
    {
        $sqlQuery = 'INSERT INTO delivery_users (username, password , UserType ) VALUES (?, ?, ?)'; //store SQL query to select all users in variable

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $username);
        $statement->bindParam(2, $password);
        $statement->bindParam(3, $userType);
        if ($statement->execute()) // execute the PDO statement
            return true;
        else
            return false;
    }

    public function fetchAllUsers(): array
    {
        $sqlQuery = 'SELECT * FROM delivery_users INNER JOIN delivery_usertype on delivery_users.UserType = delivery_usertype.id'; //store SQL query to select all users in variable

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        $dataSet = [];

        while ($row = $statement->fetch()) {
            $dataSet[] = new UserData($row);
        }
        return $dataSet;
    }

    public function fetchUser($username, $password)
    {
        $sqlQuery = 'SELECT * FROM delivery_users INNER JOIN delivery_usertype ON delivery_users.UserType = delivery_usertype.id WHERE username = ? AND password = ? ';
        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->bindParam(1, $username); //Binds variable to SQL query
        $statement->bindParam(2, $password);
        $statement->execute();  // execute the PDO statement
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new UserData($row);
        }

        if (count($dataSet) == 0) {
            return null;
        } else {
            return $dataSet;
        }
    }
}