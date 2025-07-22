<?php

class Database { //Handles the connection to the database
    /**
     * @var Database
     */
    protected static $_dbInstance = null;

    /**
     * @var PDO
     */
    protected $_dbHandle;

    /**
     * @return Database
     */
    public static function getInstance() {
        $username = ''; //Storing the required details to make a db instance
        $password = ''; //Usually wouldn't be stored in a file like this for security reasons
        $host = '';
        $dbName = '';

        if(self::$_dbInstance === null) { //checks if the PDO exists
            // creates new instance PDO doesn't exist
            self::$_dbInstance = new self($username, $password, $host, $dbName);
        }

        return self::$_dbInstance; //Returns the instance
    }

    /**
     * @param $username
     * @param $password
     * @param $host
     * @param $database
     */
    private function __construct($username, $password, $host, $database) {
        try {
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database",  $username, $password); //Database connection
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return PDO
     */
    public function getdbConnection() {
        return $this->_dbHandle;
    }

    public function __destruct() {
        $this->_dbHandle = null;
    }
}
