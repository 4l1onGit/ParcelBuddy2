<?php
require_once('UserType.php');
class UserData extends UserType
{
    protected $_id, $_username, $_password, $_userType, $_statusCode; //Stores user data

    public function __construct($dbRow)
    {
        $this->_id = $dbRow['UserID'];
        $this->_username = $dbRow['username'];
        $this->_password = $dbRow['password'];
        $this->_userType = $dbRow['UserType'];
        $this->_statusCode = $dbRow['status_code'];

    }

    public function getUserID() //Accessors for userdata fields
    {
        return $this->_id;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function getUserType()
    {
        return $this->_userType;
    }
}